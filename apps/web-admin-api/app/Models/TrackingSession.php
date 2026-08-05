<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayName;
use App\Enums\TrackingStatus;
use Database\Factories\TrackingSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrackingSession extends Model
{
    /** @use HasFactory<TrackingSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'local_uuid',
        'marketing_profile_id',
        'schedule_id',
        'session_date',
        'day_name',
        'started_at',
        'ended_at',
        'status',
        'distance_meters',
        'visit_count',
    ];

    protected function casts(): array
    {
        return [
            'day_name' => DayName::class,
            'session_date' => 'date',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'status' => TrackingStatus::class,
            'distance_meters' => 'integer',
            'visit_count' => 'integer',
        ];
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MarketingSchedule::class, 'schedule_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(TrackingPoint::class);
    }

    public function latestPoint(): HasOne
    {
        return $this->hasOne(TrackingPoint::class)->latestOfMany('recorded_at');
    }
}
