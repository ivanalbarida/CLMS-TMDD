<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->enum('type', ['Corrective', 'Preventive'])->default('Corrective')->after('id');
            $table->date('scheduled_for')->nullable()->after('date_reported');
        });
    }

    public function down(): void {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn(['type', 'scheduled_for']);
        });
    }
};