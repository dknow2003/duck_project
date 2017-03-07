<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DeleteAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:delete
    {username : The username who you want to remove.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete super admin.';

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

        // remove superadmin permission.
        $superAdminRole = \App\Role::where('name', 'superadmin')->first();

        $admin->roles()->detach($superAdminRole->id);

        $admin->delete();

        $this->info(
            "Super admin [ {$name} ] deleted!"
        );
    }
}
