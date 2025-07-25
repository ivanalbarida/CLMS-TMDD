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
    Schema::create('components', function (Blueprint $table) {
        $table->id();
        $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
        $table->enum('type', ['Monitor', 'OS', 'Processor', 'Motherboard', 'Memory', 'Storage', 'Video Card', 'PSU', 'Router', 'Switch', 'Other']);
        $table->string('description');
        $table->string('serial_number')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
