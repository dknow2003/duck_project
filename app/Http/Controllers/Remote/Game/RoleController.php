<?php

namespace App\Http\Controllers\Remote\Game;

use App\Entities\Game\Player;
use App\Entities\Game\Role;
use App\Server;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $menu = $this->menu;
        // order
        if ($request->has('order') && in_array($orderBy = $request->get('order'), ['level', 'crystal'])) {
            if ($orderBy !== 'crystal') {
                $roles = Role::orderBy($orderBy, 'DESC');
            } else {
                $roles = Role::orderBy(DB::raw('BindingCrystal+Crystal'), 'DESC');
            }
        } else {
            $roles = Role::orderBy('CreateTime', 'DESC');
        }

        // search
        if ($id = $request->get('user_id')) {
            $roles->where('UserID', '=', trim($id));
        } elseif ($name = $request->get('role_name')) {
            $name = trim($name);
            $roles->where('RoleName', 'LIKE', "%{$name}%");
        }

        $roles = $roles->paginate(20);

        return view('remote.game.roles-index', compact('menu', 'roles'));
    }

    public function show($roleId, Request $request)
    {
        $role = Role::findOrFail($roleId);
        $menu = $this->menu;
        $role = $this->mapColumnComment($role);
        $goodsList = $role->goods()->get();
        $equipmentsList = $role->equipments()->get();
        return view('remote.game.role-show', compact('menu', 'role', 'goodsList', 'equipmentsList'));
    }

    private function mapColumnComment(Model $model) {
        $table = $model->getTable();
        $fullColumn = DB::select("SHOW FULL COLUMNS FROM {$table}");
        $mapped = [];
        foreach ($fullColumn as $column) {
            if (isset($model['attributes'][$key = $column->Field])) {
                $mapped[$column->Comment] = $model['attributes'][$key];
            }
        }
        $model->mappedComment = $mapped;

        return $model;
    }

    public function json(Request $request)
    {
        $uid = $request->get('uid');
        $svr_id = $request->get('sid');

        if( empty($svr_id) )
        {
            return array('ret'=>1,'error'=>'miss sid param','data'=>array());
        }

        if( empty($uid) )
        {
            return array('ret'=>1,'error'=>'miss uid param','data'=>array());
        }


        $server = Server::where('id', $svr_id)->first();
        if( empty($server) )
        {
            return array('ret'=>1,'error'=>'invalid sid param','data'=>array());
        }

        $cur_config =  app()['config'];
        $databases = $server->connections ;
        $def_cfg = app('config')->get('database.connections.game-default');
        $config = array_merge($def_cfg, $databases[1]);
        $config['password'] = $config['pwd'];
        $cur_config->set('database.connections.game-default', $config);
        $cur_config->set('database.default', 'game-default');


        // Makeing an object of second DB.
        // Getting data with second DB object.
        $account = DB::connection('game-default')->table('usr_userinfo')
            ->where('LoginName', '=', $uid)
            ->first();
        if( empty($account) )
        {
            return array('ret'=>1,'error'=>'role is not exist','data'=>array());
        }

        $role = DB::connection('game-default')->table('usr_userroleinfo')->
        where('UserID','=',$account->UserID)->first();
        if( empty($role) )
        {
            return array('ret'=>1,'error'=>'role is not exist','data'=>array());
        }

        $data = array();
        $data['ret'] = 0;
        $data['error'] = 'ok';
        $data['roleid'] = $role->UserID;
        $data['nickname'] = $role->RoleName;
        $data['level'] = $role->Level;

        return $data;

    }
}
