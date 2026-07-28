<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@prads.kit',
        ]);

        $superAdmin->assignRole(Role::SUPER_ADMIN->value);

        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@prads.kit',
        ]);

        $admin->assignRole(Role::ADMIN->value);

        $editor = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@prads.kit',
        ]);

        $editor->assignRole(Role::EDITOR->value);

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@prads.kit',
        ]);

        $user->assignRole(Role::USER->value);
    }
}
