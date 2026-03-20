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
        $this->call([
            MemberSeeder::class,
            EventSeeder::class,
        ]);

        // Admin user (no member link)
        User::factory()->create([
            'name' => 'Admin FFGVA',
            'email' => 'admin@ffgva.ch',
            'password' => bcrypt('password'),
            'role' => 'A',
            'member_id' => null,
        ]);

        // Chef de peloton user linked to first member (Sophie Dupont)
        $firstMember = \App\Models\Member::first();
        User::factory()->create([
            'name' => 'Sophie Dupont',
            'email' => 'sophie.dupont@ffgva.ch',
            'password' => bcrypt('password'),
            'role' => 'C',
            'member_id' => $firstMember->id,
        ]);

        // Livia Wagner - admin
        User::factory()->create([
            'name' => 'Livia Wagner',
            'email' => 'livia.wagner@gmail.com',
            'password' => bcrypt('hydro2lique'),
            'role' => 'A',
            'member_id' => null,
        ]);
    }
}
