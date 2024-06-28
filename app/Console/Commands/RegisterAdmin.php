<?php

namespace App\Console\Commands;

use Filament\Commands\MakeUserCommand;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Console\Command;

class RegisterAdmin extends MakeUserCommand
{
    protected $signature = 'make:filament-user';
    protected $description = 'Create a new Admin with custom credentials';

    public function handle() : int
    {
        $first_name = $this->ask('First Name');
        $last_name = $this->ask('Last Name');
        $email = $this->ask('Email');
        $password = $this->secret('Password');

        $user = config('filament.models.User')::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($role);

        $this->info("Admin [{$email}] created successfully.");
        return static::SUCCESS;
    }
}
