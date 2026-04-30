<?php

namespace App\DTOs\Invoice;

readonly class SendInvoiceData
{
    public function __construct(
        public int $userId,
        public string $description = 'Window Cleaning Service',
        public int $amount = 5000,
        public string $currency = 'gbp',
    ) {}
}
