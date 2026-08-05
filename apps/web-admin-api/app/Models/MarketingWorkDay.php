<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayName;
use Database\Factories\MarketingWorkDayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingWorkDay extends Model
{
    /** @use HasFactory<MarketingWorkDayFactory> */
    use HasFactory;

    protected $fillable = [
        'marketing_profile_id',
        'day_name',
    ];

    protected function casts(): array
    {
        return [
            'day_name' => DayName::class,
        ];
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }
}
