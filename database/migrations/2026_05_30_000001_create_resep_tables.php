<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->string('no_resep', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('nama_dokter', 100);
            $table->string('nama_pasien', 100);
            $table->unsignedTinyInteger('umur');
            $table->string('alamat', 255);
            $table->date('tanggal_resep');
            $table->timestamps();

            $table->index('tanggal_resep');
            $table->index('nama_pasien');
            $table->index('user_id');
        });

        Schema::create('resep_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('resep')->onDelete('cascade');
            $table->string('nama_obat', 100);
            $table->string('dosis', 20);
            $table->enum('waktu_minum', ['Pagi', 'Siang', 'Sore', 'Malam', '-'])->default('-');
            $table->timestamps();

            $table->index('resep_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_obat');
        Schema::dropIfExists('resep');
    }
};
