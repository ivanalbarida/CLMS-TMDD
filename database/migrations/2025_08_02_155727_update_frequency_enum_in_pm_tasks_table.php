<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update existing data to prevent errors
        DB::table('pm_tasks')->where('frequency', 'Quarterly')->update(['frequency' => 'End of Term']);

        // Now, change the column definition
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->enum('frequency', ['Daily', 'Weekly', 'Monthly', 'End of Term', 'Annually'])->change();
        });
    }

    public function down(): void
    {
        // Define how to reverse the change
        DB::table('pm_tasks')->where('frequency', 'End of Term')->update(['frequency' => 'Quarterly']);
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->enum('frequency', ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annually'])->change();
        });
    }
};
