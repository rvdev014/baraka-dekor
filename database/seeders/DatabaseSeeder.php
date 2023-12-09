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
        if (!User::where('phone', '998935146492')->exists()) {
            User::factory()->create([
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => 'test@example.com',
                'phone' => '998935146492',
            ]);
        }

        if (!User::where('phone', '998935146491')->exists()) {
            User::factory()->create([
                'firstname' => 'Test',
                'lastname' => 'Admin',
                'email' => 'admin@example.com',
                'phone' => '998935146491',
                'password' => Hash::make(env('ADMIN_PASSWORD')),
                'is_admin' => true,
            ]);
        }
    }
}
