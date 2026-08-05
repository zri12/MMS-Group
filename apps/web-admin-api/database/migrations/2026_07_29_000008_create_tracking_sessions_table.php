<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->id();
            $table->char('local_uuid', 36)->nullable()->unique();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('marketing_schedules')->nullOnDelete();
            $table->date('session_date');
            $table->string('day_name', 20);
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->string('status', 30);
            $table->unsignedInteger('distance_meters')->nullable();
            $table->unsignedSmallInteger('visit_count');
            $table->timestamps();

            $table->index(['marketing_profile_id', 'session_date'], 'idx_tracking_sessions_marketing_date');
            $table->index('schedule_id', 'idx_tracking_sessions_schedule');
            $table->index('session_date', 'idx_tracking_sessions_date');
            $table->index('day_name', 'idx_tracking_sessions_day');
            $table->index('started_at', 'idx_tracking_sessions_started');
            $table->index('status', 'idx_tracking_sessions_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_sessions');
    }
};
