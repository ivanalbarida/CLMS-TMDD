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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id(); // Ticket no.
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('requesting_office');
            $table->enum('request_type', ['Procurement', 'Repair', 'Condemnation', 'Other']);
            $table->string('title');
            $table->text('description'); // "Problem Encountered"
            $table->text('equipment_details')->nullable();
            $table->enum('classification', ['Simple', 'Complex', 'Unclassified'])->default('Unclassified');
            $table->text('action_taken')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('status_after_service')->nullable();
            $table->string('client_verifier_name')->nullable();
            $table->enum('status', ['Submitted', 'In Review', 'Approved', 'Rejected', 'In Progress', 'Completed', 'On Hold'])->default('Submitted');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
