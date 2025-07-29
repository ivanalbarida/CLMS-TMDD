<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        DB::table('users')->whereIn('role', ['Custodian', 'Technician'])->update(['role' => 'Custodian/Technician']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Custodian/Technician'])->change();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Technician', 'Custodian'])->change();
        });
    }
};