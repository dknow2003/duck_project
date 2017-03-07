<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Requests\UserRequest;
use App\Permission;
use App\Role;
use App\Server;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('users.is_super', 0)->orderBy('users.id', 'DESC');

        $menu = $this->menu;

        // 按权限、按角色筛选，只能二选一。
        if ($request->has('permission')) {
            $permission = Permission::with('roles')->find($request->get('permission'));
            if ($permission) {
                $users->where('users.status', 1);
                // 移除超管角色
                $roles = $permission->roles->reject(function ($role) {
                    return 'superadmin' == $role->name;
                })->pluck('id')->all();

                $rawIds = implode(',', $roles);
                $users->join(\DB::raw("(SELECT `user_id` FROM `admin_role_user` WHERE `admin_role_user`.`role_id` IN ({$rawIds}) GROUP BY `admin_role_user`.`user_id`) as sub"), function ($join) use ($roles) {
                    $join->on('users.id', '=', \DB::raw('`sub`.user_id'));
                });
                $users->count();
                unset($roles);
            }
        } elseif ($request->has('role')) {
            $users->where('users.status', 1);
            $role = Role::find($request->get('role'));
            if ($role && $role->name != 'superadmin') {
                $users->join('role_user', function ($join) use ($role) {
                    $join->on('users.id', '=', 'role_user.user_id');
                });
                $users->where('role_user.role_id', $role->id);
            }
        }

        $users = $users->paginate(10);

        // 各个权限的帐号数量
        $permissions = Permission::with('roles')->get();
        foreach ($permissions as $permission) {
            $permission->usersCount = User::countUsers($permission->roles);
        }

        // 各个角色的帐号数量
        //\DB::enableQueryLog();
        $roles = Role::where('name', '!=', 'superadmin')->get();
        foreach ($roles as $role) {
            $role->usersCount = User::where('users.is_super', 0)->orderBy('users.id', 'DESC')->where('users.status', 1)->join('role_user', function ($join) use ($role) {
                $join->on('users.id', '=', 'role_user.user_id');
            })->where('role_user.role_id', $role->id)->count();
            //$role->users()->count();
        }
        //dd($roles);
        //dd(\DB::getQueryLog());

        return view('users.index', compact('users', 'menu', 'permissions', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $menu = $this->menu;
        $channelList = Channel::pluck('name', 'channel_id');
        $available_servers = Server::pluck('name', 'id');


        return view('users.create', compact('roles', 'menu', 'channelList', 'available_servers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\UserRequest
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = User::create([
            'username'  => $request->get('username'),
            'email'     => $request->get('email'),
            'password'  => Hash::make($request->get('pwd')),
            'full_name' => $request->get('full_name'),
            'channel' => $request->get('channel') ?: '',
            'available_servers' => array_filter($request->get('available_servers')),
        ]);

        $user->roles()->attach($request->get('roles'));

        return redirect('admin/users')->with('flash_message', ['帐号创建成功！']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $menu = $this->menu;
        $channelList = Channel::pluck('name', 'channel_id');
        $available_servers = Server::pluck('name', 'id');


        return view('users.edit', compact('user', 'roles', 'menu', 'channelList', 'available_servers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UserRequest|\Illuminate\Http\Request $request
     * @param \App\User                                               $user
     *
     * @return \Illuminate\Http\Response
     * @internal param int $id
     *
     */
    public function update(UserRequest $request, User $user)
    {
        // is_super 只能通过命令行设置。
        $except = ['is_super', 'pwd'];
        $input = $request->except($except);

        if ($request->has('change_password')) {
            $input['password'] = Hash::make($request->get('pwd'));
        }
        $input['available_servers'] = array_filter(isset($input['available_servers']) ? $input['available_servers'] : []);
        $user->update($input);

        // 移除有可能通过修改表单伪造管理员。
        $superAdmin = Role::where('name', 'superadmin')->first()->id;
        $roles = $request->get('roles', []);
        foreach ($roles as $key => $role) {
            if (false !== array_search($superAdmin, $roles)) {
                unset($roles[$key]);
            }
        }

        $user->roles()->sync($request->get('roles') ?: []);

        return redirect('admin/users')->with('flash_message', ['帐号修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeStatus(Request $request)
    {
        $status = $request->get('status') ?: 0;
        $users = User::where('id', $request->get('user_id'))->update([
            'status' => $status,
        ]);

        return redirect('admin/users')->with('flash_message', ['修改成功！']);
    }

    public function switchServer(Request $request)
    {
        $id = $request->get('server');
        $user = Auth::user()->update([
            'selected_server' => $id
        ]);

        return redirect()->back()->with('flash_message', '服务器切换成功！');
    }
}
