<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->enum('frequency', ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annually'])->change();
        });
    }
    public function down(): void {
        Schema::table('pm_tasks', function (Blueprint $table) {
            $table->enum('frequency', ['Daily', 'Weekly', 'Monthly', 'Annually'])->change();
        });
    }
};
