<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This is what happens when you run `php artisan migrate`.
     */
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Step 1: Drop the old unique index.
            // Laravel automatically names unique indexes like: tablename_columnname_unique
            $table->dropUnique('equipment_tag_number_unique');

            // Step 2: Add the new composite unique index.
            // This says that the combination of tag_number and lab_id must be unique.
            $table->unique(['tag_number', 'lab_id']);
        });
    }

    /**
     * Reverse the migrations.
     * This is what happens if you ever need to roll back.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Revert the changes: drop the composite index...
            $table->dropUnique(['tag_number', 'lab_id']);

            // ...and re-add the original single-column unique index.
            $table->unique('tag_number', 'equipment_tag_number_unique');
        });
    }
};