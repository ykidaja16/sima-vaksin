<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resep_obat ADD COLUMN makan ENUM('Sebelum Makan','Sesudah Makan','-') NOT NULL DEFAULT '-' AFTER waktu_minum");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resep_obat DROP COLUMN makan");
    }
};
