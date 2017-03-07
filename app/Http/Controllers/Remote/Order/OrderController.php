<?php

namespace App\Http\Controllers\Remote\Order;

use App\Entities\Financial\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function check(Request $request)
    {
        $menu = $this->menu;

        $orders = (new Order())->newQuery();

        if ($aid = $request->get('aid')) {
            $orders->where('aid', $aid);
        } elseif ($serial = $request->get('serial')) {
            $orders->where('orderSerial', $serial);
        }

        $orders = $orders->paginate(20);

        return view('remote.expenses.pay-detail', compact('menu', 'orders'));
    }
}
