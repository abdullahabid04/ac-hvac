<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ac_daily_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ac_unit_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date');
            $table->unsignedInteger('on_seconds')->default(0);
            $table->unsignedInteger('unknown_seconds')->default(0);
            $table->boolean('incomplete')->default(false);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['ac_unit_id', 'usage_date']);
            $table->index(['organization_id', 'usage_date']);
        });

        Schema::create('generated_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('report_type', 60);
            $table->string('title');
            $table->json('filters');
            $table->string('format', 10)->default('pdf');
            $table->string('status', 20)->default('queued');
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
        Schema::dropIfExists('ac_daily_usage');
    }
};
