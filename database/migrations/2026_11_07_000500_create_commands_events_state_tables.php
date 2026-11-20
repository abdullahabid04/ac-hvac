<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ac_commands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('command_type', 40)->default('set_state');
            $table->json('desired_state');
            $table->foreignId('ir_profile_version_id')->nullable()->constrained('ir_profile_versions')->nullOnDelete();
            $table->string('status', 30)->default('created');
            $table->timestamp('issued_at');
            $table->timestamp('expires_at');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('transmission_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('failure_code', 60)->nullable();
            $table->text('failure_message')->nullable();
            $table->string('idempotency_key', 100)->nullable();
            $table->uuid('correlation_id');
            $table->string('client_request_id', 100)->nullable();
            $table->json('device_result')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->timestamps();

            $table->unique(['organization_id', 'idempotency_key'], 'ac_commands_idempotency_unique');
            $table->index(['organization_id', 'ac_unit_id', 'issued_at']);
            $table->index(['device_id', 'status', 'expires_at']);
            $table->index(['organization_id', 'status']);
        });

        Schema::create('device_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_uid', 80);
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('command_id')->nullable();
            $table->string('event_type', 50);
            $table->string('source', 30)->default('device');
            $table->string('classification', 40)->nullable();
            $table->timestamp('device_occurred_at');
            $table->timestamp('server_received_at');
            $table->unsignedSmallInteger('schema_version')->default(1);
            $table->json('payload')->nullable();
            $table->string('processing_status', 20)->default('processed');
            $table->timestamps();

            $table->unique(['device_id', 'event_uid'], 'device_events_device_uid_unique');
            $table->index(['organization_id', 'ac_unit_id', 'device_occurred_at'], 'device_events_org_ac_occurred_idx');
            $table->index(['organization_id', 'event_type']);
            $table->index(['command_id']);
        });

        Schema::create('ac_state_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('power_state', 20)->default('unknown');
            $table->integer('temperature_setpoint_c')->nullable();
            $table->string('operating_mode', 30)->nullable();
            $table->string('fan_setting', 30)->nullable();
            $table->string('swing_setting', 30)->nullable();
            $table->string('source', 40)->nullable();
            $table->timestamp('observed_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->string('quality', 20)->default('unknown');
            $table->foreignId('ir_profile_version_id')->nullable()->constrained('ir_profile_versions')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ac_state_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->constrained()->cascadeOnDelete();
            $table->uuid('device_event_id')->nullable();
            $table->uuid('command_id')->nullable();
            $table->string('source', 40);
            $table->string('state_field', 40);
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->timestamp('observed_at');
            $table->timestamp('received_at');
            $table->string('quality', 20)->default('reported');
            $table->timestamps();

            $table->index(['organization_id', 'ac_unit_id', 'observed_at'], 'state_history_org_ac_observed_index');
            $table->index(['ac_unit_id', 'state_field', 'observed_at'], 'state_history_ac_field_observed_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ac_state_history');
        Schema::dropIfExists('ac_state_snapshots');
        Schema::dropIfExists('device_events');
        Schema::dropIfExists('ac_commands');
    }
};
