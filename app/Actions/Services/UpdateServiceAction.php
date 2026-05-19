<?php

namespace App\Actions\Services;

use App\DTOs\Service\ServiceData;
use App\Models\Service;

class UpdateServiceAction
{
    public function execute(ServiceData $data, Service $service): void
    {
        $service->update([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }
}
