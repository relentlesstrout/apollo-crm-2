<?php

namespace App\DTOs\PropertyService;

readonly class PropertyServiceData
{
    public function __construct(
        public int $serviceId,
        public int $price,
        public ?string $description,
        public string $effectiveFrom,
        public ?string $effectiveTo,
    ) {}
}
