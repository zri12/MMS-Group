<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_recaps', function (Blueprint $table) {
            $table->id();
            $table->string('report_number', 50)->unique();
            $table->date('recap_date')->unique();
            $table->string('day_name', 20);
            $table->string('status', 30);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('day_name', 'idx_operational_recaps_day');
            $table->index('status', 'idx_operational_recaps_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_recaps');
    }
};
