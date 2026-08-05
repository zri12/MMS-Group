<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OperationalRecapRowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalRecapRow extends Model
{
    /** @use HasFactory<OperationalRecapRowFactory> */
    use HasFactory;

    protected $fillable = [
        'operational_recap_id',
        'marketing_profile_id',
        'mg',
        'members_l',
        'members_m',
        'members_k',
        'members_s',
        'target_previous',
        'target_incoming',
        'target_outgoing',
        'target_s',
        'drop_previous',
        'drop_current',
        'drop_total',
        'storting_previous',
        'storting_current',
        'storting_total',
        'percentage',
        'previous_circulation',
        'current_circulation',
        'followed_by',
        'morning_cash',
    ];

    protected function casts(): array
    {
        return [
            'members_l' => 'integer',
            'members_m' => 'integer',
            'members_k' => 'integer',
            'members_s' => 'integer',
            'target_previous' => 'integer',
            'target_incoming' => 'integer',
            'target_outgoing' => 'integer',
            'target_s' => 'integer',
            'drop_previous' => 'integer',
            'drop_current' => 'integer',
            'drop_total' => 'integer',
            'storting_previous' => 'integer',
            'storting_current' => 'integer',
            'storting_total' => 'integer',
            'percentage' => 'decimal:2',
            'previous_circulation' => 'integer',
            'current_circulation' => 'integer',
            'morning_cash' => 'integer',
        ];
    }

    public function operationalRecap(): BelongsTo
    {
        return $this->belongsTo(OperationalRecap::class);
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }
}
