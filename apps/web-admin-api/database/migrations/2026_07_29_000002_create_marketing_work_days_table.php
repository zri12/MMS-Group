<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->string('day_name', 20)->index('idx_work_days_day');
            $table->timestamps();

            $table->unique(['marketing_profile_id', 'day_name'], 'uq_work_days_marketing_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_work_days');
    }
};
