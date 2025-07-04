<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('software_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('program_name'); // e.g., "Civil Engineering"
            $table->string('year_and_sem'); // e.g., "1st Year, 1st Semester"
            $table->string('software_name');
            $table->string('version')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Who last updated it
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('software_checklists');
    }
};