<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('serial_number', 80)->nullable()->unique()->after('device_uid');
            $table->string('command_representation', 30)->nullable()->after('serial_number');
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex('devices_serial_number_unique');
            $table->dropColumn('serial_number');
            $table->dropColumn('command_representation');
        });
    }
};
