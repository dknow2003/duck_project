<?php

namespace App\Http\Controllers\Remote\Game;

use App\Entities\Game\Currency;
use App\Entities\Game\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{

    public function index(Request $request)
    {
        $menu = $this->menu;

        $currencies = (new Currency())->newQuery();
        if ($roleId = $request->get('role_id')) {
            $currencies->where('UserID', trim($roleId));
        } elseif ($roleName = trim($request->get('role_name'))) {
            $roles = Role::where('RoleName', 'LIKE', "%{$roleName}%")->get()->pluck('UserID');
            $currencies->whereIn('UserID', $roles);
        }

        if ($remark = trim($request->get('remark'))) {
            $currencies->where('Remark', $remark);
        }


        $currencies = $currencies->orderBy('LogTime', 'DESC')
                                 ->with('role')
                                 ->paginate(20);
        $remarks = Currency::$remarkMap;

        return view('remote.game.currencies-index', compact('menu', 'currencies', 'remarks'));
    }

}
