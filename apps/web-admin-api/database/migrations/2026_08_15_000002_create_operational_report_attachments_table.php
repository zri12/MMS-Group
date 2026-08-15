<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_report_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('daily_operational_report_id');
            $table->string('type', 30);
            $table->string('photo_path');
            $table->string('caption')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'uploaded_at'], 'idx_operational_attachments_type_uploaded');
            $table->foreign('daily_operational_report_id', 'fk_op_attachment_report')
                ->references('id')
                ->on('daily_operational_reports')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_report_attachments');
    }
};
