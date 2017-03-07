<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::with('perms')->where('name', '!=', 'superadmin')->paginate(10);

        $menu = $this->menu;

        foreach ($roles as $role) {
            $role->usersCount = User::where('users.is_super', 0)->orderBy('users.id', 'DESC')->where('users.status', 1)->join('role_user', function ($join) use ($role) {
                $join->on('users.id', '=', 'role_user.user_id');
            })->where('role_user.role_id', $role->id)->count();
        }

        return view('roles.index', compact('roles', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        $menu = $this->menu;

        return view('roles.create', compact('permissions', 'menu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @param \App\Http\Requests\RoleRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $newRoles = \App\Role::create([
            'name' => $this->generateRoleNameUnique(),
            'display_name' => $request->get('display_name'),
            'description'  => $request->get('description'),
        ]);

        if ($request->has('permissions') && $permissions = $request->get('permissions')) {
            $newRoles->perms()->sync($permissions);
        }

        return redirect('admin/roles')->with('flash_message', ['角色创建成功！']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     *
     * @param \App\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $menu = $this->menu;

        return view('roles.edit', compact('role', 'permissions', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @param \App\Http\Requests\RoleRequest $request
     * @param \App\Role                      $role
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        if ($role->name == 'superadmin') {
            return redirect('roles')->with('flash_message', ['超级管理员不能修改！']);
        }
        $role->update($request->all());

        $role->perms()->sync($request->get('permissions') ?: []);

        return redirect('admin/roles')->with('flash_message', ['角色修改成功！']);
    }

    public function generateRoleNameUnique()
    {
        static $i = 0;
        $microTime = microtime(true);
        $str = uniqid('role_name_uniqid', true);
        $randomBytes = md5($microTime . $str);
        if ($role = Role::where('name', $randomBytes) && $i >= 10) {
            $randomBytes = $this->generateRoleNameUnique();
            $i ++;
        }

        return $randomBytes;
    }

}
