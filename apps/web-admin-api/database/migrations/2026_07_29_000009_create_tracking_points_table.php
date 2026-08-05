<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_points', function (Blueprint $table) {
            $table->id();
            $table->char('local_uuid', 36)->nullable()->unique();
            $table->foreignId('tracking_session_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('speed_mps', 8, 2)->nullable();
            $table->decimal('heading', 8, 2)->nullable();
            $table->decimal('altitude_meters', 10, 2)->nullable();
            $table->string('address')->nullable();
            $table->string('point_type', 30);
            $table->dateTime('recorded_at');
            $table->dateTime('received_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tracking_session_id', 'recorded_at'], 'idx_tracking_points_session_time');
            $table->index('point_type', 'idx_tracking_points_type');
            $table->index('recorded_at', 'idx_tracking_points_recorded');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_points');
    }
};
