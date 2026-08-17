<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayName;
use App\Enums\SyncStatus;
use Database\Factories\DailyOperationalReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyOperationalReport extends Model
{
    /** @use HasFactory<DailyOperationalReportFactory> */
    use HasFactory;

    protected $fillable = [
        'local_uuid',
        'marketing_profile_id',
        'report_date',
        'report_time',
        'day_name',
        'resort',
        'storting',
        'insurance_amount',
        'drop_amount',
        'withdrawal_saving',
        'previous_target_amount',
        'previous_target_people',
        'incoming_target_amount',
        'incoming_target_people',
        'outgoing_target_amount',
        'outgoing_target_people',
        'total_target_amount',
        'total_target_people',
        'incoming_member_count',
        'outgoing_member_count',
        'new_drop',
        'continued_drop',
        'notes',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'day_name' => DayName::class,
            'report_date' => 'date',
            'storting' => 'integer',
            'insurance_amount' => 'integer',
            'drop_amount' => 'integer',
            'withdrawal_saving' => 'integer',
            'previous_target_amount' => 'integer',
            'previous_target_people' => 'integer',
            'incoming_target_amount' => 'integer',
            'incoming_target_people' => 'integer',
            'outgoing_target_amount' => 'integer',
            'outgoing_target_people' => 'integer',
            'total_target_amount' => 'integer',
            'total_target_people' => 'integer',
            'incoming_member_count' => 'integer',
            'outgoing_member_count' => 'integer',
            'new_drop' => 'integer',
            'continued_drop' => 'integer',
            'sync_status' => SyncStatus::class,
        ];
    }

    public function marketingProfile(): BelongsTo
    {
        return $this->belongsTo(MarketingProfile::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OperationalReportAttachment::class);
    }
}
