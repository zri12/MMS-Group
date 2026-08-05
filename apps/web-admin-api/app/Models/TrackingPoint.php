<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TrackingPointType;
use Database\Factories\TrackingPointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingPoint extends Model
{
    /** @use HasFactory<TrackingPointFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'local_uuid',
        'tracking_session_id',
        'latitude',
        'longitude',
        'accuracy_meters',
        'speed_mps',
        'heading',
        'altitude_meters',
        'address',
        'point_type',
        'recorded_at',
        'received_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy_meters' => 'decimal:2',
            'speed_mps' => 'decimal:2',
            'heading' => 'decimal:2',
            'altitude_meters' => 'decimal:2',
            'point_type' => TrackingPointType::class,
            'recorded_at' => 'datetime',
            'received_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function trackingSession(): BelongsTo
    {
        return $this->belongsTo(TrackingSession::class);
    }
}
