<?php

namespace App\Entities\Game;

class Calendar extends Base
{
    public $timestamps = false;

    protected $fillable = ['date'];

    protected $table = 'admin_calendar';
}
