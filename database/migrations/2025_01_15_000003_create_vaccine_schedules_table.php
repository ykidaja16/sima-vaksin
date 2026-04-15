<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccine_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('vaccine_id')->constrained('vaccines')->onDelete('cascade');
            $table->integer('dosis_ke')->index(); // 1, 2, 3, etc.
            $table->date('tanggal_vaksin')->index();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->index();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Critical indexes for reminder queries (H-7)
            $table->index(['status', 'tanggal_vaksin']);
            $table->index(['patient_id', 'status']);
            $table->index(['vaccine_id', 'dosis_ke']);
            
            // Composite index for the main reminder query
            $table->index(['status', 'tanggal_vaksin', 'patient_id', 'vaccine_id'],'idx_vaccine_schedule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccine_schedules');
    }
};
