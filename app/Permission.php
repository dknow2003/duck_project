<?php

namespace App;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $connection = 'mysql';

    protected $touches = [
        'roles'
    ];
}
