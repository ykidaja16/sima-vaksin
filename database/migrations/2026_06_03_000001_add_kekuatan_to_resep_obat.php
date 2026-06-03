<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->string('kekuatan', 10)->nullable()->after('nama_obat');
            $table->enum('satuan_kekuatan', ['mg', 'ml', '%'])->default('mg')->after('kekuatan');
        });
    }

    public function down(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->dropColumn(['kekuatan', 'satuan_kekuatan']);
        });
    }
};
