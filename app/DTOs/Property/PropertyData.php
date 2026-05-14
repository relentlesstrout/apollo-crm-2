<?php

namespace App\DTOs\Property;

use App\Models\Customer;

readonly class PropertyData
{
    public function __construct(
        public string $house,
        public string $street,
        public ?string $area,
        public string $postcode,
        public ?string $notes,
    ) {}
}
