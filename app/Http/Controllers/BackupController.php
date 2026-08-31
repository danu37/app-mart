<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    /**
     * Menampilkan Halaman Backup & Restore beserta informasi database
     */
    public function index()
    {
        $databaseName = config('database.connections.mysql.database', env('DB_DATABASE', 'db_toko_online'));
        $tables = [];
        $totalRows = 0;

        try {
            $tableList = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            foreach ($tableList as $t) {
                $tableName = array_values((array) $t)[0];
                $rowCount = DB::table($tableName)->count();
                $totalRows += $rowCount;
                $tables[] = [
                    'name' => $tableName,
                    'rows' => $rowCount,
                ];
            }
        } catch (Exception $e) {
            // Database query fallback jika koneksi bermasalah
        }

        return Inertia::render('Backup/Index', [
            'dbInfo' => [
                'name' => $databaseName,
                'driver' => config('database.default', 'mysql'),
                'host' => config('database.connections.mysql.host', '127.0.0.1'),
                'totalTables' => count($tables),
                'totalRows' => $totalRows,
                'tables' => $tables,
            ]
        ]);
    }

    /**
     * Proses Backup Database (.sql)
     */
    public function backup()
    {
        $database = config('database.connections.mysql.database', env('DB_DATABASE', 'db_toko_online'));
        $user = config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = config('database.connections.mysql.password', env('DB_PASSWORD', ''));
        $host = config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port = config('database.connections.mysql.port', env('DB_PORT', '3306'));

        $filename = "backup-{$database}-" . date('Y-m-d_H-i-s') . ".sql";
        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);
        $storagePath = $directory . DIRECTORY_SEPARATOR . $filename;

        $backupSuccess = false;
        $errorMessage = '';

        // 1. Coba menggunakan binary mysqldump jika ditemukan
        $mysqldumpBinary = $this->findBinary('mysqldump');
        if ($mysqldumpBinary) {
            try {
                $args = [$mysqldumpBinary, '-h', $host, '-P', (string)$port, '-u', $user];
                if ($password !== '' && !is_null($password)) {
                    $args[] = "-p{$password}";
                }
                $args[] = '--routines';
                $args[] = '--triggers';
                $args[] = '--single-transaction';
                $args[] = $database;

                $process = new Process($args);
                $process->setTimeout(300); // 5 menit
                $process->run();

                if ($process->isSuccessful()) {
                    $output = $process->getOutput();
                    if (strlen(trim($output)) > 0) {
                        File::put($storagePath, $output);
                        $backupSuccess = true;
                    }
                } else {
                    $errorMessage = $process->getErrorOutput();
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }

        // 2. Fallback menggunakan PHP PDO jika mysqldump tidak ada atau gagal
        if (!$backupSuccess) {
            try {
                $this->dumpDatabaseViaPhp($storagePath, $database);
                $backupSuccess = true;
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }

        // Verifikasi file hasil backup
        if ($backupSuccess && File::exists($storagePath) && File::size($storagePath) > 0) {
            return response()->download($storagePath, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ])->deleteFileAfterSend(true);
        }

        // Jika gagal, hapus file 0 byte jika ada
        if (File::exists($storagePath)) {
            File::delete($storagePath);
        }

        return back()->with('error', 'Gagal membuat cadangan database: ' . ($errorMessage ?: 'Terjadi kesalahan sistem.'));
    }

    /**
     * Proses Restore Database (.sql)
     */
    public function restore(Request $request)
    {
        $request->validate([
            'file_sql' => [
                'required',
                'file',
                'max:102400', // Maksimal 100MB
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['sql', 'txt'])) {
                        $fail('File cadangan harus berekstensi .sql atau .txt.');
                    }
                },
            ],
        ], [
            'file_sql.required' => 'Silakan pilih file backup database (.sql).',
            'file_sql.file' => 'File yang diunggah tidak valid.',
            'file_sql.max' => 'Ukuran file cadangan maksimal 100MB.',
        ]);

        $file = $request->file('file_sql');
        $filePath = $file->getRealPath();

        $database = config('database.connections.mysql.database', env('DB_DATABASE', 'db_toko_online'));
        $user = config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = config('database.connections.mysql.password', env('DB_PASSWORD', ''));
        $host = config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port = config('database.connections.mysql.port', env('DB_PORT', '3306'));

        $restoreSuccess = false;
        $errorMessage = '';

        // 1. Coba menggunakan binary mysql CLI jika ditemukan
        $mysqlBinary = $this->findBinary('mysql');
        if ($mysqlBinary) {
            try {
                $args = [$mysqlBinary, '-h', $host, '-P', (string)$port, '-u', $user];
                if ($password !== '' && !is_null($password)) {
                    $args[] = "-p{$password}";
                }
                $args[] = $database;

                $process = new Process($args);
                $process->setTimeout(300);
                $fileHandle = fopen($filePath, 'r');
                if ($fileHandle) {
                    $process->setInput($fileHandle);
                    $process->run();
                    fclose($fileHandle);

                    if ($process->isSuccessful()) {
                        $restoreSuccess = true;
                    } else {
                        $errorMessage = $process->getErrorOutput();
                    }
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }

        // 2. Fallback menggunakan PHP PDO jika binary mysql CLI tidak ada atau gagal
        if (!$restoreSuccess) {
            try {
                $this->restoreDatabaseViaPhp($filePath);
                $restoreSuccess = true;
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }

        if ($restoreSuccess) {
            return back()->with('success', 'Database berhasil dipulihkan!');
        }

        return back()->with('error', 'Gagal memulihkan database: ' . ($errorMessage ?: 'Periksa kembali file SQL Anda.'));
    }

    /**
     * Mencari path binary (mysqldump / mysql) secara otomatis di berbagai lokasi sistem
     */
    private function findBinary(string $binaryName): ?string
    {
        // 1. Cek environment variable khusus
        $envKey = strtoupper($binaryName) . '_PATH';
        $customPath = env($envKey);
        if ($customPath && File::exists($customPath)) {
            return $customPath;
        }

        // 2. Cek lokasi instalasi umum di Windows (XAMPP, Laragon, WAMP, MySQL Server)
        $commonWindowsPaths = [
            'C:\\xampp\\mysql\\bin\\' . $binaryName . '.exe',
            'D:\\xampp\\mysql\\bin\\' . $binaryName . '.exe',
            'E:\\xampp\\mysql\\bin\\' . $binaryName . '.exe',
            'C:\\laragon\\bin\\mysql\\current\\bin\\' . $binaryName . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\' . $binaryName . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\' . $binaryName . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\' . $binaryName . '.exe',
        ];

        foreach ($commonWindowsPaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        // 3. Cek apakah binary tersedia langsung di PATH sistem
        try {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            $checkCmd = $isWindows ? ['where.exe', $binaryName] : ['which', $binaryName];
            $process = new Process($checkCmd);
            $process->run();
            if ($process->isSuccessful()) {
                $path = trim(explode("\n", trim($process->getOutput()))[0]);
                if (!empty($path) && ($isWindows ? File::exists($path) : is_executable($path))) {
                    return $path;
                }
            }
        } catch (Exception $e) {
            // Ignore error
        }

        return null;
    }

    /**
     * Fallback: Membuat file cadangan database murni melalui PHP PDO streaming
     */
    private function dumpDatabaseViaPhp(string $destinationPath, string $databaseName): void
    {
        $handle = fopen($destinationPath, 'w');
        if (!$handle) {
            throw new Exception("Tidak dapat membuka file tujuan untuk penulisan: {$destinationPath}");
        }

        $pdo = DB::connection()->getPdo();

        // Header SQL
        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, "-- Database Backup via PHP PDO\n");
        fwrite($handle, "-- Database: {$databaseName}\n");
        fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- --------------------------------------------------------\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

        $tables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");

        foreach ($tables as $tableObj) {
            $tableName = array_values((array) $tableObj)[0];

            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$tableName}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

            $createTableRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createTableSql = ((array) $createTableRes[0])['Create Table'] ?? '';
            fwrite($handle, $createTableSql . ";\n\n");

            $totalRows = DB::table($tableName)->count();
            if ($totalRows > 0) {
                fwrite($handle, "-- Dumping data for table `{$tableName}`\n");

                // Dump dalam chunk 200 baris untuk efisiensi memori
                $chunkSize = 200;
                $offset = 0;

                while ($offset < $totalRows) {
                    $rows = DB::table($tableName)->offset($offset)->limit($chunkSize)->get();
                    if ($rows->isEmpty()) {
                        break;
                    }

                    $insertPrefix = "INSERT INTO `{$tableName}` VALUES ";
                    $rowsFormatted = [];

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array) $row as $val) {
                            if (is_null($val)) {
                                $values[] = "NULL";
                            } elseif (is_numeric($val) && !is_string($val)) {
                                $values[] = $val;
                            } else {
                                $values[] = $pdo->quote($val);
                            }
                        }
                        $rowsFormatted[] = "(" . implode(", ", $values) . ")";
                    }

                    fwrite($handle, $insertPrefix . implode(",\n", $rowsFormatted) . ";\n");
                    $offset += $chunkSize;
                }

                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Fallback: Menjalankan pemulihan database murni melalui PHP PDO
     */
    private function restoreDatabaseViaPhp(string $filePath): void
    {
        $sqlContent = File::get($filePath);
        if (empty(trim($sqlContent))) {
            throw new Exception("File SQL kosong atau tidak dapat dibaca.");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            DB::unprepared($sqlContent);
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
