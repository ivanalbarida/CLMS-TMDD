<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modify the 'role' column to include the new 'Custodian' option
            $table->enum('role', ['Admin', 'Technician', 'Custodian'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to the old options if we need to undo this migration
            $table->enum('role', ['Admin', 'Technician'])->change();
        });
    }
};