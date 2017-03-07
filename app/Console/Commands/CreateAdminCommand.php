<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
    {username : The username you want to create.}
    {email : User\'s email.}
    {password : User\'s password.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin account.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (User::where('username', $name = $this->argument('username'))->count('id')) {
            $this->error('Username already exists!');
            return;
        }
        if (User::where('email', $email = $this->argument('email'))->count('id')) {
            $this->error('Email already exists!');
            return;
        }
        $user = User::create([
            'username' => $this->argument('username'),
            'email' => $this->argument('email'),
            'password' => Hash::make($password = $this->argument('password')),
            'is_super' => true
        ]);

        // Add superadmin permission.
        $superAdminRole = \App\Role::where('name', 'superadmin')->first();
        $user->roles()->attach($superAdminRole->id);

        if ($user instanceof User) {
            $this->info(
                "Create super admin successful! \n"
                . "Username: {$name}\n"
                . "Email: {$email}\n"
                . "Password: {$password}\n"
            );
        }
    }
}
