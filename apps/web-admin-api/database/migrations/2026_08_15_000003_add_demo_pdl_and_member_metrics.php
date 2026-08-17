<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_profiles', function (Blueprint $table): void {
            $table->string('display_name', 150)->nullable()->after('user_id');
        });

        Schema::table('daily_operational_reports', function (Blueprint $table): void {
            $table->unsignedSmallInteger('incoming_member_count')->default(0)->after('total_target_people');
            $table->unsignedSmallInteger('outgoing_member_count')->default(0)->after('incoming_member_count');
        });
    }

    public function down(): void
    {
        Schema::table('daily_operational_reports', function (Blueprint $table): void {
            $table->dropColumn(['incoming_member_count', 'outgoing_member_count']);
        });

        Schema::table('marketing_profiles', function (Blueprint $table): void {
            $table->dropColumn('display_name');
        });
    }
};
