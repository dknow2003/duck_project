<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ChangeAdminEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:email
    {username : The username that you want to change email.}
    {email : New email.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To change a super admin\'s email.';

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
        if (!$admin = User::where('username', $name = $this->argument('username'))
                          ->where('is_super', 1)
                          ->first()
        ) {
            $this->error('Cannot find super admin with username [ ' . $name . ' ].');

            return;
        }

        $admin->update([
            'email' => $email = $this->argument('email'),
        ]);

        $this->info(
            "[ {$name} ]'s email has been changed to {$email}!"
        );
    }
}
