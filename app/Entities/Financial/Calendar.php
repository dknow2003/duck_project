<?php

namespace App\Entities\Financial;

class Calendar extends Base
{
    public $timestamps = false;

    protected $fillable = ['date'];

    protected $table = 'admin_calendar';
}
