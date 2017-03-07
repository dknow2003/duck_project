<?php

namespace App\Entities\Game;


class LoginLog extends Base
{
    protected $table = 'log_onlineinfo';

    protected $dates = [
        'LoginTime', 'LogoutTime'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'UserID', 'UserID');
    }

    public function role()
    {
        $ret =  $this->belongsTo(Role::class, 'UserID', 'UserID');
        return $ret;
    }
}
