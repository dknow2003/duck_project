<?php

namespace App\Entities\Game;

class Statistic extends Base
{
    public $timestamps = false;

    protected $table      = 'sat_online';

    protected $dates = ['StatTime'];
}
