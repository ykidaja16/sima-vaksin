<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah')->default(1)->after('makan');
            $table->enum('satuan', ['tablet','kaplet','kapsul','strip','tube','botol'])->default('tablet')->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->dropColumn(['jumlah', 'satuan']);
        });
    }
};
