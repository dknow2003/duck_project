<?php

namespace App\Http\Controllers;

use App\CrawlerData;
use App\CrawlerStatus;
use App\Entities\Financial\Order;
use App\Http\Requests;
use App\Menu\Menu;
use App\Menu\MenuPresenter;
use App\Server;
use App\Services\Crawler;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $from;

    private $to;

    private $export;

    private $fetch;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        parent::__construct(new MenuPresenter(new Menu()));
        $this->from = $request->get('from') ?: Carbon::now()->subMonth()->toDateString();
        $this->to = $request->get('to') ?: Carbon::now()->toDateString();
        $this->export = $request->get('export') ?: null;
        $this->fetch = $request->get('fetch') ?: null;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->ifUserDoNotHavePermission();

        $menu = $this->menu;
        $data['display_from'] = $this->getDisplayFrom();
        $data['display_to'] = $this->getDisplayTo();
        $data['actives'] = $this->getActives();
        $data['registers'] = $this->getRegisters();
        $data['payed'] = $this->getPayed();
        $data['amount'] = $this->getAmount();
        $data['acu_by_day'] = $this->getAcuByDay();
        $data['pcu_by_day'] = $this->getPcuByDay();
        $data['login_by_day'] = $this->getLoginByDay();
        $data['pur'] = $this->getPayedByDay();
        $data['pcu'] = $this->getPcu();
        $data['acu'] = $this->getAcu();
        $data['last_sync'] = $this->getLastSync();

        //dd($data['pur']);
        $this->ifExport($data);

        //dd($data);
        return view('home', compact('menu', 'data'));
    }

    private function getLastSync()
    {
        $status = CrawlerStatus::orderBy('date', 'DESC')->first();

        return $status ? $status->date : Carbon::now()->subDay()->toDateString();
    }

    private function ifUserDoNotHavePermission()
    {
        $user = Auth::user();
        if (!$user->can('home-manage')) {
            if (!$permissions = $this->detectPermissions($user)) {
                abort('403');
            }

            header('Location: ' . route($permissions[0]));
        }
    }

    private function detectPermissions(User $user)
    {
        $permissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->perms as $perm) {
                $permissions[] = $perm;
            }
        }
        $permissions = collect($permissions)->transform(function ($item) {
            $str = str_replace('-manage', '', $item->name);
            // channel-comparison 替换
            $str = str_replace('channel-comparison', 'channelcomparison', $str);
            $pos = strpos($str, '-');
            if ($pos !== false) {
                $str = substr_replace($str, '.', $pos, strlen('-'));
            }
            // channel-comparison 换回
            $str = str_replace('channelcomparison', 'channel-comparison', $str);

            return $str;
        })->all();

        return $permissions = array_unique($permissions);
    }

    private function ifExport($players)
    {
        if ($export = $this->export && $fetch = $this->fetch) {
            $result = [];
            switch ($fetch) {
                case 'acu-pcu':
                    $filename = 'ACU - PCU';
                    $acuList = $players['acu_by_day'];
                    //dd($players);
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

    public function json(Request $request)
    {
        $method = $request->get('method');
        $by = $request->get('by') ?: 'day';
        $data = [];
        switch ($method) {
            case 'active':
                $data['result'] = $this->getActiveNewAndOldByDay();
                break;
            case 'payed':
                $result = $this->getPayedUsersByDay();
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['payed'];
                }
                $data['result'] = $result;
                break;
            case 'incoming':
                $result = $this->getAmountByDay();
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['money'];
                }
                $data['result'] = $result;
                break;
            case 'arpu':
                $result = $this->getArpuByDay();
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['arpu'];
                }
                $data['result'] = $result;
                break;
            case 'arppu':
                $result = $this->getArppuByDay();
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['arppu'];
                }
                $data['result'] = $result;
                break;
            case 'au-avg':
                $result = $this->getAuAvgByDay();
                foreach ($result as $key => $item) {
                    $result[$key]['two'] = $item['auAvg'];
                }
                $data['result'] = $result;
                break;
        }

        $this->ifExport($data['result']);

        return $data;
    }

    private function getAuAvgByDay()
    {
        $au = $this->getActivesByDay();
        $amounts = $this->getAmountByDay()->keyBy('day');

        $arpu = $au->map(function ($item) use ($amounts) {
            $key = $item->day;
            if (isset($amounts[$key]) && $amounts[$key]['money'] > 0 && $item->actives > 0) {
                $item->auAvg = round($amounts[$key]['money'] / $item->actives, 2);
            } else {
                $item->auAvg = 0;
            }

            return $item;
        });

        return $arpu;
    }

    private function getArppuByDay()
    {
        $payed = $this->getPayedUsersByDay()->keyBy('day');
        $amounts = $this->getAmountByDay();

        $arppu = $amounts->map(function ($item) use ($payed) {
            $key = $item->day;
            if (isset($payed[$key]) && $payed[$key]['payed'] > 0 && $item->money > 0) {
                $item->arppu = round($item->money / $payed[$key]['payed'], 2);
            } else {
                $item->arppu = 0;
            }

            return $item;
        });

        return $arppu;
    }

    private function getArpuByDay()
    {
        $au = $this->getActivesByDay();
        $amounts = $this->getAmountByDay()->keyBy('day');

        $arpu = $au->map(function ($item) use ($amounts) {
            $key = $item->day;
            if (isset($amounts[$key]) && $amounts[$key]['money'] > 0 && $item->actives > 0) {
                $item->arpu = round($amounts[$key]['money'] / $item->actives, 2);
            } else {
                $item->arpu = 0;
            }

            return $item;
        });

        return $arpu;
    }

    private function getAmountByDay()
    {
        $amounts = CrawlerData::where('type', CrawlerData::AMOUNT);
        $this->basicSetup($amounts);
        $amounts->groupBy(DB::raw('DATE(date)'));
        $amounts->select(DB::raw('SUM(value) as money, DATE_FORMAT(date, \'%m月%d日\') as day'));

        return $amounts->get();
    }

    private function getActiveNewAndOldByDay()
    {
        $au = $this->getActivesByDay();
        $new = $this->getNewLoginByDay()->keyBy('day');
        $newAndOld = $au->map(function ($item) use ($new) {
            $key = $item->day;
            $new = isset($new[$key]) ? $new[$key]['logins'] : 0;
            $item->one = $item->actives;
            $item->two = $new;

            return $item;
        });

        return $newAndOld;
    }

    private function getNewLoginByDay()
    {
        $logins = CrawlerData::where('type', CrawlerData::NEW_LOGINS);
        $this->basicSetup($logins);
        $logins->groupBy(DB::raw('DATE(date)'));
        $logins->select(DB::raw('SUM(value) as logins, DATE_FORMAT(date, \'%m月%d日\') as day'));
        $logins = $logins->get();

        // registers
        return $logins;
    }

    private function getAcu()
    {
        $acu = $this->getAcuByDay();
        $total = 0;
        $acu->map(function ($item) use (& $total) {
            $total += $item->acu;
        });

        return count($acu) ? $total / count($acu) : 0;
    }

    private function getPcu()
    {
        $pcu = $this->getPcuByDay();
        $max = collect($pcu)->pluck('pcu')->all();

        return count($max) ? max($max) : 0;
    }

    private function getPayedByDay()
    {
        $au = $this->getActivesByDay();
        $payed = $this->getPayedUsersByDay()->keyBy('day');

        $pur = $au->map(function ($item) use ($payed) {
            $p = isset($payed[$item->day]) ? $payed[$item->day]['payed'] : 0;
            $item->payed = $item->actives ? round(100 * $p / $item->actives, 1) : 0;

            return $item;
        });

        return $pur;
    }

    private function getPayedUsersByDay()
    {
        $payedUsers = CrawlerData::where('type', CrawlerData::PAYED);
        $this->basicSetup($payedUsers);
        $payedUsers->groupBy(DB::raw('DATE(date)'));
        $payedUsers->select(DB::raw('SUM(value) as payed, DATE_FORMAT(date, \'%m月%d日\') as day'));

        return $payedUsers->get();
    }

    private function getActivesByDay()
    {
        $actives = CrawlerData::where('type', CrawlerData::ACTIVES);
        $this->basicSetup($actives);
        $actives->groupBy(DB::raw('DATE(date)'));
        $actives->select(DB::raw('SUM(value) as actives, DATE_FORMAT(date, \'%m月%d日\') as day'));

        return $actives->get();
    }

    private function getLoginByDay()
    {
        $logins = CrawlerData::where('type', CrawlerData::NEW_LOGINS);
        $this->basicSetup($logins);
        $logins->groupBy(DB::raw('DATE(date)'));
        $logins->select(DB::raw('SUM(value) as logins, DATE_FORMAT(date, \'%m月%d日\') as day'));
        $logins = $logins->get();

        // registers
        $logins = $logins->keyBy('day');
        $registers = $this->getRegistersByDay();

        return $registers->map(function ($item) use ($logins) {
            $item->logins = isset($logins[$item->day]) ? $logins[$item->day]['logins'] : 0;

            return $item;
        });
    }

    private function getRegistersByDay()
    {
        $registers = CrawlerData::where('type', CrawlerData::REGISTERS);
        $this->basicSetup($registers);
        $registers->groupBy(DB::raw('DATE(date)'));
        $registers->select(DB::raw('SUM(value) as registers, DATE_FORMAT(date, \'%m月%d日\') as day'));

        return $registers->get();
    }

    private function getPcuByDay()
    {
        $data = CrawlerData::where('type', CrawlerData::PCU);
        $this->basicSetup($data);
        $data->select(DB::raw('DATE_FORMAT(date, \'%m月%d日\') as day, data, server_id'));
        $data->groupBy(DB::raw('DATE(date), server_id'));
        // 计算每小时最高
        $result = [];
        $data->get()->map(function ($item) use (&$result) {
            $day = $item->day;
            $result[$day][0] = 0;
            foreach ($item->data as $hour => $data) {
                $result[$day][$hour] = (isset($result[$day][$hour]) ? $result[$day][$hour] : 0) + $data;
            }

            return $item;
        });
        foreach ($result as $d => $r) {
            unset($result[$d]);
            $result[$d]['pcu'] = max($r);
            $result[$d]['day'] = $d;
        }

        return $result;
    }

    private function getAcuByDay()
    {
        $data = CrawlerData::where('type', CrawlerData::ACU);
        $this->basicSetup($data);
        $data->groupBy(DB::raw('DATE(date)'));
        $data->select(DB::raw('SUM(value) / COUNT(id) as acu, DATE_FORMAT(date, \'%m月%d日\') as day'));

        return $data->get()->keyBy('day');
    }

    private function basicSetup($data)
    {
        if ($this->from) {
            $data->where('date', '>=', $this->from);
        }
        if ($this->to) {
            $data->where('date', '<=', $this->to);
        }
        $data->orderBy('date', 'ASC');
    }

    private function getAmount()
    {
        $data = CrawlerData::where('type', CrawlerData::AMOUNT);
        $this->basicSetup($data);

        return $data->sum('value');
    }

    private function getPayed()
    {
        $data = CrawlerData::where('type', CrawlerData::PAYED);

        $this->basicSetup($data);

        return $data->sum('value');
    }

    /**
     * Print phpinfo out.
     *
     * @return string
     *
     */
    public function phpinfo()
    {
        phpinfo();
    }

    public function systemInfo()
    {
        $menu = $this->menu;

        return view('system-info', compact('menu'));
    }

    /**
     * @see \App\Services\Crawler::getActives()
     * @return int
     */
    private function getActives()
    {
        $total = CrawlerData::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'));
            $query->from(DB::raw('admin_crawler_data'));
            $query->where('type', '=', CrawlerData::ACTIVES_BY_RANGE);
            $query->where('date', '<=', $this->to);
            $query->orderBy('date', 'DESC');
            $query->groupBy('server_id');
        })->sum('value');

        // 减去开始日期之前的数据
        $beforeFromDate = CrawlerData::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'));
            $query->from(DB::raw('admin_crawler_data'));
            $query->where('type', '=', CrawlerData::ACTIVES_BY_RANGE);
            $query->where('date', '<', $this->from);
            $query->orderBy('date', 'DESC');
            $query->groupBy('server_id');
        })->sum('value');
        return $total - $beforeFromDate;
    }

    private function getRegisters()
    {
        $data = CrawlerData::where('type', CrawlerData::REGISTERS);

        $this->basicSetup($data);

        return $sum = $data->sum('value');
    }

    private function getDisplayFrom()
    {
        return $this->from;
    }

    private function getDisplayTo()
    {
        return $this->to;
    }
}
