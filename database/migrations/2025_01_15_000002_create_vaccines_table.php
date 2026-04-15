<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->unsignedBigInteger('vaccine_type_id');
            $table->date('tanggal_vaksin_pertama')->index();
            $table->timestamps();
            
            // Composite index for reminder queries
            $table->index(['vaccine_type_id', 'tanggal_vaksin_pertama']);
            $table->index(['patient_id', 'vaccine_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccines');
    }
};
