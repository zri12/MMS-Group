<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use Database\Factories\ProspectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    /** @use HasFactory<ProspectFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'local_uuid',
        'marketing_profile_id',
        'name',
        'phone',
        'address',
        'business',
        'status',
        'initial_visit_result',
        'notes',
        'resort',
        'input_date',
        'input_time',
        'latitude',
        'longitude',
        'location_address',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProspectStatus::class,
            'input_date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'sync_status' => SyncStatus::class,
        ];
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }

    public function visitReports(): HasMany
    {
        return $this->hasMany(VisitReport::class);
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'source_prospect_id');
    }
}
