<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InsertDefaultUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['admin', 'user'] as $role) {
            Role::create(['name' => $role]);
            $user = User::create([
                'name' => ucfirst($role),
                'email' => $role === 'admin'
                    ? config('app.default_admin_email', str_replace('role', $role, 'role@role.net'))
                    : str_replace('role', $role, 'role@role.net'),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);

            $user->assignRole([$role]);

            if (filled(config('app.default_admin_email')) && $role === 'admin') {
                $user->sendFinalizationEmail();
            }
        }
    }
}
