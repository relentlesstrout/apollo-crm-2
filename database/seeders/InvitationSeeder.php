<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invitedBy = User::where('email', 'admin1@apollo.com')->firstOrFail();

        Invitation::factory()->create([
            'invited_by' => $invitedBy->id,
        ]);

        Invitation::factory()->count(2)
            ->expired()
            ->create();

        Invitation::factory()->count(2)
            ->accepted()
            ->create();

        Invitation::factory()->count(2);
    }
}
