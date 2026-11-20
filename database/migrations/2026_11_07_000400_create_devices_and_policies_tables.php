<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('device_uid', 80)->unique();
            $table->string('name');
            $table->string('hardware_model', 80)->default('ESP32');
            $table->string('firmware_version', 40)->nullable();
            $table->string('status', 20)->default('active');
            $table->string('api_token_hash', 64)->nullable()->unique();
            $table->string('token_prefix', 12)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('offline_notified_at')->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->integer('wifi_rssi_dbm')->nullable();
            $table->string('effective_remote_policy', 20)->default('disabled');
            $table->unsignedInteger('policy_version')->default(1);
            $table->timestamp('policy_synced_at')->nullable();
            $table->json('local_config')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['last_seen_at', 'status']);
        });

        Schema::create('device_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();
            $table->boolean('is_current')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'ac_unit_id', 'is_current'], 'device_assignments_org_ac_current_idx');
            $table->index(['device_id', 'is_current']);
        });

        Schema::create('remote_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('scope_type', 30);
            $table->unsignedBigInteger('scope_id');
            $table->string('policy_value', 20)->default('inherit');
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['scope_type', 'scope_id'], 'remote_policies_scope_unique');
            $table->index(['organization_id', 'scope_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remote_policies');
        Schema::dropIfExists('device_assignments');
        Schema::dropIfExists('devices');
    }
};
