<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin
        User::factory()->superadmin()->create([
            'name' => 'Renaut Mestdagh',
            'email' => 'renaut.mestdagh+superadmin@hotmail.com',
            'password' => Hash::make('123456789'),
        ]);

        // Use firstOrCreate to ensure roles exist in the database
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);

        // Create users and assign roles
        $organizations = Organization::all(); // Retrieve all organizations

        foreach ($organizations as $organization) {
            $orgId = $organization->id;

            // Create one admin
            $adminUser = User::factory()->create([
                'name' => "Admin Org{$orgId}",
                'email' => "renaut.mestdagh+admin{$orgId}@hotmail.com",
                'password' => Hash::make('123456789'),
                'organization_id' => $orgId,
            ]);
            $adminUser->assignRole($adminRole);

            // Create 1-3 admin users for each organization
            $adminCount = rand(1, 3);
            for ($i = 0; $i < $adminCount; $i++) {
                $adminUser = User::factory()->create([
                    'organization_id' => $organization->id,
                ]);
                $adminUser->assignRole($adminRole);
            }

            $organizerUser = User::factory()->create([
                'name' => "Organizer Org{$orgId}",
                'email' => "renaut.mestdagh+organizer{$orgId}@hotmail.com",
                'password' => Hash::make('123456789'),
                'organization_id' => $orgId,
            ]);
            $organizerUser->assignRole($organizerRole);

            // Create 0-3 organizer users for each organization
            $organizerCount = rand(0, 3);
            for ($i = 0; $i < $organizerCount; $i++) {
                $organizerUser = User::factory()->create([
                    'organization_id' => $organization->id,
                ]);
                $organizerUser->assignRole($organizerRole);
            }
        }
    }
}
