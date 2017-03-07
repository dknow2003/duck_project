<?php

namespace App\Http\Controllers\Remote\Expense;

use App\Entities\Financial\Order;
use App\Entities\Game\Currency;
use App\Entities\Game\LoginLog;
use App\Entities\Game\Player;
use App\Entities\Game\Role;
use App\Menu\Menu;
use App\Menu\MenuPresenter;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class ExpenseController extends Controller
{
    private $payAmountRange = [
        '20 元以下'      => [0, 20],
        '20 - 50 元'   => [20, 50],
        '50 - 100 元'  => [50, 100],
        '100 - 200 元' => [100, 200],
        '200 - 500 元' => [200, 500],
        '500 元以上'     => [500, 999999],
    ];

    private $gameDatabaseName;

    private $month;

    public function __construct(Request $request)
    {
        parent::__construct(new MenuPresenter(new Menu));
        $this->month = $request->get('month') ? str_replace(['年', '月', '日'], ['-', '', ''], $request->get('month')) : null;
        $this->gameDatabaseName = Order::getDefaultConnections()[1]['database'];
    }

    public function dailyTotal(Request $request)
    {
        $menu = $this->menu;
        $expenseByDay = Order::orderBy('createTime', 'ASC')
                             ->select(DB::raw('SUM(orderMoney) as money, CONCAT(DATE_FORMAT(createTime,\'%m\'), \'-\', DAY(createTime)) as date, DATE_FORMAT(createTime,\'%Y-%m\')as month, DAY(createTime) as day'));
        $month = $this->getFromAndTo();

        $expenseByDay->where('createTime', '>=', $month[0]);
        $expenseByDay->where('createTime', '<', $month[1]);

        $expenseByDay = $expenseByDay->groupBy(DB::raw('DATE(createTime)'))->get()->keyBy('date');
        //dd($this->month);
        // make date range
        $dateRange = $this->dateRange();
        // fill datarang
        $result = [];
        foreach ($dateRange as $key => $date) {
            $result[$key]['date'] = $date->format('m-d');
            $result[$key]['money'] = isset($expenseByDay[$date->format('m-d')]) ? $expenseByDay[$date->format('m-d')]->money : 0;
        }

        $monthRange = $this->monthRange();

        return view('remote.expenses.daily-total', compact('menu', 'result', 'dateRange', 'monthRange'));
    }

    public function hours(Request $request)
    {
        $menu = $this->menu;
        $totalByHour = Order::select(DB::raw('SUM(orderMoney) as money, HOUR(createTime) as hour'))
                            ->groupBy(DB::raw('HOUR(createTime)'))
                            ->get()
                            ->keyBy('hour');
        $result = [];
        foreach (range(0, 23) as $key => $hour) {
            $result[$key]['money'] = isset($totalByHour[$key]) ? $totalByHour[$key]['money'] : 0;
        }

        return view('remote.expenses.hours', compact('menu', 'result'));
    }

    public function summarize(Request $request)
    {
        $menu = $this->menu;
        $orderTotal = Order::sum('orderMoney');
        $totalRecharge = $orderTotal ?: 0;
        $expenseTotal = Currency::where('OperType', 2)->where('MoneyType', 3)->sum('MoneyChanged');
        $totalExpense = $expenseTotal ?: 0;
        // 开服天数

        if ($firstDay = LoginLog::orderBy('LoginTime', 'ASC')->first()) {
            $tz = new \DateTimeZone('Asia/Chongqing');
            $first = Carbon::createFromFormat('Y-m-d H:i:s', $firstDay->LoginTime, $tz);
            $today = Carbon::now();
            $days = $today->diffInDays($first);
            $avgExpenseByDay = $days ? round($expenseTotal / $days, 0) : 0;
        } else {
            $avgExpenseByDay = 0;
        }

        return view('remote.expenses.summarize', compact('menu', 'totalRecharge', 'totalExpense', 'avgExpenseByDay'));
    }

    public function payDetail(Request $request)
    {
        $menu = $this->menu;

        $orders = (new Order())->newQuery();

        if ($aid = $request->get('aid')) {
            $orders->where('aid', $aid);
        } elseif ($serial = $request->get('serial')) {
            $orders->where('orderSerial', $serial);
        }

        $orders->with('user');
        $orders = $orders->paginate(20);

        return view('remote.expenses.pay-detail', compact('menu', 'orders'));
    }

    public function roleExpense(Request $request)
    {
        $menu = $this->menu;

        $orders = (new Currency())->newQuery();
        if ($roleId = trim($request->get('role_id'))) {
            $orders->where('UserID', trim($roleId));
        } elseif ($roleName = trim($request->get('role_name'))) {
            $roles = Role::where('RoleName', 'LIKE', "%{$roleName}%")->get()->pluck('UserID');
            $orders->whereIn('UserID', $roles);
        }

        $orders = $orders->where('OperType', 2)
                         ->where('MoneyType', 3)
                         ->orderBy('LogTime', 'DESC')
                         ->with('role')
                         ->paginate(20);

        return view('remote.expenses.role-expense', compact('menu', 'orders'));
    }

    public function range(Request $request)
    {
        $menu = $this->menu;

        $result = [];
        foreach ($this->payAmountRange as $key => $value) {
            $count = Order::where('orderMoney', '>', $value[0])
                          ->where('orderMoney', '<=', $value[1])
                          ->count('aid');
            $result[$key]['count'] = $count ?: 0;
            $result[$key]['range'] = $key;
            $result[$key]['color'] = $this->randomColor();
        }

        return view('remote.expenses.range', compact('menu', 'result'));
    }

    public function payRate(Request $request)
    {
        $userTable = DB::raw("{$this->gameDatabaseName}.usr_userinfo user");
        $menu = $this->menu;
        // form and to
        $month = $this->getFromAndTo();
        // select option range for two years.
        $monthRange = $this->monthRange();
        $dateRange = $this->dateRange();
        $month = $this->getFromAndTo();
        DB::enableQueryLog();
        // 每天注册人数
        $newByDay = Player::from(DB::raw('usr_userinfo main'))
                          ->where('RegTime', '>=', $month[0])
                          ->where('RegTime', '<', $month[1])
                          ->select(DB::raw('COUNT(UserID) as count, DATE(Regtime) as day'))
                          ->groupBy(DB::raw('DATE(RegTime)'))
                          ->get();
        // 老玩家付费人数
        $oldPayByDay = Order::from(DB::raw('gms_order orders'))
                            ->select(DB::raw('COUNT(DISTINCT(orders.aid)) as count, DATE(orders.createTime) as day'))
                            ->where('orders.createTime', '>=', $month[0])
                            ->where('orders.createTime', '<', $month[1])
                            ->whereIn('orders.aid', function ($query) use ($userTable) {
                                $query->select('user.UserID');
                                $query->from(DB::raw($userTable));
                                $query->where(DB::raw('user.RegTime'), '<', DB::raw('DATE(orders.createTime)'));
                            })
                            ->groupBy(DB::raw('DATE(orders.createTime)'))
                            ->get()->keyBy('day');
        // 新玩家付费人数
        $newPayByDay = Order::from(DB::raw('gms_order orders'))
                            ->select(DB::raw('COUNT(DISTINCT(orders.aid)) as count, DATE(orders.createTime) as day'))
                            ->where('orders.createTime', '>=', $month[0])
                            ->where('orders.createTime', '<', $month[1])
                            ->whereNotIn('orders.aid', function ($query) use ($userTable) {
                                $query->select('user.UserID');
                                $query->from(DB::raw($userTable));
                                $query->where(DB::raw('user.RegTime'), '<', DB::raw('DATE(orders.createTime)'));
                            })
                            ->groupBy(DB::raw('DATE(orders.createTime)'))
                            ->get()->keyBy('day');

        // 新玩家人数
        $newByDay = Player::where('RegTime', '>=', $month[0])
                          ->where('RegTime', '<', $month[1])
                          ->select(DB::raw('COUNT(UserID) as new, DATE(RegTime) as day'))
                          ->groupBy(DB::raw('DATE(RegTime)'))
                          ->get()
                          ->keyBy('day');

        // 通过新玩家计算老玩家
        $old = 0;
        $newByDay->map(function ($item) use (&$old) {
            $item->old = $old;
            $old = $item->new + $old;
        });
        $userByDay = $newByDay;
        // 结果集
        $result = [];
        foreach ($dateRange as $d) {
            $key = $d->format('Y-m-d');
            $result[$key]['day'] = $d->format('m-d');
            $old = isset($userByDay[$key]) ? $userByDay[$key]['old'] : 0;
            $new = isset($userByDay[$key]) ? $userByDay[$key]['new'] : 0;
            $oldPay = isset($oldPayByDay[$key]) ? $oldPayByDay[$key]['count'] : 0;
            $newPay = isset($newPayByDay[$key]) ? $newPayByDay[$key]['count'] : 0;
            $result[$key]['old'] = $old ? round(100 * ($oldPay / $old), 1) : 0;
            $result[$key]['new'] = $new ? round(100 * ($newPay / $new), 1) : 0;
        }

        return view('remote.expenses.pay-rate', compact('menu', 'monthRange', 'dateRange', 'result'));
    }

    /**
     * 制作开服时间
     */
    private function dateRange()
    {
        $month = $this->getFromAndTo();
        $tz = new \DateTimeZone('Asia/Chongqing');
        $from = Carbon::createFromFormat('Y-m-d', $month[0], $tz);
        $to = Carbon::createFromFormat('Y-m-d', $month[1], $tz);

        $interval = new DateInterval('P1D');
        $to->add($interval);
        $daterange = new DatePeriod($from, $interval, $to);

        return $daterange;
    }

    /**
     * 获取最近一个月或指定月份的区间
     */
    private function getFromAndTo()
    {
        if ($this->month) {
            $month[0] = date('Y-m-d', strtotime($this->month));
            $month[1] = date('Y-m-d', strtotime($this->month . ' +1 month -1 day'));
        } else {
            $month[0] = date('Y-m-d', strtotime(date('Y-m-d') . ' -1 month'));
            $month[1] = date('Y-m-d');
        }

        return $month;
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

    /**
     * return a random hex color
     */
    private function randomColor()
    {
        $rand = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
        $color = '#' . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)];

        return $color;
    }
}
