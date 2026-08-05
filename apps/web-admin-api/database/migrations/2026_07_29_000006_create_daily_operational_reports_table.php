<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_operational_reports', function (Blueprint $table) {
            $table->id();
            $table->char('local_uuid', 36)->nullable()->unique();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->date('report_date');
            $table->time('report_time');
            $table->string('day_name', 20);
            $table->string('resort', 100);
            $table->unsignedBigInteger('storting');
            $table->unsignedBigInteger('insurance_amount');
            $table->unsignedBigInteger('drop_amount');
            $table->unsignedBigInteger('withdrawal_saving');
            $table->unsignedBigInteger('previous_target_amount');
            $table->unsignedSmallInteger('previous_target_people');
            $table->unsignedBigInteger('incoming_target_amount');
            $table->unsignedSmallInteger('incoming_target_people');
            $table->unsignedBigInteger('outgoing_target_amount');
            $table->unsignedSmallInteger('outgoing_target_people');
            $table->unsignedBigInteger('total_target_amount');
            $table->unsignedSmallInteger('total_target_people');
            $table->unsignedBigInteger('new_drop');
            $table->unsignedBigInteger('continued_drop');
            $table->text('notes')->nullable();
            $table->string('sync_status', 30);
            $table->timestamps();

            $table->index(['marketing_profile_id', 'report_date'], 'idx_reports_marketing_date');
            $table->index('report_date', 'idx_reports_date');
            $table->index('day_name', 'idx_reports_day');
            $table->index('resort', 'idx_reports_resort');
            $table->index('sync_status', 'idx_reports_sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_operational_reports');
    }
};
