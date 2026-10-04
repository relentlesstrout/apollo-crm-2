<?php

namespace App\Models;

use App\Enums\CleaningJobStatus;
use Database\Factories\CleaningJobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $property_id
 * @property CleaningJobStatus $status
 * @property string|null $notes
 * @property Carbon $scheduled_at
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property int|null $invoice_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Property $property
 *
 * @method static \Database\Factories\CleaningJobFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class CleaningJob extends Model
{
    /** @use HasFactory<CleaningJobFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'status',
        'notes',
        'scheduled_at',
        'started_at',
        'completed_at',
        'invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => CleaningJobStatus::class,
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withTrashed()
            ->withPivot(['price', 'actual_price'])
            ->withTimestamps();
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class)->withTimestamps();
    }

    public function cleaners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
