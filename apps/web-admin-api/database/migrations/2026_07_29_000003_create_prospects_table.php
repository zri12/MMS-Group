<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->char('local_uuid', 36)->nullable()->unique();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->string('name', 150)->index('idx_prospects_name');
            $table->string('phone', 30)->index('idx_prospects_phone');
            $table->text('address');
            $table->string('business', 150);
            $table->string('status', 30);
            $table->text('initial_visit_result');
            $table->text('notes')->nullable();
            $table->string('resort', 100);
            $table->date('input_date');
            $table->time('input_time');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->string('sync_status', 30);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['marketing_profile_id', 'input_date'], 'idx_prospects_marketing_date');
            $table->index(['status', 'resort'], 'idx_prospects_status_resort');
            $table->index('input_date', 'idx_prospects_input_date');
            $table->index('sync_status', 'idx_prospects_sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
