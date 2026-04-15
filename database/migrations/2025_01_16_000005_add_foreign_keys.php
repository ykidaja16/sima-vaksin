<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign key to patients table (branch_id)
        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('cascade');
        });

        // Add foreign key to vaccines table (vaccine_type_id)
        Schema::table('vaccines', function (Blueprint $table) {
            $table->foreign('vaccine_type_id')
                  ->references('id')
                  ->on('vaccine_types')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('vaccines', function (Blueprint $table) {
            $table->dropForeign(['vaccine_type_id']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
        });
    }
};
