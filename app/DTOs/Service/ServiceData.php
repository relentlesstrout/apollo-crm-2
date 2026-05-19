<?php

namespace App\DTOs\Service;

readonly class ServiceData
{
    public function __construct(
        public string $name,
        public ?string $description,
    ) {}
}
