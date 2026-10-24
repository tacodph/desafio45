<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'sandro.devsis@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'email_verified_at' => now(),
                'user_type_id' => UserType::idFor(UserRole::Administrador),
            ],
        );
    }
}
