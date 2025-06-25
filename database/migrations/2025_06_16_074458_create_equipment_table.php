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
    Schema::create('equipment', function (Blueprint $table) {
        $table->id();
        $table->string('tag_number')->unique();
        $table->foreignId('lab_id')->constrained('labs')->onDelete('cascade');
        $table->enum('status', ['Working', 'For Repair', 'In Use', 'Retired']);
        $table->text('notes')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
