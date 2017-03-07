<?php

namespace App\Entities\Game;

class Player extends Base
{
    protected $primaryKey = 'UserID';

    protected $table      = 'usr_userinfo';

    protected $fillable   = ['State'];

    protected $dates      = [
        'RegTime',
    ];

    public $timestamps = false;

    public function logins()
    {
        return $this->hasMany(LoginLog::class, 'UserID', 'UserID');
    }
}
