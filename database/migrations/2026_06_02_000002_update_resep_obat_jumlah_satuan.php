<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN jumlah SMALLINT UNSIGNED NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN satuan ENUM('tablet','kaplet','kapsul','strip','tube','botol','-') NOT NULL DEFAULT '-'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN jumlah SMALLINT UNSIGNED NOT NULL DEFAULT 1");
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN satuan ENUM('tablet','kaplet','kapsul','strip','tube','botol') NOT NULL DEFAULT 'tablet'");
    }
};
