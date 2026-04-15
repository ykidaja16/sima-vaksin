<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang', 100)->unique();
            $table->string('kode_prefix', 10)->unique(); // LX, LZ, dll
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('kode_prefix');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
