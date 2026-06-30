<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protokol_abi', function (Blueprint $table) {
            $table->id();
            $table->string('no_protokol', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('nama_dokter', 100);
            $table->string('nama_pasien', 100);
            $table->unsignedTinyInteger('umur');
            $table->string('alamat', 255);
            $table->date('tanggal_pemeriksaan');

            $table->unsignedSmallInteger('right_arm_sistolik');
            $table->unsignedSmallInteger('right_arm_diastolik');
            $table->unsignedSmallInteger('right_arm_mean');
            $table->unsignedSmallInteger('left_arm_sistolik');
            $table->unsignedSmallInteger('left_arm_diastolik');
            $table->unsignedSmallInteger('left_arm_mean');
            $table->unsignedSmallInteger('right_ankle_sistolik');
            $table->unsignedSmallInteger('right_ankle_diastolik');
            $table->unsignedSmallInteger('right_ankle_mean');
            $table->unsignedSmallInteger('left_ankle_sistolik');
            $table->unsignedSmallInteger('left_ankle_diastolik');
            $table->unsignedSmallInteger('left_ankle_mean');

            $table->unsignedSmallInteger('highest_brachial_sistolik');
            $table->unsignedSmallInteger('abi_left_pembilang');
            $table->unsignedSmallInteger('abi_left_penyebut');
            $table->decimal('abi_left_hasil', 4, 2);
            $table->unsignedSmallInteger('abi_right_pembilang');
            $table->unsignedSmallInteger('abi_right_penyebut');
            $table->decimal('abi_right_hasil', 4, 2);

            $table->timestamps();

            $table->index('tanggal_pemeriksaan');
            $table->index('nama_pasien');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protokol_abi');
    }
};
