<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand enum — tambahkan 'Sesuai Dosis' sementara '-' masih ada
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN waktu_minum ENUM('Pagi','Siang','Sore','Malam','-','Sesuai Dosis') NOT NULL DEFAULT '-'");
        // Step 2: migrasi data lama
        DB::table('resep_obat')->where('waktu_minum', '-')->update(['waktu_minum' => 'Sesuai Dosis']);
        // Step 3: hapus '-' dari enum dan set default baru
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN waktu_minum ENUM('Pagi','Siang','Sore','Malam','Sesuai Dosis') NOT NULL DEFAULT 'Sesuai Dosis'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN waktu_minum ENUM('Pagi','Siang','Sore','Malam','Sesuai Dosis','-') NOT NULL DEFAULT 'Sesuai Dosis'");
        DB::table('resep_obat')->where('waktu_minum', 'Sesuai Dosis')->update(['waktu_minum' => '-']);
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN waktu_minum ENUM('Pagi','Siang','Sore','Malam','-') NOT NULL DEFAULT '-'");
    }
};
