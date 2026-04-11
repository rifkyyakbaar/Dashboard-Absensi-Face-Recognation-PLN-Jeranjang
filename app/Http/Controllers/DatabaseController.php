<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DatabaseController extends Controller
{
    public function backup()
    {
        // 1. Buat nama file backup yang unik (berdasarkan tanggal & jam)
        $filename = "backup_pln_" . date('Y-m-d_H-i-s') . ".sql";
        
        // 2. Tentukan lokasi penyimpanan sementara di server
        $path = storage_path('app/' . $filename);
        
        // 3. GANTI BAGIAN INI SESUAI DATABASE POSTGRESQL MILIKMU!
        $db_user = "postgres"; // username postgres-mu
        $db_pass = "password_kamu"; // password postgres-mu
        $db_name = "nama_database_pln"; // nama database-mu
        $db_host = "localhost";
        
        // 4. Perintah terminal untuk export PostgreSQL (pg_dump)
        $command = "PGPASSWORD='{$db_pass}' pg_dump -U {$db_user} -h {$db_host} {$db_name} > {$path}";
        
        // Eksekusi perintah di atas
        exec($command);
        
        // Cek apakah file berhasil dibuat
        if (file_exists($path)) {
            // Kirim file ke browser untuk didownload, lalu hapus filenya dari server agar tidak memenuhi memori
            return Response::download($path)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Gagal membuat backup database.');
        }
    }
}