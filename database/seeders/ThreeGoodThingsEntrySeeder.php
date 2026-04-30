<?php

namespace Database\Seeders;

use App\Models\ThreeGoodThingsEntry;
use App\Models\User;
use Illuminate\Database\Seeder;

class ThreeGoodThingsEntrySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()
            ->where('role', User::ROLE_USER_PRO)
            ->first();

        if ($user === null) {
            return;
        }

        ThreeGoodThingsEntry::factory()
            ->count(5)
            ->for($user)
            ->sequence(
                fn ($sequence): array => ['entry_date' => now()->subDays($sequence->index)->toDateString()],
            )
            ->create();
    }
}
