<?php

namespace App\Http\Controllers\Remote\Analyze;

use App\Entities\Financial\Order;
use App\Entities\Game\Base;
use App\Entities\Game\LoginLog;
use App\Entities\Game\Player;
use App\Entities\Game\Role;
use App\Entities\Game\Statistic;
use App\Menu\MenuPresenter;
use App\Server;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AnalyzeController extends Controller
{
    /** @var int filter by time range */
    protected $from = 0;

    protected $to   = 0;

    private   $orderDatabaseName;

    private   $acuByDay;

    private   $payedByDay;

    private   $activesByDay;

    private   $startFrom;

    private   $export;

    private   $fetch;

    public function __construct(Request $request, MenuPresenter $menu)
    {
        parent::__construct($menu);
        $this->from = $request->get('from') ? str_replace(['年', '月', '日'], ['-', '-', ''], $request->get('from')) : Carbon::now()->subMonth()->toDateString();
        $this->to = $request->get('to') ? str_replace(['年', '月', '日'], ['-', '-', ''], $request->get('to')) : Carbon::now()->toDateString();
        if ($request->get('month')) {
            $day = $request->get('month') . '1日';
            $this->from = Carbon::createFromFormat('Y年m月d日', $day)->toDateString();
            $this->to = Carbon::createFromFormat('Y-m-d', $this->from)->addMonth()->toDateString();
        }
        $this->startFrom = $this->getStartFrom();
        $this->orderDatabaseName = Order::getDefaultConnections()[2]['database'];
        $this->export = $request->get('export') ?: null;
        $this->fetch = $request->get('fetch') ?: null;
    }

    public function summarize(Request $request)
    {

        $menu = $this->menu;
        // 取得开服日期，用最早 acu

        $players['registers'] = $this->getRegisters();
        $players['actives'] = $this->getActives();
        $players['payed'] = $this->getPayed();
        $players['amount'] = $this->getAmount();
        $players['acu'] = $this->getAcu();
        $players['pcu'] = $this->getPcu();
        $players['acu_by_day'] = $this->getAcuByDay();
        $players['pcu_by_day'] = $this->getPcuByDay();
        //$players['register_by_day'] = $this->getRegistersByDay();
        $players['login_by_day'] = $this->getLoginsByDay();
        $players['pur'] = $this->getPayedByDay();
        $players['display_from'] = $this->getDisplayFrom($this->from ?: $this->startFrom);
        $players['display_to'] = $this->getDisplayTo();

        //dd($players);
        $this->ifExport($players);

        return view('remote.analyze.summarize', compact('menu', 'players'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function json(Request $request)
    {
        $method = $request->get('method');
        $by = $request->get('by') ?: 'day';
        $players = [];
        switch ($method) {
            case 'active':
                $players['result'] = $this->getActiveNewAndOldByDay($by);
                break;
            case 'payed':
                $result = $this->getPayedUsersByDay($by);
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['payed'];
                }
                $players['result'] = $result;
                break;
            case 'incoming':
                $result = $this->getAmountByDay($by);
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['money'];
                }
                $players['result'] = $result;
                break;
            case 'arpu':
                $result = $this->getArpuByDay($by);
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['arpu'];
                }
                $players['result'] = $result;
                break;
            case 'arppu':
                $result = $this->getArppuByDay($by);
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['arppu'];
                }
                $players['result'] = $result;
                break;
            case 'au-avg':
                $result = $this->getAuAvgByDay($by);
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['auAvg'];
                }
                $players['result'] = $result;
                break;
        }

        $this->ifExport($players['result']);

        return $players;
    }

    public function twoWeeks(Request $request)
    {
        $menu = $this->menu;

        $players['acu_by_day'] = $this->getAcuByDay('two_weeks');
        $players['registers'] = $this->getRegisters();
        $players['actives'] = $this->getActives();
        $players['payed'] = $this->getPayed();
        $players['amount'] = $this->getAmount();
        $players['acu'] = $this->getAcu();
        $players['pcu'] = $this->getPcu();
        $players['pcu_by_day'] = $this->getPcuByDay('two_weeks');
        //$players['register_by_day'] = $this->getRegistersByDay();
        $players['login_by_day'] = $this->getLoginsByDay('two_weeks');
        $players['pur'] = $this->getPayedByDay('two_weeks');
        $players['display_from'] = $this->getDisplayFrom($this->from ?: $this->startFrom);
        $players['display_to'] = $this->getDisplayTo();
        // 开服日期
        $players['start_time'] = $this->startFrom;
        $this->ifExport($players);

        return view('remote.analyze.two-weeks', compact('menu', 'players'));
    }

    public function monthly(Request $request)
    {
        $menu = $this->menu;

        $players['acu_by_day'] = $this->getAcuByDay('month');
        $players['pcu_by_day'] = $this->getPcuByDay('month');
        $players['registers'] = $this->getRegisters();
        $players['actives'] = $this->getActives();
        $players['payed'] = $this->getPayed();
        $players['amount'] = $this->getAmount();
        $players['acu'] = $this->getAcu();
        $players['pcu'] = $this->getPcu();
        //$players['register_by_day'] = $this->getRegistersByDay();
        $players['login_by_day'] = $this->getLoginsByDay('month');
        $players['pur'] = $this->getPayedByDay('month');
        $players['display_from'] = $this->getDisplayFrom($this->from ?: $this->startFrom);
        $players['display_to'] = $this->getDisplayTo();
        // 开服日期
        $players['start_time'] = $this->startFrom;
        $this->ifExport($players);

        return view('remote.analyze.monthly', compact('menu', 'players'));
    }

    /**
     * 特定时间段用户数
     *
     * @return int
     */
    private function getRegisters()
    {
        $group = $this->getRegistersByDay();
        if ($group->isEmpty()) {
            return 0;
        }

        $registersByDay = 0;
        foreach ($group as $singleDay) {
            $registersByDay += $singleDay->registers;
        }

        return $registersByDay;
    }

    /**
     * 特定时间段活跃用户
     *
     * @return int
     */
    private function getActives()
    {
        $players = Player::join('log_onlineinfo', function ($join) {
            $join->on('usr_userinfo.UserID', '=', 'log_onlineinfo.UserID');
        });

        $players->where(DB::raw('DATE(log_onlineinfo.LoginTime)'), '>=', $this->from ?: $this->startFrom);
        if ($this->to) {
            $players->where(DB::raw('DATE(log_onlineinfo.LoginTime)'), '<=', $this->to);
        }

        return $players->count(DB::raw('DISTINCT(usr_userinfo.UserID)')) ?: 0;
    }

    /**
     * 特定时间充值人数
     *
     * @return int
     */
    private function getPayed()
    {
	$orders = Order::where('payStatus',1); // where('createTime', '>=', $this->from ?: $this->startFrom);
/*
        if ($this->to) {
            $orders->where('createTime', '<=', $this->to);
        }
*/
        $ret =  $orders->groupBy('aid')->get()->count();
        return $ret;

/*
        $joinTable = DB::raw($this->orderDatabaseName . '.gms_order');
        $players = DB::table('usr_userinfo')->join($joinTable, function ($join) use ($joinTable) {
            $join->on("{$joinTable}.aid", '=', 'usr_userinfo.UserID');
        });

        $players->where(DB::raw("DATE({$joinTable}.createTime)"), '>=', $this->from ?: $this->startFrom);

        if ($this->to) {
            $players->where(DB::raw("DATE({$joinTable}.createTime)"), '<=', $this->to);
        }

        $players = $players->groupBy('usr_userinfo.UserID');
        return $players->count('usr_userinfo.UserID') ?: 0;
*/

        //$orders = Order::where(DB::raw('DATE(createTime)'), '>=', $this->from ?: $this->startFrom);
        //$orders->where(DB::raw('DATE(createTime)'), '<=', $this->to);

    }

    /**
     * 特定时间充值总额
     *
     * @return int
     */
    private function getAmount()
    {
        $orders = (new Order())->newQuery();

        $orders->where('payStatus',1)->where(DB::raw('DATE(createTime)'), '>=', $this->from ?: $this->startFrom);
        if ($this->to) {
            $orders->where(DB::raw('DATE(createTime)'), '<=', $this->to);
        }


        return $orders->sum('orderMoney');
    }

    /**
     * 同时在线人数
     */
    private function getAcu()
    {
        $group = $this->getAcuByDay();
        if (!$group) {
            return 0;
        }

        $acuByDay = 0;
        foreach ($group as $singleDay) {
            $acuByDay += $singleDay['acu'];
        }

        return $acuByDay / count($group);
    }

    /**
     * 最高在线人数
     */
    private function getPcu()
    {
        $group = $this->getPcuByDay();

        if (!$group) {
            return 0;
        }

        $pcuByDay = 0;
        foreach ($group as $singleDay) {
            $pcuByDay = $singleDay['pcu'] > $pcuByDay ? $singleDay['pcu'] : $pcuByDay;
        }

        return $pcuByDay;
    }

    /**
     * 以每天为一组计算 acu
     *
     * @return mixed
     */
    private function getAcuByDay($by = 'day')
    {
        $stats = Statistic::from(DB::raw('sat_online'));
        if ($by == 'day') {
            $select = "SUM(StatNum) / COUNT(StatTime) as acu, DATE_FORMAT(StatTime, '%m月%d日') as day";
            $groupBy = "DATE(StatTime)";
            $stats->where(DB::raw('DATE(StatTime)'), '>=', $this->from ?: $this->startFrom);
            if ($this->to) {
                $stats->where(DB::raw('DATE(StatTime)'), '<=', $this->to);
            }
            $stats->orderBy('StatTime', 'ASC');
            $stats->where('StatType', 2);
        } elseif ($by == 'two_weeks') {
            $stamp = strtotime($this->from ?: $this->startFrom);
            $select = "SUM(StatNum) / COUNT(StatTime) as acu, DATE(MIN(date))";
            // 开始日期时间戳
            $stats->orderBy('date', 'ASC');
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 1209600";
            //
            $stats->where('date', '>=', $this->from ?: $this->startFrom);

            $stats->where('date', '<=', $this->to);
            $stats->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(StatTime)'), '=', 'date');
                $join->where('StatType', '=', 2);
            });
        } elseif ($by == 'month') {
            $stamp = strtotime($this->from ?: $this->startFrom);
            $select = "SUM(StatNum) / COUNT(StatTime) as acu";
            // 开始日期时间戳
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 2592000";
            //
            $stats->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(StatTime)'), '=', 'date');
                $join->where('StatType', '=', 2);
            });
            $stats->where('date', '>=', $this->from ?: $this->startFrom);

            $stats->where('date', '<=', $this->to);
            $stats->orderBy('date', 'ASC');
        }
        //DB::enableQueryLog();
        $stats->select(DB::raw($select));
        $result = $stats->groupBy(DB::raw($groupBy))->get();
        if ($by == 'two_weeks') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
            //dd($result);

        } elseif ($by == 'day') {
            $result = $result->keyBy('day');
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $r[$key]['acu'] = isset($result[$daytoStr]) ? $result[$daytoStr]['acu'] : 0;
                $r[$key]['day'] = $daytoStr;
            }
            $result = $r;
        } elseif ($by == 'month') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        }

        //dd($result);
        return $result;
    }

    /**
     * 以每天为一组计算 pcu
     *
     * @param string $by
     *
     * @return mixed
     */
    private function getPcuByDay($by = 'day')
    {
        $stats = Statistic::from('sat_online');
        if ($by == 'day') {
            $select = "MAX(StatNum) as pcu, DATE_FORMAT(StatTime, '%m月%d日') as day";
            $groupBy = "DATE(StatTime)";
            $stats->where(DB::raw('DATE(StatTime)'), '>=', $this->from ?: $this->startFrom);
            $stats->where(DB::raw('DATE(StatTime)'), '<=', $this->to);
            $stats->where('StatType', 2);
        } elseif ($by == 'two_weeks') {
            $select = "MAX(StatNum) as pcu";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            // 开始日期时间戳
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 1209600";
            //
            $stats->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(StatTime)'), '=', 'date');
                $join->where('StatType', '=', 2);
            });
            $stats->where('date', '>=', $this->from ?: $this->startFrom);

            $stats->where('date', '<=', $this->to);
            $stats->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "MAX(StatNum) as pcu";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            // 开始日期时间戳
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 2592000";
            //
            $stats->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(StatTime)'), '=', 'date');
                $join->where('StatType', '=', 2);
            });
            $stats->where('date', '>=', $this->from ?: $this->startFrom);

            $stats->where('date', '<=', $this->to);
            $stats->orderBy('date', 'ASC');
        }

        $stats->select(DB::raw($select));

        $result = $stats->groupBy(DB::raw($groupBy))->get();

        if ($by == 'two_weeks') {
            $result->map(function ($item, $key) use ($stamp) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (strtotime($wantTo) < $stamp + (($key + 1) * 14 * 86400)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        } elseif ($by == 'day') {
            $result = $result->keyBy('day');
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $r[$key]['pcu'] = isset($result[$daytoStr]) ? $result[$daytoStr]['pcu'] : 0;
                $r[$key]['day'] = $daytoStr;
            }
            $result = $r;
        } elseif ($by == 'month') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        }

        return $result;
    }

    /**
     * 以每天为一组 计算 注册用户
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getRegistersByDay($by = 'day')
    {
        $players = (new Player)->newQuery();

        if ($by == 'day') {
            $select = "DATE_FORMAT(RegTime, '%m月%d日') as day, COUNT(UserID) as  registers";
            $groupBy = "DATE(RegTime)";
            $players->orderBy('RegTime', 'asc');
            $players->where(DB::raw('DATE(RegTime)'), '>=', $this->from ?: $this->startFrom);
            if ($this->to) {
                $players->where(DB::raw('DATE(RegTime)'), '<=', $this->to);
            }
            $players->groupBy(DB::raw($groupBy));
        } elseif ($by == 'two_weeks') {
            $select = "DATE_FORMAT(MIN(date), '%m月%d日') as day, COUNT(UserID) as  registers";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            //
            // 开始日期时间戳
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date))  DIV 1209600";

            $players->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(RegTime)'), '=', 'date');
            });
            $players->where('date', '>=', $this->from ?: $this->startFrom);

            $players->where('date', '<=', $this->to);
            $players->orderBy('date', 'ASC');
            //
        } elseif ($by == 'month') {
            $select = "DATE_FORMAT(MIN(date), '%m月%d日') as day, COUNT(UserID) as  registers";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            //
            // 开始日期时间戳
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date))  DIV 2592000";

            $players->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(RegTime)'), '=', 'date');
            });
            $players->where('date', '>=', $this->from ?: $this->startFrom);

            $players->where('date', '<=', $this->to);
            $players->orderBy('date', 'ASC');
        }

        $players->select(DB::raw($select));

        $result = $players->groupBy(DB::raw($groupBy))->get();

        if ($by == 'two_weeks') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        } elseif ($by == 'month') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        }

        return $result;
    }

    /**
     * 以每天为一组计算 首次登录和注册的用户数量
     *
     * @return array
     */
    private function getLoginsByDay($by = 'day')
    {
        $logins = LoginLog::from(DB::raw('log_onlineinfo main'));
        if ($by == 'day') {
            $select = "DATE_FORMAT(LoginTime, '%m月%d日') as identityKey, COUNT(DISTINCT(UserID)) as logins, CONCAT(MONTH(LoginTime), '月', DAY(LoginTime), '日') as day";
            $groupBy = 'DATE(LoginTime)';
            $logins->orderBy('LoginTime', 'asc');
            $logins->where(DB::raw('DATE(LoginTime)'), '>=', $this->from ?: $this->startFrom);
            $logins->where(DB::raw('DATE(LoginTime)'), '<=', $this->to);
        } elseif ($by == 'two_weeks') {
            $select = "DATE_FORMAT(MIN(date), '%m月%d日') as identityKey, COUNT(DISTINCT(UserID)) as logins";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date))  DIV 1209600";
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);

            $logins->where('date', '<=', $this->to ?: Carbon::now());
            $logins->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "DATE_FORMAT(MIN(date), '%m月%d日') as identityKey, COUNT(DISTINCT(UserID)) as logins";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date))  DIV 2592000";
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);

            $logins->where('date', '<=', $this->to ?: Carbon::now());
            $logins->orderBy('date', 'ASC');
        }

        $logins->select(DB::raw($select));
        $logins->whereNotIn('UserID', function ($query) {
            $query->select('UserID');
            $query->from(DB::raw('log_onlineinfo'));
            $query->where('LoginTime', '<', DB::raw('DATE(main.LoginTime)'));
        });

        $result = $logins->groupBy(DB::raw($groupBy))->get();
        if ($by == 'day') {
            $result = $result->keyBy('identityKey');
            $allRegisters = $this->getRegistersByDay()->keyBy('day')->all();
            //dd($allRegisters);
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $r[$key]['logins'] = isset($result[$daytoStr]) ? $result[$daytoStr]['logins'] : 0;
                $r[$key]['registers'] = isset($allRegisters[$daytoStr]) ? $allRegisters[$daytoStr]['registers'] : 0;
                $r[$key]['day'] = $daytoStr;
            }
            $result = $r;
        } elseif ($by == 'two_weeks') {
            $allRegisters = $this->getRegistersByDay('two_weeks')->keyBy('day')->all();
            //dd($allRegisters);
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $allRegisters, $result, &$i) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
                $item->registers = $allRegisters[$item->day]['registers'];
            });
        } elseif ($by == 'month') {
            $allRegisters = $this->getRegistersByDay('month')->keyBy('day')->all();
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $allRegisters, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
                //dd($allRegisters);
                $item->registers = isset($allRegisters[$item->day]) ? $allRegisters[$item->day]['registers'] : 0;
            });
        }

        return $result;
    }

    /**
     * 付费用户按天
     *
     * @return mixed
     */
    private function getPayedUsersByDay($by = 'day')
    {
        $orders = (new Order())->newQuery();
        if ($by == 'day') {
            $select = "DATE_FORMAT(createTime, '%m月%d日') as day, COUNT(DISTINCT(aid)) as payed";
            $groupBy = "DATE(createTime)";
            $orders->where(DB::raw('DATE(createTime)'), '>=', $this->from ?: $this->startFrom);
            $orders->where(DB::raw('DATE(createTime)'), '<=', $this->to);
            $orders->orderBy('createTime', 'ASC');
        } elseif ($by == 'two_weeks') {
            $select = "COUNT(DISTINCT(aid)) as payed";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date))  DIV 1209600";
            $orders->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(createTime)'), '=', 'date');
            });
            $orders->where('date', '>=', $this->from ?: $this->startFrom);

            $orders->where('date', '<=', $this->to ?: Carbon::now());
            $orders->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "COUNT(DISTINCT(aid)) as payed";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date))  DIV 2592000";
            $orders->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(createTime)'), '=', 'date');
            });
            $orders->where('date', '>=', $this->from ?: $this->startFrom);

            $orders->where('date', '<=', $this->to ?: Carbon::now());
            $orders->orderBy('date', 'ASC');
        }
        $result = $orders->select(DB::raw($select))->groupBy(DB::raw($groupBy))->get();
        //dd($result);
        if ($by == 'day') {
            $result = $result->keyBy('day');
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $r[$key]['payed'] = isset($result[$daytoStr]) ? $result[$daytoStr]['payed'] : 0;
                $r[$key]['day'] = $daytoStr;
            }
            $result = $r;
        } elseif ($by == 'two_weeks') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";

                return $item;
            });
        } elseif ($by == 'month') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";

                return $item;
            });
        }

        return $result;
    }

    /**
     * 以天为单位的付费率
     */
    private function getPayedByDay($by = 'day')
    {
        if ($by == 'day') {
            $au = $this->getActivesByDay();
            $payed = $this->getPayedUsersByDay();
        } elseif ($by == 'two_weeks') {
            $au = $this->getActivesByDay('two_weeks');
            $payed = $this->getPayedUsersByDay('two_weeks');
        } elseif ($by == 'month') {
            $au = $this->getActivesByDay('month');
            $payed = $this->getPayedUsersByDay('month');
        }

        $result = [];
        foreach ($payed as $key => $value) {
            $result[$key]['day'] = $payed[$key]['day'];
            $tmp = isset($au[$key]) && $au[$key]['actives'] != 0 ? ($payed[$key]['payed'] / $au[$key]['actives']) : 0;
            $result[$key]['payed'] = round(100 * $tmp, 1) < 100 ? round(100 * $tmp, 1) : 100;
        }

        //dd($result);
        return $result;
    }

    private function getActivesByDay($by = 'day')
    {
        $logins = (new LoginLog())->newQuery();
        if ($by == 'day') {
            $select = "DATE_FORMAT(LoginTime, '%m月%d日') as day, COUNT(UserID) as actives, DATE(LoginTime) as keyday";
            $groupBy = "DATE(LoginTime)";
            $logins->orderBy('LoginTime', 'asc');
            $logins->where(DB::raw('DATE(LoginTime)'), '>=', $this->from ?: $this->startFrom);
            $logins->where(DB::raw('DATE(LoginTime)'), '<=', $this->to);
        } elseif ($by == 'two_weeks') {
            $select = "COUNT(UserID) as actives";
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 1209600";
            //
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);

            $logins->where('date', '<=', $this->to ?: Carbon::now());
            $logins->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "COUNT(UserID) as actives";
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 2592000";
            //
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);

            $logins->where('date', '<=', $this->to ?: Carbon::now());
            $logins->orderBy('date', 'ASC');
        }

        $logins->groupBy(DB::raw($groupBy))->select(DB::raw($select));

        $result = $logins->get();
        if ($by == 'day') {
            $result = $result->keyBy('day');
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $r[$key]['actives'] = isset($result[$daytoStr]) ? $result[$daytoStr]['actives'] : 0;
                $r[$key]['day'] = $daytoStr;
                $r[$key]['key'] = $day->format('Y-m-d');
            }
            $result = $r;
        } elseif ($by == 'two_weeks') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        } elseif ($by == 'month') {
            $i = 0;
            $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
            });
        }

        return $result;
    }

    /**
     * 页面筛选显示的开始时间
     *
     * @return bool|string
     */
    private function getDisplayFrom($fromIm = null)
    {
        $from = $this->from ?: date('Y-m-d', time() - 604800);
        if ($fromIm) {
            $from = $fromIm;
        }
        $str = strtotime($from);

        return date('Y年m月d日', $str);
    }

    /**
     * 页面筛选显示的结束时间
     */
    private function getDisplayTo()
    {
        $to = $this->to ?: date('Y-m-d', time());

        return date('Y年m月d日', strtotime($to));
    }

    /**
     * 新活跃玩家，老活跃玩家数量按天分组
     */
    private function getActiveNewAndOldByDay($by = 'day')
    {
        $resultKeyDay = 'day';
        $resultKeyLogins = 'logins';
        $identityKey = 'identityKey';
        $logins = LoginLog::from(DB::raw('log_onlineinfo main'));
        // new
        if ($by == 'day') {
            $select = "DATE_FORMAT(LoginTime, '%m月%d日') as {$identityKey}, COUNT(DISTINCT(UserID)) as {$resultKeyLogins}, CONCAT(MONTH(LoginTime), '-', DAY(LoginTime)) as {$resultKeyDay}";
            $groupBy = "DATE(LoginTime)";
            $logins->orderBy('LoginTime', 'asc');
            $logins->where(DB::raw('DATE(LoginTime)'), '>=', $this->from ?: $this->startFrom);
            $logins->where(DB::raw('DATE(LoginTime)'), '<=', $this->to);
        } elseif ($by == 'two_weeks') {
            $select = "COUNT(DISTINCT(UserID)) as {$resultKeyLogins}";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 1209600";
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);
            $logins->where('date', '<=', $this->to);
            $logins->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "COUNT(DISTINCT(UserID)) as {$resultKeyLogins}";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 2592000";
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);
            $logins->where('date', '<=', $this->to);
            $logins->orderBy('date', 'ASC');
        }

        $logins->select(DB::raw($select));
        $logins->whereNotIn('UserID', function ($query) {
            $query->select('UserID');
            $query->from(DB::raw('log_onlineinfo'));
            $query->where('LoginTime', '<', DB::raw('DATE(main.LoginTime)'));
        });

        $new = $logins->groupBy(DB::raw($groupBy))->get();
        // old
        $logins = LoginLog::from(DB::raw('log_onlineinfo main'));
        if ($by == 'day') {
            $select = "DATE_FORMAT(LoginTime, '%m月%d日') as {$identityKey}, COUNT(DISTINCT(UserID)) as {$resultKeyLogins}, CONCAT(MONTH(LoginTime), '-', DAY(LoginTime)) as {$resultKeyDay}";
            $groupBy = "DATE(LoginTime)";
            $logins->orderBy('LoginTime', 'asc');
            $logins->where(DB::raw('DATE(LoginTime)'), '>=', $this->from ?: $this->startFrom);
            $logins->where(DB::raw('DATE(LoginTime)'), '<=', $this->to);
        } elseif ($by == 'two_weeks') {
            $select = "COUNT(DISTINCT(UserID)) as {$resultKeyLogins}";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 1209600";
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);
            $logins->where('date', '<=', $this->to);
            $logins->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "COUNT(DISTINCT(UserID)) as {$resultKeyLogins}";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 2592000";
            $logins->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(LoginTime)'), '=', 'date');
            });
            $logins->where('date', '>=', $this->from ?: $this->startFrom);
            $logins->where('date', '<=', $this->to);
            $logins->orderBy('date', 'ASC');
        }
        $logins->select(DB::raw($select));

        //DB::enableQueryLog();
        $old = $logins->groupBy(DB::raw($groupBy))->get();
        //dd(DB::getQueryLog());

        if ($by == 'day') {
            $new = $new->keyBy($identityKey);
            $old = $old->keyBy($identityKey);
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $perNew = isset($new[$daytoStr]) ? $new[$daytoStr]['logins'] : 0;
                $perOld = isset($old[$daytoStr]) ? $old[$daytoStr]['logins'] : 0;
                $r[$key]['day'] = $daytoStr;
                $r[$key]['one'] = $perOld;
                $r[$key]['two'] = $perNew;
            }
            $result = $r;
        } elseif ($by == 'two_weeks') {
            $i = 0;
            $result = $old->map(function ($item, $key) use ($stamp, $old, &$i, $new) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($old)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
                $item->one = $item->logins;
                $item->two = isset($new[$key]) ? $new[$key]['logins'] : 0;

                return $item;
            });
        } elseif ($by == 'month') {
            $i = 0;
            $result = $old->map(function ($item, $key) use ($stamp, $old, &$i, $new) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($old)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
                $item->one = $item->logins + (isset($new[$key]) ? $new[$key]['logins'] : 0);
                $item->two = isset($new[$key]) ? $new[$key]['logins'] : 0;

                return $item;
            });
        }

        return $result;
    }

    /**
     * 按天的消费额
     *
     * @return mixed
     */
    private function getAmountByDay($by = 'day')
    {
        $orders = (new Order())->newQuery();
        if ($by == 'day') {
            $select = "DATE_FORMAT(createTime, '%m月%d日') as day, SUM(orderMoney) as money";
            $groupBy = "DATE(createTime)";
            $orders->orderBy('createTime', 'ASC');
            $orders->where(DB::raw('DATE(createTime)'), '>=', $this->from ?: $this->startFrom);
            $orders->where(DB::raw('DATE(createTime)'), '<=', $this->to);
        } elseif ($by == 'two_weeks') {
            $select = "SUM(orderMoney) as money";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 1209600";
            $orders->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(createTime)'), '=', 'date');
            });
            $orders->where('date', '>=', $this->from ?: $this->startFrom);
            $orders->where('date', '<=', $this->to);
            $orders->orderBy('date', 'ASC');
        } elseif ($by == 'month') {
            $select = "SUM(orderMoney) as money";
            // 开始日期时间戳
            $stamp = strtotime($this->from ?: $this->startFrom);
            $groupBy = "({$stamp} - UNIX_TIMESTAMP(date)) DIV 2592000";
            $orders->rightJoin(DB::raw('admin_calendar'), function ($join) {
                $join->on(DB::raw('DATE(createTime)'), '=', 'date');
            });
            $orders->where('date', '>=', $this->from ?: $this->startFrom);
            $orders->where('date', '<=', $this->to);
            $orders->orderBy('date', 'ASC');
        }

        $orders->select(DB::raw($select));
        $orders->groupBy(DB::raw($groupBy));
        $result = $orders->get();
        if ($by == 'day') {
            $result = $result->keyBy('day');
            $r = [];
            foreach ($this->stepDays() as $key => $day) {
                $daytoStr = $day->format('m月d日');
                $r[$key]['money'] = isset($result[$daytoStr]) ? $result[$daytoStr]['money'] : 0;
                $r[$key]['day'] = $daytoStr;
            }
            $result = $r;
            //dd($result);
        } elseif ($by == 'two_weeks') {
            $i = 0;
            $result = $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 14 . ' days';
                $toAdd = (($key + 1) * 14 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";
                $item->money = $item->money ?: 0;

                return $item;
            });
        } elseif ($by == 'month') {
            $i = 0;
            $result = $result->map(function ($item, $key) use ($stamp, $result, &$i) {
                $fromAdd = $key * 30 . ' days';
                $toAdd = (($key + 1) * 30 - 1) . ' days';
                $wantTo = $this->to ?: date('Y-m-d', time());
                $inFrom = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($fromAdd)), 'm月d日');
                if (++$i == count($result)) {
                    $inTo = date('m月d日', strtotime($wantTo));
                } else {
                    $inTo = date_format(date_add(date_create(date('Y-m-d', $stamp)), date_interval_create_from_date_string($toAdd)), 'm月d日');
                }
                $item->day = "{$inFrom} -- {$inTo}";

                return $item;
            });
        }

        //dd($result);
        return $result;
    }

    /**
     * 按天 arpu
     */
    private function getArpuByDay($by = 'day')
    {
        if ($by == 'day') {
            $actives = $this->getActivesByDay();
            $amounts = $this->getAmountByDay();
        } elseif ($by == 'two_weeks') {
            $actives = $this->getActivesByDay('two_weeks');
            $amounts = $this->getAmountByDay('two_weeks');
        } elseif ($by == 'month') {
            $actives = $this->getActivesByDay('month');
            $amounts = $this->getAmountByDay('month');
        }
        $result = [];
        foreach ($actives as $key => $value) {
            $result[$key]['day'] = $actives[$key]['day'];
            $result[$key]['key'] = $actives[$key]['key'];
            if (isset($amounts[$key]) && $amounts[$key]['money'] > 0 && $actives[$key]['actives'] > 0) {
                $result[$key]['arpu'] = round($amounts[$key]['money'] / $actives[$key]['actives'], 2);
            } else {
                $result[$key]['arpu'] = 0;
            }
        }

        return $result;
    }

    /**
     * 按天 arppu
     *
     * @param string $by
     *
     * @return array
     */
    private function getArppuByDay($by = 'day')
    {
        if ($by == 'day') {
            $payed = $this->getPayedUsersByDay();
            $amounts = $this->getAmountByDay();
        } elseif ($by == 'two_weeks') {
            $payed = $this->getPayedUsersByDay('two_weeks');
            $amounts = $this->getAmountByDay('two_weeks');
        } elseif ($by == 'month') {
            $payed = $this->getPayedUsersByDay('month');
            $amounts = $this->getAmountByDay('month');
        }
        $result = [];
        foreach ($payed as $key => $value) {
            $result[$key]['day'] = $payed[$key]['day'];
            if (isset($amounts[$key]) && $amounts[$key]['money'] > 0 && $payed[$key]['payed'] > 0) {
                $result[$key]['arppu'] = round($amounts[$key]['money'] / $payed[$key]['payed'], 2);
            } else {
                $result[$key]['arppu'] = 0;
            }
        }

        return $result;
    }

    /**
     * 活跃用户平均付费
     */
    private function getAuAvgByDay($by = 'day')
    {
        if ($by == 'day') {
            $au = $this->getActivesByDay();
            $amounts = $this->getAmountByDay();
        } elseif ($by == 'two_weeks') {
            $au = $this->getActivesByDay('two_weeks');
            $amounts = $this->getAmountByDay('two_weeks');
        } elseif ($by == 'month') {
            $au = $this->getActivesByDay('month');
            $amounts = $this->getAmountByDay('month');
        }
        $result = [];
        foreach ($au as $key => $value) {
            $result[$key]['day'] = $au[$key]['day'];
            if (isset($amounts[$key]) && $amounts[$key]['money'] > 0 && $au[$key]['actives'] > 0) {
                $result[$key]['auAvg'] = round($amounts[$key]['money'] / $au[$key]['actives'], 2);
            } else {
                $result[$key]['auAvg'] = 0;
            }
        }

        return $result;
    }

    private function stepDays($to = null)
    {
        $tz = new \DateTimeZone('Asia/Chongqing');
        $from = Carbon::createFromFormat('Y-m-d', $this->from ?: $this->startFrom);
        $to = Carbon::createFromFormat('Y-m-d', $this->to ?: date('Y-m-d', time()));
        $interval = new DateInterval('P1D');
        $to->add($interval);
        $daterange = new DatePeriod($from, $interval, $to);

        return $daterange;
    }

    /**
     * 开服时间
     */
    private function getStartFrom()
    {
        if ($server = \Auth::user()->selected_server) {
            $startFrom = Server::find($server)->start_from;
        } else {
            $earliest = Statistic::select(DB::raw('DATE(StatTime) as day'))->first();
            $startFrom = $earliest ? $earliest->day : '';
        }

        return $startFrom;
    }

    private function ifExport($players)
    {
        if ($export = $this->export && $fetch = $this->fetch) {
            $result = [];
            switch ($fetch) {
                case 'acu-pcu':
                    $filename = 'ACU - PCU';
                    $acuList = $players['acu_by_day'];
                    foreach ($acuList as $key => $acu) {
                        $result[$key]['day'] = $acu['day'];
                        $result[$key]['acu'] = round($acu['acu'], 1) ?: "0";
                        $result[$key]['pcu'] = round($players['pcu_by_day'][$key]['pcu'], 1) ?: '0';
                    }
                    $title = ['时间', 'ACU', 'PCU'];
                    break;
                case 'login-register':
                    $filename = 'register - login';
                    foreach ($players['login_by_day'] as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['registers'] = $value['registers'];
                        $result[$key]['logins'] = $value['logins'];
                    }
                    $title = ['时间', '注册人数', '首次登陆人数'];
                    break;
                case 'pay-percent':
                    $filename = 'pay - persent';
                    foreach ($players['pur'] as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['pur'] = $value['payed'] . '%';
                    }
                    $title = ['时间', '付费率'];
                    break;
                case 'active':
                    $filename = 'active';
                    foreach ($players as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['old'] = $value['one'];
                        $result[$key]['new'] = $value['two'];
                    }
                    $title = ['时间', '全部玩家', '新玩家'];
                    break;
                case 'payed':
                    $filename = 'payed';
                    foreach ($players as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['payed'] = $value['payed'];
                    }
                    $title = ['时间', '付费人数'];
                    break;
                case 'incoming':
                    $filename = 'incoming';
                    foreach ($players as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['money'] = $value['money'];
                    }
                    $title = ['时间', '总收入'];
                    break;
                case 'arpu':
                    $filename = 'arpu';
                    foreach ($players as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['arpu'] = $value['arpu'];
                    }
                    $title = ['时间', '每用户平均收入'];
                    break;
                case 'arppu':
                    $filename = 'arppu';
                    foreach ($players as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['arppu'] = $value['arppu'];
                    }
                    $title = ['时间', '每付费用户平均收入'];
                    break;
                case 'au-avg':
                    $filename = 'au-avg';
                    foreach ($players as $key => $value) {
                        $result[$key]['day'] = $value['day'];
                        $result[$key]['auAvg'] = $value['auAvg'];
                    }
                    $title = ['时间', '活跃用户平均付费'];
                    break;
            }
            \Excel::create($filename, function ($excel) use ($result, $title) {
                $excel->sheet('Sheetname', function ($sheet) use ($result, $title) {
                    $sheet->fromArray($result, null, 'A1', true);
                    $sheet->row(1, $title);
                });
            })->export('xls');
        }
    }

    /**
     * 一个月内的用户终身价值计算。
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ltv(Request $request)
    {
        // 每天的用户留存率累加 * 每用户平均付费
        $menu = $this->menu;
        $arpu = $this->getArpuByDay();
        $ltv = [];
        foreach ($arpu as $key => $a) {

            $ltv[$key]['day'] = $a['day'];
            $ltv[$key]['ltv'] = $arpu[$key]['arpu'] * $this->dayTotalRetention($a['key']) / 100;
        }
        $monthRange = $this->monthRange();

        return view('remote.analyze.ltv', compact('menu', 'ltv', 'monthRange'));
    }

    /**
     * 每一天的未来所有用户留存率，用来计算用户终身价值。
     */
    private function dayTotalRetention($date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);
        $total = 0;
        foreach (static::retention($date) as $day) {
            $total += $day;
        }

        return $total;
    }

    private function monthRange()
    {
        $tz = new \DateTimeZone('Asia/Chongqing');
        $from = Carbon::now()->subYears(2);
        $to = Carbon::now();
        $interval = new DateInterval('P1M');
        $to->add($interval);
        $daterange = new DatePeriod($from, $interval, $to);

        return $daterange;
    }

    private static function retention_impl(Carbon $day , $after_days )
    {
        $date = $day->toDateString();
        $list_uid = Role::where(DB::raw('DATE(CreateTime)') , '=' ,$date )->pluck('UserID')->all();

        // $max_uid = Role::where(DB::raw('DATE(CreateTime)') , '=' ,$date )->orderby('UserID','desc')->list('UserID');
      //  $min_uid = Role::where(DB::raw('DATE(CreateTime)') , '=' ,$date )->orderby('UserID','asc')->first();
    //    if( !empty($max_uid) && !empty($min_uid) )
        if( !empty($list_uid) )
        {
            /*
            $max_uid = $max_uid->UserID;
            $min_uid = $min_uid->UserID;
            */
            $afterDays = clone $day;
            $afterDays = $afterDays->addDays( $after_days )->toDateString();

            $sql_after_day =  " '" . $afterDays . "' ";

            $uid_list_str = implode(",", $list_uid);

            // $sql =  "SELECT log_onlineinfo.UserID FROM log_onlineinfo WHERE DATE(log_onlineinfo.LoginTime) = $sql_after_day and log_onlineinfo.UserID >= $min_uid and log_onlineinfo.UserID <= $max_uid GROUP BY log_onlineinfo.UserID";
             $sql =  "SELECT log_onlineinfo.UserID FROM log_onlineinfo WHERE DATE(log_onlineinfo.LoginTime) = $sql_after_day and UserID IN($uid_list_str) GROUP BY log_onlineinfo.UserID";


            $result = DB::select(DB::raw($sql));
           
            
            /*
             * SELECT COUNT(log_onlineinfo.UserID) FROM log_onlineinfo WHERE DATE(log_onlineinfo.LoginTime) = '2016-06-30' and log_onlineinfo.UserID > 1 and log_onlineinfo.UserID <= 10000 GROUP BY log_onlineinfo.UserID
             */
//            $day2_result = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)->
//            where('UserID','>=', $min_uid)->
//            where('UserID','<=',1000000)->groupBy('UserID')->pluck("UserID")->count();
            $day2_count = count($result);
            //$ret_day = $day2_count / ($max_uid - $min_uid);
            $ret_day = $day2_count / count($list_uid);
        }
        else
        {
            $ret_day = 0;
        }
        return $ret_day;
    }

    /**
     * 返回某一天的未来一系列时间的留存率
     *
     * @param \Carbon\Carbon $day
     *
     * @return array
     */
    public static function retention(Carbon $day)
    {
        $date = $day->toDateString();
        $day1 = self::retention_impl( $day ,  1 );
        $day2 = self::retention_impl( $day ,  2 );
        $day3 = self::retention_impl( $day ,  3 );
        $day4 = self::retention_impl( $day ,  4 );
        $day5 = self::retention_impl( $day ,  5 );
        $day6 = self::retention_impl( $day ,  6 );
        $day7 = self::retention_impl( $day ,  7 );
        $day14 = self::retention_impl( $day ,  14 );
        $day30 = self::retention_impl( $day ,  30 );


        $list['day1'] = (float)$day1 * (float)100;
        $list['day2'] = (float)$day2 * (float)100;
        $list['day3'] = (float)$day3 * (float)100.0;
        $list['day4'] = (float)$day4 * (float)100.0;
        $list['day5'] = (float)$day5 * (float)100.0;
        $list['day6'] = (float)$day6 * (float)100.0;
        $list['day7'] = (float)$day7 * (float)100.0;
        $list['day14'] = (float)$day14 * (float)100.0;
        $list['day30'] = (float)$day30 * (float)100.0;

        /*
        $list = compact('day2', 'day7', 'day31');
        foreach ($list as $key => $daily) {
            $list[$key] = round(100, 1);
        }
        */
        return $list;
    }

    /**
     * 返回某一天的未来一系列时间的留存率去除 ip 重复
     *
     * @param \Carbon\Carbon $day
     *
     * @return array
     */
    public static function retentionFilterIp(Carbon $day)
    {
        $date = $day->toDateString();
        $today = LoginLog::where(DB::raw('DATE(LoginTime)'), $date)->count('UserID');
        $afterDays = clone $day;
        $afterDays = $afterDays->addDay()->toDateString();
        $day2 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(2)->toDateString();

        $day3 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(3)->toDateString();

        $day4 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(4)->toDateString();
        $day5 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(5)->toDateString();
        $day6 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(6)->toDateString();
        $day7 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(7)->toDateString();
        $day8 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                        ->whereIn('UserID', function ($query) use ($date) {
                            $query->from(DB::raw('log_onlineinfo'));
                            $query->where(DB::raw('DATE(LoginTime)'), $date);
                            $query->select('UserID');
                            $query->get();
                        })
                        ->groupBy(DB::raw('UserID, LoginIp'))
                        ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(15)->toDateString();
        $day16 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                         ->whereIn('UserID', function ($query) use ($date) {
                             $query->from(DB::raw('log_onlineinfo'));
                             $query->where(DB::raw('DATE(LoginTime)'), $date);
                             $query->select('UserID');
                             $query->get();
                         })
                         ->groupBy(DB::raw('UserID, LoginIp'))
                         ->count(DB::raw('DISTINCT(UserID)'));
        $afterDays = clone $day;
        $afterDays = $afterDays->addDays(30)->toDateString();

        $day31 = LoginLog::where(DB::raw('DATE(LoginTime)'), '=', $afterDays)
                         ->whereIn('UserID', function ($query) use ($date) {
                             $query->from(DB::raw('log_onlineinfo'));
                             $query->where(DB::raw('DATE(LoginTime)'), $date);
                             $query->select('UserID');
                             $query->get();
                         })
                         ->groupBy(DB::raw('UserID, LoginIp'))
                         ->count(DB::raw('DISTINCT(UserID)'));
        $list = compact('day2', 'day3', 'day4', 'day5', 'day6', 'day7', 'day8', 'day16', 'day31');
        foreach ($list as $key => $daily) {
            $list[$key] = $today ? round(100 * $daily / $today, 1) : 0;
        }

        return $list;
    }
}
