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
        Schema::create('profile_software_item', function (Blueprint $table) {
            $table->foreignId('software_profile_id')->constrained('software_profiles')->onDelete('cascade');
            $table->foreignId('software_item_id')->constrained('software_items')->onDelete('cascade');
            $table->primary(['software_profile_id', 'software_item_id']); // Important for pivot tables
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_software_pivot');
    }
};
