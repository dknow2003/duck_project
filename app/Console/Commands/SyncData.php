<?php

namespace App\Console\Commands;

use App\CrawlerStatus;
use App\Server;
use App\Services\Crawler;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Console\Command;

class SyncData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data
    {--all : fetch all days.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync analyze data from servers.';

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
        $timezone = new \DateTimeZone('Asia/Chongqing');
        $date = Carbon::now($timezone)->subDay();

        $servers = Server::all();

        // 如果抓所有的
        if ($this->option('all')) {
            // 取得最早开服时间
            $earliest = Carbon::now();
            foreach ($servers as $server) {
                if ($server->start_from != '0000-00-00' && $server->start_from) {
                    $serverStartFrom = Carbon::createFromFormat('Y-m-d', $server->start_from, $timezone);
                    $earliest = $serverStartFrom < $earliest ? $serverStartFrom : $earliest;
                }
            }
        } else {
            $earliest = Carbon::now($timezone)->subDay();
        }

        // 时间跨度制作
        $interval = new DateInterval('P1D');
        $date->add($interval);
        $daterange = new DatePeriod($earliest, $interval, $date);
        $this->output->progressStart(iterator_count($daterange));
        foreach ($daterange as $d) {
            foreach ($servers as $server) {
                $crawler = new Crawler($d, $server);
                $crawler->getRegisters();
                $crawler->getPayed();
                $crawler->getAmount();
                $crawler->getActives();
                $crawler->getAcu();
                $crawler->getPcu();
                $crawler->getNewLogins();
            }
            $this->output->progressAdvance();
        }
        $this->updateSyncTime();
        $this->output->progressFinish();
        $this->info('Sync complete!');
    }

    private function updateSyncTime()
    {
        CrawlerStatus::updateOrCreate([
            'date' => Carbon::now()->subDay()->toDateString(),
            'status' => true
        ]);
    }
}
