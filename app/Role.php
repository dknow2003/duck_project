<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $connection = 'mysql';

    protected $touches  = [
        'users'
    ];

    protected $fillable = [
        'name', 'display_name', 'description',
    ];
}
