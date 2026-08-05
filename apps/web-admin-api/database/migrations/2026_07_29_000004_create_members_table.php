<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->char('local_uuid', 36)->nullable()->unique();
            $table->foreignId('marketing_profile_id')->constrained()->restrictOnDelete();
            $table->foreignId('source_prospect_id')->nullable()->unique('uq_members_source_prospect')->constrained('prospects')->nullOnDelete();
            $table->string('resort', 100);
            $table->date('input_date');
            $table->time('input_time');
            $table->string('name', 150)->index('idx_members_name');
            $table->string('member_number', 50)->unique();
            $table->string('loan_number', 50)->unique();
            $table->text('address');
            $table->string('phone', 30)->index('idx_members_phone');
            $table->string('business', 150);
            $table->unsignedBigInteger('loan_amount');
            $table->unsignedBigInteger('installment_amount');
            $table->unsignedBigInteger('insurance_amount');
            $table->string('collateral');
            $table->string('approval_status', 30);
            $table->string('member_photo_path')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('sync_status', 30);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['marketing_profile_id', 'input_date'], 'idx_members_marketing_date');
            $table->index(['approval_status', 'resort'], 'idx_members_approval_resort');
            $table->index('input_date', 'idx_members_input_date');
            $table->index('sync_status', 'idx_members_sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
