<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MemberApprovalStatus;
use App\Enums\SyncStatus;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'local_uuid',
        'marketing_profile_id',
        'source_prospect_id',
        'resort',
        'input_date',
        'input_time',
        'name',
        'member_number',
        'loan_number',
        'address',
        'phone',
        'business',
        'loan_amount',
        'installment_amount',
        'insurance_amount',
        'collateral',
        'approval_status',
        'member_photo_path',
        'latitude',
        'longitude',
        'location_address',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'input_date' => 'date',
            'loan_amount' => 'integer',
            'installment_amount' => 'integer',
            'insurance_amount' => 'integer',
            'approval_status' => MemberApprovalStatus::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'sync_status' => SyncStatus::class,
        ];
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }

    public function sourceProspect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class, 'source_prospect_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
