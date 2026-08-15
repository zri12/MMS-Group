<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MarketingProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketingProfile extends Model
{
    /** @use HasFactory<MarketingProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'code',
        'phone',
        'area',
        'profile_photo_path',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(function (User $user, MarketingProfile $profile): void {
            $user->name = $profile->display_name ?? $profile->code;
            $user->username = '';
            $user->is_active = false;
        });
    }

    public function workDays(): HasMany
    {
        return $this->hasMany(MarketingWorkDay::class);
    }

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(MarketingSchedule::class);
    }

    public function dailyOperationalReports(): HasMany
    {
        return $this->hasMany(DailyOperationalReport::class);
    }

    public function visitReports(): HasMany
    {
        return $this->hasMany(VisitReport::class);
    }

    public function trackingSessions(): HasMany
    {
        return $this->hasMany(TrackingSession::class);
    }

    public function latestTrackingSession(): HasOne
    {
        return $this->hasOne(TrackingSession::class)->latestOfMany('started_at');
    }

    public function operationalRecapRows(): HasMany
    {
        return $this->hasMany(OperationalRecapRow::class);
    }
}
