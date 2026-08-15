<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OperationalAttachmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalReportAttachment extends Model
{
    protected $fillable = [
        'daily_operational_report_id',
        'type',
        'photo_path',
        'caption',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => OperationalAttachmentType::class,
            'uploaded_at' => 'datetime',
        ];
    }

    public function dailyOperationalReport(): BelongsTo
    {
        return $this->belongsTo(DailyOperationalReport::class);
    }
}
