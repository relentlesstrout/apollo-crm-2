<?php

namespace App\Models;

use Database\Factories\PropertyServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyService extends Model
{
    /** @use HasFactory<PropertyServiceFactory> */
    use HasFactory;

    protected $table = 'property_service';

    protected $fillable = [
        'property_id',
        'service_id',
        'price',
        'description',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
