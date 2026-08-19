<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operational_report_attachments', function (Blueprint $table): void {
            $table->unique(
                ['daily_operational_report_id', 'type'],
                'op_attachment_report_type_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('operational_report_attachments', function (Blueprint $table): void {
            $table->dropUnique('op_attachment_report_type_unique');
        });
    }
};
