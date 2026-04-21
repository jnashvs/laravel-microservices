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
        // Create Test User
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => \Hash::make('12345678'),
            ]
        );

        // Ensure Passport client exists with ID and Secret from .env
        $clientId = config('services.passport.client_id');
        $clientSecret = config('services.passport.client_secret');

        if ($clientId && $clientSecret) {
            \DB::table('oauth_clients')->updateOrInsert(
                ['id' => $clientId],
                [
                    'name' => 'Password Grant Client',
                    'secret' => \Hash::make($clientSecret),
                    'provider' => 'users',
                    'redirect_uris' => '["http://localhost"]',
                    'grant_types' => '["password","refresh_token"]',
                    'revoked' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
