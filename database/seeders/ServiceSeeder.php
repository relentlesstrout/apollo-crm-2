<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private const array SERVICES = [
        ['name' => 'Window Clean', 'description' => 'Clean all accessible windows inside and out using streak-free solution.'],
        ['name' => 'Gutter Clean', 'description' => 'Clear gutters and downpipes of debris and flush through with water.'],
        ['name' => 'Conservatory Clean', 'description' => 'Clean conservatory roof panels, frames, and glass inside and out.'],
        ['name' => 'Fascia & Soffit Clean', 'description' => 'Wipe down and clean all fascias, soffits, and bargeboards.'],
        ['name' => 'Solar Panel Clean', 'description' => 'Clean solar panels using pure water fed pole system.'],
    ];

    public function run(): void
    {
        foreach (self::SERVICES as $service) {
           Service::create($service);
        }
    }
}
