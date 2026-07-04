<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $house
 * @property string $street
 * @property string|null $area
 * @property string $postcode
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $notes
 * @property PropertyStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer $customer
 *
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property query()
 *
 * @mixin \Eloquent
 */
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'house',
        'street',
        'area',
        'postcode',
        'latitude',
        'longitude',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => PropertyStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function propertyServices(): HasMany
    {
        return $this->hasMany(PropertyService::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function cleaningJobs(): HasMany
    {
        return $this->hasMany(CleaningJob::class);
    }
}
