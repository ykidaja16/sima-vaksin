<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('pid', 50)->index();
            $table->string('nama_pasien', 100)->index();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->date('dob')->nullable();
            $table->timestamps();
            
            // Composite unique untuk PID per cabang
            $table->unique(['branch_id', 'pid']);
            
            // Composite index for common queries
            $table->index(['pid', 'nama_pasien']);
            $table->index(['branch_id', 'nama_pasien']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
