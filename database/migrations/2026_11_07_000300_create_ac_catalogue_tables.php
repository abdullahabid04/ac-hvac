<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ac_models', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('model_name');
            $table->string('remote_variant', 80)->default('standard');
            $table->string('protocol_name', 80);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['manufacturer', 'model_name', 'remote_variant'], 'ac_models_identity_unique');
        });

        Schema::create('ir_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ac_model_id')->constrained('ac_models')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index(['ac_model_id', 'status']);
        });

        Schema::create('ir_profile_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ir_profile_id')->constrained('ir_profiles')->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->json('protocol_metadata')->nullable();
            $table->json('capabilities');
            $table->integer('temperature_min_c')->default(16);
            $table->integer('temperature_max_c')->default(30);
            $table->decimal('temperature_step_c', 4, 1)->default(1);
            $table->string('validation_status', 20)->default('unvalidated');
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->unique(['ir_profile_id', 'version_number']);
        });

        Schema::create('ir_profile_payloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ir_profile_version_id')->constrained('ir_profile_versions')->cascadeOnDelete();
            $table->string('action_key');
            $table->string('representation_type', 30);
            $table->json('payload');
            $table->unsignedInteger('carrier_frequency_hz')->nullable();
            $table->json('repeat_metadata')->nullable();
            $table->timestamps();

            $table->index(['ir_profile_version_id', 'action_key']);
        });

        Schema::create('ac_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('space_id')->constrained()->restrictOnDelete();
            $table->foreignId('ac_model_id')->nullable()->constrained('ac_models')->nullOnDelete();
            $table->foreignId('ir_profile_version_id')->nullable()->constrained('ir_profile_versions')->nullOnDelete();
            $table->string('name');
            $table->string('asset_code', 60);
            $table->string('serial_number', 80)->nullable();
            $table->date('installation_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'asset_code']);
            $table->index(['organization_id', 'space_id']);
            $table->index(['organization_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ac_units');
        Schema::dropIfExists('ir_profile_payloads');
        Schema::dropIfExists('ir_profile_versions');
        Schema::dropIfExists('ir_profiles');
        Schema::dropIfExists('ac_models');
    }
};
