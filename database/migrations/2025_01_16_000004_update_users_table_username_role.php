<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username column if not exists
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->unique()->after('id');
            }
            
            // Add role_id column if not exists
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->after('username');
            }
        });

        // Drop email columns if exist (outside the main Schema::table block)
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email')) {
                $table->dropUnique(['email']);
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });

        // Drop role enum if exists
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore email columns
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique()->after('name');
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            
            // Restore role enum
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['it', 'admin'])->default('admin')->after('password');
                $table->index('role');
            }
            
            // Drop role_id
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
            
            // Drop username
            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }
        });
    }
};
