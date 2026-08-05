<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->foreignId('prospect_id')->nullable()->constrained('prospects')->nullOnDelete();
            $table->string('day_name', 20);
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('consumer_name_snapshot', 150)->nullable();
            $table->string('agenda');
            $table->string('area', 100);
            $table->string('resort', 100)->nullable();
            $table->string('destination')->nullable();
            $table->text('note')->nullable();
            $table->string('status', 30);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['marketing_profile_id', 'schedule_date'], 'idx_schedules_marketing_date');
            $table->index(['status', 'schedule_date'], 'idx_schedules_status_date');
            $table->index('day_name', 'idx_schedules_day');
            $table->index('area', 'idx_schedules_area');
            $table->index('resort', 'idx_schedules_resort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_schedules');
    }
};
