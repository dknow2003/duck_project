<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;

    protected $connection = 'mysql';

    protected $casts      = [
        'is_super'          => 'boolean',
        'status'            => 'boolean',
        'channel'           => 'integer',
        'available_servers' => 'array',
        'selected_server'   => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'is_super',
        'full_name',
        'status',
        'channel',
        'available_servers',
        'selected_server',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function countUsers(Collection $roles)
    {
        // 缓存每个权限下角色的任务数量。
        static $permission = [];

        // 刨除超级管理员。
        $filtered = $roles->filter(function ($role) {
            return $role->name != 'superadmin';
        });

        $roleIds = $filtered->pluck('id')->all();
        if (!$roleIds) {
            return 0;
        }

        $rawIds = implode(',', $roleIds);
        if (isset($permission[$rawIds])) {
            return $permission[$rawIds];
        }
        $count = \DB::table('users')->where('users.status', 1)->join(\DB::raw("(SELECT `user_id` FROM `admin_role_user` WHERE `admin_role_user`.`role_id` IN ({$rawIds}) GROUP BY `admin_role_user`.`user_id`) as sub"), function ($join) {
            $join->on('users.id', '=', \DB::raw('`sub`.user_id'));
        })->count();

        $permission[$rawIds] = $count;

        return $count;
    }
}
