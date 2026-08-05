<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_recap_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_recap_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->string('mg', 20);
            $table->unsignedInteger('members_l');
            $table->unsignedInteger('members_m');
            $table->unsignedInteger('members_k');
            $table->unsignedInteger('members_s');
            $table->unsignedBigInteger('target_previous');
            $table->unsignedBigInteger('target_incoming');
            $table->unsignedBigInteger('target_outgoing');
            $table->unsignedBigInteger('target_s');
            $table->unsignedBigInteger('drop_previous');
            $table->unsignedBigInteger('drop_current');
            $table->unsignedBigInteger('drop_total');
            $table->unsignedBigInteger('storting_previous');
            $table->unsignedBigInteger('storting_current');
            $table->unsignedBigInteger('storting_total');
            $table->decimal('percentage', 7, 2)->nullable();
            $table->unsignedBigInteger('previous_circulation');
            $table->unsignedBigInteger('current_circulation');
            $table->string('followed_by', 150);
            $table->unsignedBigInteger('morning_cash');
            $table->timestamps();

            $table->unique(['operational_recap_id', 'marketing_profile_id'], 'uq_recap_marketing');
            $table->index('marketing_profile_id', 'idx_recap_rows_marketing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_recap_rows');
    }
};
