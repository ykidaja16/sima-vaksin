<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccine_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vaksin', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->integer('total_dosis')->default(1);
            $table->json('interval_bulan'); // [0, 2, 6] untuk HPV, [0, 1, 6] untuk Hepatitis, dll
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccine_types');
    }
};
