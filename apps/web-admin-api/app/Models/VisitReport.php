<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Enums\VisitResult;
use Database\Factories\VisitReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitReport extends Model
{
    /** @use HasFactory<VisitReportFactory> */
    use HasFactory;

    protected $fillable = [
        'local_uuid',
        'prospect_id',
        'marketing_profile_id',
        'visit_date',
        'visit_time',
        'day_name',
        'visit_purpose',
        'visit_result',
        'prospect_status',
        'notes',
        'follow_up_date',
        'photo_path',
        'photo_caption',
        'resort',
        'latitude',
        'longitude',
        'location_address',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'day_name' => DayName::class,
            'visit_date' => 'date',
            'visit_result' => VisitResult::class,
            'prospect_status' => ProspectStatus::class,
            'follow_up_date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'sync_status' => SyncStatus::class,
        ];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }
}
