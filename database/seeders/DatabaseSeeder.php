<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(DiscountCodeSeeder::class);
        $this->call(TicketTypeSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(TicketSeeder::class);
    }
}
