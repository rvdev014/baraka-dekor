<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('phone', '998999999999')->exists()) {
            User::factory()->create([
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => 'test@example.com',
                'phone' => '998999999999',
            ]);
        }

        if (!User::where('phone', env('ADMIN_PHONE'))->exists()) {
            User::factory()->create([
                'firstname' => 'Test',
                'lastname' => 'Admin',
                'phone' => env('ADMIN_PHONE'),
                'password' => Hash::make(env('ADMIN_PASSWORD')),
                'is_admin' => true,
            ]);
        }
    }
}
