<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        //$this->addServer();
        //$this->addSuperAdmin();
        //$this->addTestStatData();
        //$this->fillCalendar();
    }

    private function addServer()
    {
        $server = \App\Server::create([
            'name'        => 'Test server',
            'connections' => [
                1 => [
                    'host'     => '127.0.0.1',
                    'database' => 'slg2',
                    'port'     => 3306,
                    'username' => 'homestead',
                    'password' => 'secret',
                ],
                2 => [
                    'host'     => '127.0.0.1',
                    'database' => 'firegms2',
                    'port'     => 3306,
                    'username' => 'homestead',
                    'password' => 'secret',
                ],
            ],
        ]);
    }

    private function addSuperAdmin()
    {
        \Artisan::call('admin:create', [
            'username' => 'woody',
            'password' => 'woaini',
            'email'    => '82011220@qq.com',
        ]);
    }

    private function addTestStatData()
    {
        $time = time();
        for ($i = 0; $i < 100000; $i++) {
            \App\Entities\Game\Statistic::create([
                'StatTime' => date("Y-m-d H:i:s", $time = $time - 55),
                'StatType' => mt_rand(1, 9) > 5 ? 1 : 2,
                'StatNum' => mt_rand(10000, 99999)
            ]);
        }
    }
}
