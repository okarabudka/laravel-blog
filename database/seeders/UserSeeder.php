<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public const string ADMIN_EMAIL = 'admin@example.com';

    public const int READERS = 5;

    // Local demo only; the README lists these logins.
    private const string PASSWORD = 'password';

    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => self::ADMIN_EMAIL],
            ['name' => 'Admin', 'password' => self::PASSWORD, 'email_verified_at' => now()],
        );
        $admin->assignRole(User::ADMIN_ROLE);

        $names = ['Ada Lovelace', 'Grace Hopper', 'Linus Larsen', 'Margaret Hamilton', 'Alan Turner'];

        foreach (range(1, self::READERS) as $n) {
            User::firstOrCreate(
                ['email' => "reader{$n}@example.com"],
                ['name' => $names[$n - 1], 'password' => self::PASSWORD, 'email_verified_at' => now()],
            );
        }
    }
}
