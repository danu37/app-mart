<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupRestoreTest extends TestCase
{
    public function test_backup_restore_page_can_be_rendered(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('backup.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Backup/Index')
            ->has('dbInfo.name')
            ->has('dbInfo.totalTables')
            ->has('dbInfo.totalRows')
        );
    }

    public function test_backup_can_be_downloaded(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get(route('backup.download'));

        $response->assertStatus(200);
        $this->assertTrue(
            $response->headers->get('content-type') === 'application/sql' || 
            str_contains((string)$response->headers->get('content-disposition'), '.sql')
        );
    }

    public function test_restore_can_be_performed_successfully(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Buat file SQL valid untuk tes restore
        $sqlContent = "-- Test Backup\n";
        $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sqlContent .= "SELECT 1;\n";
        $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $tempFile = storage_path('app/test_restore_' . uniqid() . '.sql');
        File::put($tempFile, $sqlContent);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_backup.sql',
            'application/sql',
            null,
            true
        );

        $response = $this->actingAs($user)->post(route('backup.upload'), [
            'file_sql' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        if (File::exists($tempFile)) {
            File::delete($tempFile);
        }
    }

    public function test_restore_rejects_invalid_file_extension(): void
    {
        $user = User::first() ?? User::factory()->create();

        $tempFile = storage_path('app/test_invalid_' . uniqid() . '.pdf');
        File::put($tempFile, 'Dummy content');

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_invalid.pdf',
            'application/pdf',
            null,
            true
        );

        $response = $this->actingAs($user)->post(route('backup.upload'), [
            'file_sql' => $uploadedFile,
        ]);

        $response->assertSessionHasErrors(['file_sql']);

        if (File::exists($tempFile)) {
            File::delete($tempFile);
        }
    }
}
