<?php

namespace App\Http\Controllers\Remote\Rank;

use App\Channel;
use App\Entities\Financial\Order;
use App\Entities\Game\Currency;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RankController extends Controller
{
    public function pay(Request $request)
    {
        $menu = $this->menu;

        $orders = Order::select(DB::raw('SUM(orderMoney) as money, channelId, aid, COUNT(id) as count'))
                       ->orderBy(DB::raw('SUM(orderMoney)'), 'DESC')
                       ->with('channel', 'user')
                       ->groupBy('aid');
        if ($channelId = $request->get('channel_id')) {
            $orders->where('channelId', trim($channelId));
        }
        $orders = $orders->paginate(10);
        // 编号
        $channelList = Channel::all();

        return view('remote.rank.pay', compact('menu', 'orders', 'channelList'));
    }

    public function expense(Request $request)
    {
        $menu = $this->menu;
        $channelList = Channel::all();

        $orders = (new Currency())->newQuery();

        $orders = $orders->from(DB::raw('log_moneyinout  money'))
                         ->where('OperType', 2)
                         ->where('MoneyType', 3)
                         ->join(DB::raw('usr_userinfo  user'), function ($join) {
                             $join->on(DB::raw('money.UserID'), '=', DB::raw('user.UserID'));
                         })
                         ->with('role')
                         ->groupBy(DB::raw('money.UserID'))
                         ->select(DB::raw('SUM(MoneyChanged) as money, COUNT(ID) as count, money.UserID'))
                        ->orderBy(DB::raw('SUM(MoneyChanged)'), 'DESC');
        if ($channelId = $request->get('channel_id')) {
            $orders->where(DB::raw('user.RegFrom'), '=', trim($channelId));
        }
        $orders = $orders->paginate(10);

        return view('remote.rank.expense', compact('menu', 'orders', 'channelList'));
    }
}
