<?php

namespace App\Entities\Game;


class Operation extends Base
{
    protected $table = 'usr_useroperactinfo';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(Player::class, 'UserID', 'UserID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'UserID', 'UserID');
    }

}
