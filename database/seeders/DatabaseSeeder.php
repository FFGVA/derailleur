<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin FFGVA',
            'email' => 'admin@ffgva.ch',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            MemberSeeder::class,
            EventSeeder::class,
        ]);
    }
}
