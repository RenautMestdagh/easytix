<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined organizations
        Organization::create([
            'name' => "Kompass Klub",
            'subdomain' => "kompass"
        ]);

        Organization::create([
            'name' => "Modul'air",
            'subdomain' => "modulair"
        ]);

        // Create additional random organizations
        Organization::factory()
            ->count(5)
            ->create();
    }
}
