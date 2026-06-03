<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN satuan_kekuatan ENUM('-','mg','ml','%') NOT NULL DEFAULT 'mg'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resep_obat MODIFY COLUMN satuan_kekuatan ENUM('mg','ml','%') NOT NULL DEFAULT 'mg'");
    }
};
