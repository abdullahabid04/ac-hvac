<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 30);
            $table->string('action', 60);
            $table->string('description');
            $table->string('result', 20)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('command_id')->nullable();
            $table->uuid('device_event_id')->nullable();
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'occurred_at']);
            $table->index(['organization_id', 'source']);
            $table->index(['ac_unit_id', 'occurred_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('action', 60);
            $table->string('entity_type', 80);
            $table->string('entity_id', 80);
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamp('occurred_at');
            $table->string('request_id', 80)->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'occurred_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 60);
            $table->string('severity', 20)->default('info');
            $table->string('title');
            $table->text('message');
            $table->foreignId('ac_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('device_event_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['organization_id', 'read_at', 'occurred_at']);
            $table->index(['organization_id', 'severity']);
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('key', 60);
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('activity_logs');
    }
};
