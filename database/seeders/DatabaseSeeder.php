<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            OrganizationSeeder::class,
            VenueSeeder::class,
            UserSeeder::class,
            EventSeeder::class,
            DiscountCodeSeeder::class,
            TicketTypeSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
