<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use Database\Factories\MarketingScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingSchedule extends Model
{
    /** @use HasFactory<MarketingScheduleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'marketing_profile_id',
        'prospect_id',
        'day_name',
        'schedule_date',
        'start_time',
        'end_time',
        'consumer_name_snapshot',
        'agenda',
        'area',
        'resort',
        'destination',
        'note',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'day_name' => DayName::class,
            'schedule_date' => 'date',
            'status' => ScheduleStatus::class,
        ];
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trackingSessions(): HasMany
    {
        return $this->hasMany(TrackingSession::class, 'schedule_id');
    }
}
