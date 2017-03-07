<?php

namespace App\Services;

use App\CrawlerData;
use App\Entities\Financial\Order;
use App\Entities\Game\LoginLog;
use App\Entities\Game\Player;
use App\Entities\Game\Statistic;
use App\Server;
use Carbon\Carbon;
use DB;

class Crawler
{
    /**
     * @var \Carbon\Carbon
     */
    private $date;

    /**
     * @var \App\Server
     */
    private $server;

    public function __construct(Carbon $date, Server $server)
    {
        $this->date = $date;
        $this->server = $server;
    }

    public function setServer(Server $server)
    {
        $this->server = $server;
    }

    public function getRegisters()
    {
        $registers = Player::setServer($this->server)->newQuery();
        $registers->where(DB::raw('DATE(RegTime)'), $date = $this->date->toDateString());
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'type'      => CrawlerData::REGISTERS,
            'server_id' => $this->server->id,
        ], [
            'value' => $registers->count('UserID'),
        ]);
    }

    public function getActives()
    {
        $logins = LoginLog::setServer($this->server)->newQuery();
        $logins->where(DB::raw('DATE(LoginTime)'), $date = $this->date->toDateString());
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'server_id' => $this->server->id,
            'type'      => CrawlerData::ACTIVES,
        ], [
            'value' => $logins->count(DB::raw('DISTINCT(UserID)')),
        ]);
        
        // Edit by abraham 07/06/2016
        // 以天为单位计算活跃人数，当控制台显示一个时间段内的总活跃人数时，累加每天的值，会出现活跃
        // 人数远大于注册人数的情况，因为这会重复计算那些多天登录的用户。
        // 为了解决这个问题，我们按照时间段统计，举例子：
        // 假设某月共有 1 2 3 4 5 日的数据，我们存储 1 日的活跃人数为 1 - 1 日（ - 是至的意思，不是减号），
        // 2 日为 1 - 2 日， 3 日为 1 - 3 日， 4 为 1 - 4 日 这种类似斐波那契数列的统计方式。
        // 当我们需要获取首日（1 日）活跃人数，则为 1 - 1 日统计数据， 获取 3 - 4 日活跃人数，
        // 则为 1 - 4 日活跃人数减去 1 - 2 日活跃人数，也就是说：
        // 某个时间段的活跃人数 = 第一日至最后一日的活跃人数 减去 第一日至所选时间段之前一天的活跃人数。
        $logins = LoginLog::setServer($this->server)->newQuery();
        $yesterday = $this->date->toDateString();
        $firstDay = CrawlerData::where('server_id', $this->server->id)
                               ->where('type', CrawlerData::ACTIVES)
                               ->orderBy('date', 'ASC')
                               ->first();
        // 如果有第一天的数据，则首日设为第一天，如果没有，设为昨天。
        if ($firstDay) {
            $firstDate = $firstDay->date;
        } else {
            $firstDate = $yesterday;
        }

        $logins->where(DB::raw('DATE(LoginTime)'), '>=', $firstDate)
               ->where(DB::raw('DATE(LoginTime)'), '<=', $yesterday);
        CrawlerData::updateOrCreate([
            'date'      => $yesterday,
            'server_id' => $this->server->id,
            'type'      => CrawlerData::ACTIVES_BY_RANGE,
        ], [
            'value' => $logins->count(DB::raw('DISTINCT(UserID)')),
        ]);
    }

    public function getPayed()
    {
        $payed = Order::setServer($this->server)->newQuery();
        $payed->where(DB::raw('DATE(createTime)'), $date = $this->date->toDateString());
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'type'      => CrawlerData::PAYED,
            'server_id' => $this->server->id,
        ], [
            'value' => $payed->count(DB::raw('DISTINCT(aid)')),
        ]);
    }

    public function getAmount()
    {
        $amount = Order::setServer($this->server)->newQuery();
        $amount->where(DB::raw('DATE(createTime)'), $date = $this->date->toDateString());
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'type'      => CrawlerData::AMOUNT,
            'server_id' => $this->server->id,
        ], [
            'value' => $amount->sum('orderMoney'),
        ]);
    }

    public function getAcu()
    {
        $acu = Statistic::setServer($this->server)->newQuery();
        $acu->where(DB::raw('DATE(StatTime)'), $date = $this->date->toDateString());
        $acu->where('StatType', 2);
        $sum = $acu->sum('StatNum');
        $count = $acu->count('StatTime');
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'type'      => CrawlerData::ACU,
            'server_id' => $this->server->id,
        ], [
            'value' => $sum ? round($sum / $count, 0) : 0,
        ]);
    }

    public function getPcu()
    {
        $pcu = Statistic::setServer($this->server)->newQuery();
        $pcu->where(DB::raw('DATE(StatTime)'), $date = $this->date->toDateString());
        $pcu->where('StatType', 2);
        $pcu->groupBy(DB::raw('HOUR(StatTime)'));
        $pcu->select(DB::raw('MAX(StatNum) as StatNum, HOUR(StatTime) as hour'));
        $data = [];
        $pcu->get()->map(function ($item) use(&$data) {
            $data[$item->hour] = $item->StatNum;
        });
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'type'      => CrawlerData::PCU,
            'server_id' => $this->server->id,
        ], [
            'data' => $data,
        ]);
    }

    public function getNewLogins()
    {
        // first logins
        $select = LoginLog::setServer($this->server)->newQuery();
        $select->from(DB::raw('log_onlineinfo main'));
        $select->where(DB::raw('DATE(LoginTime)'), $date = $this->date->toDateString());
        $select->whereNotIn('UserID', function ($query) {
            $query->from(DB::raw('log_onlineinfo'));
            $query->select('UserID');
            $query->where('LoginTime', '<', DB::raw('DATE(main.LoginTime)'));
        });
        $logins = $select->count(DB::raw('DISTINCT(UserID)'));
        CrawlerData::updateOrCreate([
            'date'      => $date,
            'type'      => CrawlerData::NEW_LOGINS,
            'server_id' => $this->server->id,
        ], [
            'value' => $logins
        ]);
    }
}
