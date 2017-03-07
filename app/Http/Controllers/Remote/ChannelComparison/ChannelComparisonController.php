<?php

namespace App\Http\Controllers\Remote\ChannelComparison;

use App\Channel;
use App\Entities\Financial\Order;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ChannelComparisonController extends Controller
{
    public function payRank(Request $request)
    {
        $menu = $this->menu;

        $orders = Order::select(DB::raw('SUM(orderMoney) as money, channelId, COUNT(id) as count'))
                       ->orderBy(DB::raw('SUM(orderMoney)'), 'DESC')
                       ->groupBy('channelId')
                       ->with('channel');
        if ($channelId = $request->get('channel_id')) {
            $orders->where('channelId', trim($channelId));
        } elseif ($channelName = $request->get('channel_name')) {
            // find channel id first
            $channels = Channel::where('name', 'LIKE', "%{$channelName}%")->get()->pluck('channel_id');
            $orders->whereIn('channelId', $channels);
        }
        $orders = $orders->where('payStatus','=',1);
        $orders = $orders->paginate(10);

        return view('remote.channel-comparison.pay-rank', compact('menu', 'orders'));
    }
}
