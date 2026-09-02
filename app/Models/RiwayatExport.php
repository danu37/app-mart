<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatExport extends Model
{
    protected $fillable = ['nama_file', 'format', 'status', 'file_url'];
}
