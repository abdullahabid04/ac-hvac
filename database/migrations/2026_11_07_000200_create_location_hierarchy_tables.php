<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 40)->nullable();
            $table->string('address')->nullable();
            $table->string('timezone', 64)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['organization_id', 'name']);
            $table->unique(['organization_id', 'code']);
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 40)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['organization_id', 'site_id']);
            $table->unique(['site_id', 'code']);
        });

        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('floor_number')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['organization_id', 'building_id']);
        });

        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_space_id')->nullable()->constrained('spaces')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 40)->nullable();
            $table->string('space_type', 30)->default('room');
            $table->string('status', 20)->default('active');
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'floor_id']);
            $table->index(['floor_id', 'parent_space_id']);
            $table->unique(['floor_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spaces');
        Schema::dropIfExists('floors');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('sites');
    }
};
