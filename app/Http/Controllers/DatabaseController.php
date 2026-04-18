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
        
        // 3.  KREDENSIAL 
        $db_user = env('DB_USERNAME');
        $db_pass = env('DB_PASSWORD');
        $db_name = env('DB_DATABASE');
        $db_host = env('DB_HOST');
        
        $command = "PGPASSWORD='{$db_pass}' pg_dump -U {$db_user} -h {$db_host} {$db_name} > {$path}";
        
        exec($command);
        
        if (file_exists($path)) {

            return Response::download($path)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Gagal membuat backup database.');
        }
    }
}