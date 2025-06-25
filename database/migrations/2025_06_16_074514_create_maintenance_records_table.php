<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('maintenance_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users');
        $table->text('issue_description');
        $table->text('action_taken')->nullable();
        $table->date('date_reported');
        $table->enum('status', ['Pending', 'In Progress', 'Completed']);
        $table->date('date_completed')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
