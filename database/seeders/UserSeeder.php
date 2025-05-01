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
        User::factory()->superadmin()->create([
            'name' => 'Renaut Mestdagh',
            'email' => 'renaut.mestdagh@hotmail.com',
            'password' => Hash::make('12345678'),
        ]);

        // Use firstOrCreate to ensure roles exist in the database
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);

        // Create users and assign roles
        $organizations = Organization::all(); // Retrieve all organizations

        foreach ($organizations as $organization) {
            // Create 1 admin user for each organization
            $adminUser = User::factory()->create([
                'organization_id' => $organization->id,
            ]);
            $adminUser->assignRole($adminRole); // Assign admin role

            // Create 2 organizer users for each organization
            for ($i = 0; $i < 2; $i++) {
                $organizerUser = User::factory()->create([
                    'organization_id' => $organization->id,
                ]);
                $organizerUser->assignRole($organizerRole); // Assign organizer role
            }
        }
    }
}
