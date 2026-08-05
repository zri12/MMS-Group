<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->char('local_uuid', 36)->nullable()->unique();
            $table->foreignId('prospect_id')->constrained('prospects')->restrictOnDelete();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->date('visit_date');
            $table->time('visit_time');
            $table->string('day_name', 20);
            $table->string('visit_purpose');
            $table->string('visit_result', 50);
            $table->string('prospect_status', 50);
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('photo_caption')->nullable();
            $table->string('resort', 100);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->string('sync_status', 30);
            $table->timestamps();

            $table->index('prospect_id', 'idx_visit_reports_prospect');
            $table->index(['marketing_profile_id', 'visit_date'], 'idx_visit_reports_marketing_date');
            $table->index(['visit_result', 'prospect_status'], 'idx_visit_reports_result_status');
            $table->index('follow_up_date', 'idx_visit_reports_follow_up');
            $table->index('day_name', 'idx_visit_reports_day');
            $table->index('resort', 'idx_visit_reports_resort');
            $table->index('sync_status', 'idx_visit_reports_sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_reports');
    }
};
