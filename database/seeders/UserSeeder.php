<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::factory()->admin()->create([
            'name' => 'Renaut Mestdagh',
            'email' => 'renaut.mestdagh@hotmail.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
