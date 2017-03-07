<?php

namespace App\Http\Controllers\Remote\Game;

use App\Entities\Game\LoginLog;
use App\Entities\Game\Role;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $menu = $this->menu;

        $loginLogs = LoginLog::with('role')->orderBy('LoginTime', 'DESC');

        if ($roleId = $request->get('user_id')) {
            $loginLogs->where('UserID', trim($roleId));
        } elseif ($roleName = $request->get('role_name')) {
            $roles = Role::where('RoleName', 'LIKE', "%{$roleName}%")->get()->pluck('UserID');
            $loginLogs->whereIn('UserID', $roles);
        }

        // filter from and to
        if ($from = $request->get('from')) {
            $loginLogs->where('LoginTime', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $loginLogs->where('LoginTime', '<=', $to);
        }

        $loginLogs = $loginLogs->paginate(20);

        //dd($loginLogs);
        return view('remote.game.login-logs-index', compact('menu', 'loginLogs'));
    }
}
