<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('service_requests', function (Blueprint $table) {
            // New, complete list of statuses
            $table->enum('status', ['Submitted', 'In Review', 'Approved', 'Rejected', 'In Progress', 'Completed', 'On Hold'])->default('Submitted')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('service_requests', function (Blueprint $table) {
            // The old list, to allow for rollbacks
            $table->enum('status', ['Submitted', 'In Progress', 'Completed', 'On Hold'])->default('Submitted')->change();
        });
    }
};
