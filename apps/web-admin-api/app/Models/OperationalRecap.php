<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DayName;
use Database\Factories\OperationalRecapFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationalRecap extends Model
{
    /** @use HasFactory<OperationalRecapFactory> */
    use HasFactory;

    protected $fillable = [
        'report_number',
        'recap_date',
        'day_name',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'day_name' => DayName::class,
            'recap_date' => 'date',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(OperationalRecapRow::class);
    }
}
