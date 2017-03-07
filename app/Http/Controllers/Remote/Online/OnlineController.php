<?php

namespace App\Http\Controllers\Remote\Online;

use App\Entities\Game\LoginLog;
use App\Entities\Game\Player;
use App\Entities\Game\Role;
use App\Entities\Game\Statistic;
use App\Http\Controllers\Remote\Analyze\AnalyzeController;
use App\Server;
use Auth;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTimeZone;
use DB;
use Excel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Factory;

class OnlineController extends Controller
{
    public    $retentionNameMap = [
        'day1'  => '次日留存率',
        'day2'  => '2日后留存率',
        'day3'  => '3日后留存率',
        'day4'  => '4日后留存率',
        'day5'  => '5日后留存率',
        'day6'  => '6日后留存率',
        'day7'  => '周留存率',
        'day14' => '2周留存率',
        'day30' => '月留存率',
    ];

    protected $from;

    private   $export;

    public function trending(Request $request)
    {

 $from = $this->from = $this->makeFromAccordingBy($request);
        $menu = $this->menu;

        /** @var string $by 按什么粒度查看 */
        if (!$by = $request->get('by')) {
            $by = 'month';
        }
        $stats = Statistic::where('StatType', 2)
                          ->orderBy('StatTime', 'ASC')
            /** @var Carbon $from */
                          ->where('StatTime', '>=', $from->toDateString());
        switch ($by) {
            case 'hour':
                $stats = $stats->select(DB::raw('CONCAT(DAYOFMONTH(StatTime), \' 日 \', HOUR(StatTime), \' 时\') as x, StatNum as avg'))
                               ->groupBy(DB::raw('CONCAT(DAYOFMONTH(StatTime), HOUR(StatTime))'))
                               ->take(48)
                               ->get();
                break;
            case 'day':
                $stats = $stats->select(DB::raw('CONCAT(MONTH(StatTime), \' 月 \', DAY(StatTime), \' 日\') as x, SUM(StatNum) / COUNT(StatTime)  as avg'))
                               ->groupBy(DB::raw('DAY(StatTime)'))
                               ->take(32)
                               ->get();
                break;
            case 'week':
                $stats = $stats->select(DB::raw('CONCAT("第 ", WEEKOFYEAR(StatTime), " 周") as x, SUM(StatNum) / COUNT(StatTime)  as avg'))
                               ->groupBy(DB::raw('WEEKOFYEAR(StatTime)'))
                               ->take(10)
                               ->get();
                break;
            case 'month':
                $stats = $stats->select(DB::raw('CONCAT(YEAR(StatTime), \' 年 \', MONTH(StatTime), \' 月\') as x, SUM(StatNum) / COUNT(StatTime)  as avg'))
                               ->groupBy(DB::raw('MONTH(StatTime)'))
                               ->take(12)
                               ->get();
                break;
        }
        $stats = $this->polishingDate($stats, $by);

        return view('remote.online.trending-index', compact('menu', 'stats'));
    }

    /**
     * 补齐数据库中不存在的时间。
     *
     * @param mixed  $stats
     * @param string $by
     *
     * @return array
     */
    private function polishingDate($stats, $by = 'month')
    {
        $stats = $stats->keyBy('x');
        $dateRange = $this->makeDateRangeFromStartDate($by, $this->from);
        $result = [];
        foreach ($dateRange as $key => $atom) {
            switch ($by) {
                case 'month':
                case 'month':
                    $resultKeyFormat = 'Y 年 n 月';
                    break;
                case 'week':
                    $resultKeyFormat = '第 W 周';
                    break;
                case 'day':
                    $resultKeyFormat = 'n 月 j 日';
                    break;
                case 'hour':
                    $resultKeyFormat = 'j 日 G 时';
                    break;
                default:
                    $resultKeyFormat = 'Y 年 n 月';
                    break;
            }
            /** @var \DateTime $atom */

            $atom->setTimezone(new \DateTimeZone('Asia/Chongqing'));
            $atomDate = $atom->format($resultKeyFormat);
            $result[$key]['x'] = $atomDate;
            $result[$key]['avg'] = isset($stats[$atomDate]) ? $stats[$atomDate]['avg'] : 0;
        }

        return $result;
    }

    /**
     * 从开始时间，构造一个时间段。
     *
     * @param        $by
     * @param Carbon $from
     *
     * @return \DatePeriod
     */
    private function makeDateRangeFromStartDate($by, $from)
    {
	$tz = new \DateTimeZone('Asia/Chongqing');
        $to = Carbon::now($tz);
        switch ($by) {
            case 'week':
                $start = $from ?: Carbon::now($tz)->startOfDay()->subWeeks(10);
                $cloneStart = clone $start;
                $to = $start->diff($to)->m > 2 ? $cloneStart->addMonth(2) : $to;
                break;
            case 'day':
                $start = $from ?: Carbon::now($tz)->subMonth();
                $cloneStart = clone $start;
                $to = $start->diff($to)->m > 1 ? $cloneStart->addMonth() : $to;
                break;
            case 'hour':
                $start = $from ?: Carbon::now($tz)->subDay();
                $cloneStart = clone $start;
                $to = $start->diff($to)->days > 1 ? $cloneStart->addDay() : $to;
                break;
            case 'month':
            default:
                $start = $from ?: Carbon::now($tz)->subYear();
                $cloneStart = clone $start;
                $to = $start->diff($to)->y > 1 ? $cloneStart->addYear() : $to;
                break;
        }

        return $this->makeTimeStep($start, $to, $by);
    }

    /**
     * 根据开始时间，结束时间，步长，构造时间区间。
     *
     * @param \Carbon\Carbon $from
     * @param \Carbon\Carbon $to
     * @param string         $by
     *
     * @return \DatePeriod
     */
    private function makeTimeStep(Carbon $from, Carbon $to, $by = 'month')
    {
        switch ($by) {
            case 'month':
                $interval = new DateInterval('P1M');
                $from = $from->firstOfMonth();
                $to = $to->firstOfMonth();
                break;
            case 'week':
                $interval = new DateInterval('P1W');
                break;
            case 'day':
                $interval = new DateInterval('P1D');
                break;
            case 'hour':
                $interval = new DateInterval('PT1H');
                break;
            default:
                $interval = new DateInterval('P1M');
        }
        $to->add($interval);
        $daterange = new DatePeriod($from, $interval, $to);

        return $daterange;
    }

    public function newPlayers(Request $request)
    {
        $menu = $this->menu;

        /** @var string $by 按什么粒度查看 */
        if (!$fetch = $request->get('fetch')) {
            $fetch = 'all';
        }

        $newPlayers = Player::orderBy('RegTime', 'ASC');
        // 默认渲染整个历史的
        if ($fetch == 'all') {
            $newPlayers = $this->buildNewPlayersFetchAll($newPlayers)->get();
        } else {
            // 默认 take
            // 按特定时间段
            $lastPlayers = Player::orderBy('RegTime', 'DESC')->first();
            switch ($fetch) {
                case 'day':
                    $newPlayers = $newPlayers->where('RegTime', '>', DB::raw('DATE_SUB(\'' . $lastPlayers->RegTime . '\', INTERVAL 1 DAY)'))
                                             ->select(DB::raw('CONCAT(DAY(RegTime), \' 日 \', HOUR(RegTime), \' 时\') as x, COUNT(UserID) as avg'))
                                             ->groupBy(DB::raw('HOUR(RegTime)'))
                                             ->take(24)
                                             ->get();
                    break;
                case 'week':
                    $newPlayers = $newPlayers->where('RegTime', '>', DB::raw('DATE_SUB(\'' . $lastPlayers->RegTime . '\', INTERVAL 7 DAY)'))
                                             ->select(DB::raw('CONCAT(\'星期 \', WEEKDAY(RegTime) + 1) as x, COUNT(UserID) as avg'))
                                             ->groupBy(DB::raw('WEEKDAY(RegTime) + 1'))
                                             ->take(7)
                                             ->get();
                    break;
                case 'month':
                    $newPlayers = $newPlayers->where('RegTime', '>', DB::raw('DATE_SUB(\'' . $lastPlayers->RegTime . '\', INTERVAL 1 MONTH)'))
                                             ->select(DB::raw('CONCAT(MONTH(RegTime), \' 月 \', DAY(RegTime), \' 日\') as x, COUNT(UserID) as avg'))
                                             ->groupBy(DB::raw('DAYOFYEAR(RegTime)'))
                                             ->take(31)
                                             ->get();
                    break;
                    break;
                case 'year':
                    $newPlayers = $newPlayers->where('RegTime', '>', DB::raw('DATE_SUB(\'' . $lastPlayers->RegTime . '\', INTERVAL 1 YEAR)'))
                                             ->select(DB::raw('CONCAT(YEAR(RegTime), \' 年 \', MONTH(RegTime), \' 月\') as x, COUNT(UserID) as avg'))
                                             ->groupBy(DB::raw('MONTH(RegTime)'))
                                             ->take(12)
                                             ->get();
                    break;
                default:
                    $newPlayers = $this->buildLoginsFetchAll($newPlayers)->get();
                    break;
            }
        }

        return view('remote.online.new-players-index', compact('menu', 'newPlayers'));
    }

    public function login(Request $request)
    {
        $menu = $this->menu;

        /** @var string $by 按什么粒度查看 */
        if (!$fetch = $request->get('fetch')) {
            $fetch = 'all';
        }

        $logins = LoginLog::orderBy('LoginTime', 'ASC');
        // 默认渲染整个历史的
        if ($fetch == 'all') {
            $logins = $this->buildLoginsFetchAll($logins)->get();
        } else {
            // 默认 take
            // 按特定时间段
            $lastLogin = LoginLog::orderBy('LoginTime', 'DESC')->first();
            switch ($fetch) {
                case 'day':
                    $logins = $logins->where('LoginTime', '>', DB::raw('DATE_SUB(\'' . $lastLogin->LoginTime . '\', INTERVAL 1 DAY)'))
                                     ->select(DB::raw('CONCAT(DAY(LoginTime), \' 日 \', HOUR(LoginTime), \' 时\') as x, COUNT(id) as avg'))
                                     ->groupBy(DB::raw('HOUR(LoginTime)'))
                                     ->take(24)
                                     ->get();
                    break;
                case 'week':
                    $logins = $logins->where('LoginTime', '>', DB::raw('DATE_SUB(\'' . $lastLogin->LoginTime . '\', INTERVAL 7 DAY)'))
                                     ->select(DB::raw('CONCAT(\'星期 \', WEEKDAY(LoginTime) + 1) as x, COUNT(id) as avg'))
                                     ->groupBy(DB::raw('WEEKDAY(LoginTime) + 1'))
                                     ->take(7)
                                     ->get();
                    break;
                case 'month':
                    $logins = $logins->where('LoginTime', '>', DB::raw('DATE_SUB(\'' . $lastLogin->LoginTime . '\', INTERVAL 1 MONTH)'))
                                     ->select(DB::raw('CONCAT(MONTH(LoginTime), \' 月 \', DAY(LoginTime), \' 日\') as x, COUNT(id) as avg'))
                                     ->groupBy(DB::raw('DAYOFYEAR(LoginTime)'))
                                     ->take(31)
                                     ->get();
                    break;
                    break;
                case 'year':
                    $logins = $logins->where('LoginTime', '>', DB::raw('DATE_SUB(\'' . $lastLogin->LoginTime . '\', INTERVAL 1 YEAR)'))
                                     ->select(DB::raw('CONCAT(YEAR(LoginTime), \' 年 \', MONTH(LoginTime), \' 月\') as x, COUNT(id) as avg'))
                                     ->groupBy(DB::raw('MONTH(LoginTime)'))
                                     ->take(12)
                                     ->get();
                    break;
                    break;
                default:
                    $logins = $this->buildLoginsFetchAll($logins)->get();
                    break;
            }
        }

        return view('remote.online.login-index', compact('menu', 'logins'));
    }

    /**
     * 开服时间
     */
    private function getStartFromOrMonthAgo()
    {
        if ($server = Auth::user()->selected_server) {
            $startFrom = Server::find($server)->start_from;
            $startFrom = Carbon::createFromFormat('Y-m-d', $startFrom);
        } else {
            $startFrom = Statistic::select('StatTime')->orderBy('StatTime', 'ASC')->first();
            if (!$startFrom) {
                $startFrom = Carbon::now()->subMonth();
            } else {
                $startFrom = $startFrom->StatTime;
            }
        }

        return $startFrom;
    }

    private function buildTrendingFetchAll($stats)
    {
        // 由于历史数据多少是不确定的，首先要计算得到默认情况下横轴单位。
        // 我们通过取得最早的和最新的日志时间，判断开服时间长短，然后根据不同的开服时长
        // 来选择不同的查询粒度。
        /** @var Carbon $firstStat */
        //$firstStat = $this->getStartFromOrMonthAgo();
        $firstStat = Statistic::orderBy('StatTime', 'ASC')->first();
        $lastStat = Statistic::orderBy('StatTime', 'DESC')->first();
        /** @var \DateInterval $statTimeRange */
        $statTimeRange = $lastStat->StatTime->diff($firstStat->StatTime);

        // 开服一天内， x 轴按小时
        if (1 >= $statTimeRange->days) {
            $stats->select(DB::raw('CONCAT(DAY(StatTime), \' 日 \', HOUR(StatTime), \' 时\') as x, SUM(StatNum) / COUNT(StatTime)  as avg'))
                  ->groupBy(DB::raw('HOUR(StatTime)'));
        } elseif (1 < $statTimeRange->days && 31 >= $statTimeRange->days) {
            // 开服一天以上，一个月以下， x 轴按天。
            $stats->select(DB::raw('CONCAT(MONTH(StatTime), \' 月 \', DAY(StatTime), \' 日\') as x, SUM(StatNum) / COUNT(StatTime)  as avg'))
                  ->groupBy(DB::raw('DAYOFYEAR(StatTime)'));
        } elseif (31 < $statTimeRange->days && 365 >= $statTimeRange->days) {
            // 开服一个月以上，一年以下，x 轴按周。
            $stats->select(DB::raw('CONCAT(weekofyear(StatTime), \' 周\') as x , sum(StatNum) / count(StatTime)  as avg'));
            $stats->groupBy(DB::raw('weekofyear(StatTime)'));
        } else {
            // 开服一年以上，x 轴按月
            $stats->select(DB::raw('CONCAT(YEAR(StatTime), \' 年 \', MONTH(StatTime), \' 月\') as x , sum(StatNum) as total, count(StatTime) as count, sum(StatNum) / count(StatTime)  as avg'));
            $stats->groupBy(DB::raw('month(StatTime)'));
        }

        return $stats;
    }

    private function buildLoginsFetchAll($logins)
    {
        $firstLogin = LoginLog::orderBy('LoginTime', 'ASC')->first();
        $lastLogin = LoginLog::orderBy('LoginTime', 'DESC')->first();
        /** @var \DateInterval $statTimeRange */
        $loginTimeRange = $lastLogin->LoginTime->diff($firstLogin->LoginTime);

        // 开服一天内， x 轴按小时
        if (1 >= $loginTimeRange->days) {
            $logins->select(DB::raw('CONCAT(DAY(LoginTime), \' 日 \', HOUR(LoginTime), \' 时\') as x, COUNT(DISTINCT(UserID)) as avg'))
                   ->groupBy(DB::raw('HOUR(LoginTime)'));
        } elseif (1 < $loginTimeRange->days && 31 >= $loginTimeRange->days) {
            // 开服一天以上，一个月以下， x 轴按天。
            $logins->select(DB::raw('CONCAT(MONTH(LoginTime), \' 月 \', DAY(LoginTime), \' 日\') as x,  COUNT(DISTINCT(UserID)) as avg'))
                   ->groupBy(DB::raw('DAYOFYEAR(LoginTime)'));
        } elseif (31 < $loginTimeRange->days && 365 >= $loginTimeRange->days) {
            // 开服一个月以上，一年以下，x 轴按周。
            $logins->select(DB::raw('CONCAT(weekofyear(LoginTime), \' 周\') as x ,  COUNT(DISTINCT(UserID)) as avg'));
            $logins->groupBy(DB::raw('weekofyear(LoginTime)'));
        } else {
            // 开服一年以上，x 轴按月
            $logins->select(DB::raw('CONCAT(YEAR(LoginTime), \' 年 \', MONTH(LoginTime), \' 月\') as x ,  COUNT(DISTINCT(UserID)) as avg'));
            $logins->groupBy(DB::raw('month(LoginTime)'));
        }

        return $logins;
    }

    private function buildNewPlayersFetchAll($newPlayers)
    {
        $firstPlayer = Player::orderBy('RegTime', 'ASC')->first();
        $lastPlayer = Player::orderBy('RegTime', 'DESC')->first();
        /** @var \DateInterval $statTimeRange */
        $regTimeRange = $lastPlayer->RegTime->diff($firstPlayer->RegTime);

        // 开服一天内， x 轴按小时
        if (1 >= $regTimeRange->days) {
            $newPlayers->select(DB::raw('CONCAT(DAY(RegTime), \' 日 \', HOUR(RegTime), \' 时\') as x, COUNT(UserID) as avg'))
                       ->groupBy(DB::raw('HOUR(RegTime)'));
        } elseif (1 < $regTimeRange->days && 31 >= $regTimeRange->days) {
            // 开服一天以上，一个月以下， x 轴按天。
            $newPlayers->select(DB::raw('CONCAT(MONTH(RegTime), \' 月 \', DAY(RegTime), \' 日\') as x,  COUNT(UserID) as avg'))
                       ->groupBy(DB::raw('DAYOFYEAR(RegTime)'));
        } elseif (31 < $regTimeRange->days && 365 >= $regTimeRange->days) {
            // 开服一个月以上，一年以下，x 轴按周。
            $newPlayers->select(DB::raw('CONCAT(weekofyear(RegTime), \' 周\') as x ,  COUNT(UserID) as avg'));
            $newPlayers->groupBy(DB::raw('weekofyear(RegTime)'));
        } else {
            // 开服一年以上，x 轴按月
            $newPlayers->select(DB::raw('CONCAT(YEAR(RegTime), \' 年 \', MONTH(RegTime), \' 月\') as x ,  COUNT(UserID) as avg'));
            $newPlayers->groupBy(DB::raw('month(RegTime)'));
        }

        return $newPlayers;
    }

    public function current(Request $request)
    {
        $menu = $this->menu;
        $stat = Statistic::orderBy('StatTime', 'DESC')->where('StatType', 2)->first();
        $currentOnline = $stat ? $stat->StatNum : 0;
        $stat = Statistic::orderBy('StatTime', 'DESC')->where('StatType', 1)->first();
        $currentLogin = $stat ? $stat->StatNum : 0;
        // 2.角色/账号创建统计 （账号和角色个数比）
        $roleCount = Role::count('UserID');
        $userCount = Player::count('UserID');

        return view('remote.online.current', compact('menu', 'currentOnline', 'currentLogin', 'roleCount', 'userCount'));
    }

    public function retention(Request $request)
    {
        $menu = $this->menu;
        // 获取记录的最早时间
        $earliest = Statistic::select(DB::raw('DATE(StatTime) as day'))->first();
        $startFrom = $earliest ? $earliest->day : '';
        $this->export = $request->has('export');

        // 如果设置开服时间，记录最早时间就是开服时间
        if ($server = Auth::user()->selected_server) {
            $serverStart = Server::find($server)->start_from;
            if ('0000-00-00' != $serverStart) {
                $startFrom = $serverStart;
            }
        }

        // 如果用户设置了时间就改为设置的
        $day = $request->get('day') ?: $startFrom;
        $date = Carbon::createFromFormat('Y-m-d', $day, new DateTimeZone('Asia/Chongqing'))->startOfDay();

        // 取未来 10 天留存率
        $today = Carbon::today(new DateTimeZone('Asia/Chongqing'));
        $diff = $today->diffInDays($date) != 0 ?: 1;
        $retentions = [];
        for ($i = 0; $i < ($diff >= 10 ? 10 : $diff); $i++) {
            $newDay = clone $date;
            $splitDays = $newDay->addDays($i);;
            $retentions[$splitDays->toDateString()] = AnalyzeController::retention($splitDays);
        }
        $result = [];
        foreach ($retentions as $key => $day) {
            foreach ($day as $oneDayKey => $oneDay) {
                $result[$key][$oneDayKey]['day'] = $this->retentionNameMap[$oneDayKey];
                $result[$key][$oneDayKey]['au'] = $oneDay;
            }
        }
        $this->checkRetentionExportToEmail($result);

        return view('remote.online.retention-index', compact('menu', 'result', 'date'));
    }

    private function makeFromAccordingBy(Request $request)
    {
	$tz = new \DateTimeZone('Asia/Chongqing');
        $by = $request->get('by') ?: 'month';
        $from = $request->get('from') ?: '';
        switch ($by) {
            case 'month':
                return $from ? Carbon::createFromFormat('Y-m-d', $from, $tz)->firstOfMonth() : Carbon::now()->subYear()->firstOfMonth();
                break;
            case 'week':
                return $from ? Carbon::createFromFormat('Y-m-d', $from, $tz) : Carbon::now()->startOfDay()->subWeeks(10);
                break;
            case 'day':
                return $from ? Carbon::createFromFormat('Y-m-d', $from, $tz) : Carbon::now()->subMonth();
                break;
            case 'hour':
                return $from ? Carbon::createFromFormat('Y-m-d', $from, $tz) : Carbon::now()->subDay();
                break;
        }
    }

    public function checkRetentionExportToEmail($retentions)
    {
        // 拆成二维数组。
        $result = [];
        foreach ($retentions as $key => $value) {
            $result[$key][] = $key;
            foreach ($value as $k => $value) {
                $result[$key][] = $value['au'];
            }
        }
        if ($this->export) {
            $filename = 'retention-rate-' . array_keys($result)[0];
            $title = [
                '时间', '次日留存率', '2 日后留存率', '3 日后留存率',
                '4 日后留存率', '5 日后留存率', '6 日后留存率', '周留存率', '2 周留存率', '月留存率',
            ];
            // 如果导出到邮箱
            $emailVerify = app(Factory::class)->make($request = \Request::all(), ['email' => 'required|email']);
            // 格式不正确
            if (\Request::has('to_email') && $emailVerify->fails()) {
                return back()->with('flash_message', '邮箱格式不正确');
            } elseif (\Request::has('to_email') && !$emailVerify->fails()) {
                Excel::create($filename, function ($excel) use ($result, $title) {
                    $excel->sheet('Sheetname', function ($sheet) use ($result, $title) {
                        $sheet->fromArray($result, null, 'A1', true);
                        $sheet->row(1, $title);
                    });
                })->save('xls');
                // send email
                $email = $request['email'];
                Mail::raw("附件[{$filename}]中包含了留存率信息。", function ($message) use ($email, $filename) {
                    $message->to($email);
                    $message->subject("留存率表单导出[{$filename}]。");
                    $message->attach(storage_path("exports/{$filename}.xls"));
                    $message->from(config('mail.username'), '软妹后台');
                });
                return back()->with('flash_message', '已发送邮件，请查收附件。');
            } else {
                // 直接下载
                Excel::create($filename, function ($excel) use ($result, $title) {
                    $excel->sheet('Sheetname', function ($sheet) use ($result, $title) {
                        $sheet->fromArray($result, null, 'A1', true);
                        $sheet->row(1, $title);
                    });
                })->export('xls');
            }
        }
    }
}

