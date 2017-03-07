<?php

namespace App\Entities\Financial;


use App\Channel;
use App\Entities\Game\Player;
use App\Entities\Game\Role;

class Order extends Base
{
    protected $table = 'gms_order';

    public function user()
    {
        return $this->hasOne(Player::class, 'UserID', 'aid');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'UserID', 'aid');
    }

    public function channel()
    {
        return $this->hasOne(Channel::class, 'channel_id', 'channelId');
    }
}
