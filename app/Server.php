<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $connection = 'mysql';

    protected $casts = [
        'connections' => 'array',
        'status' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'connections',
        'start_from'
    ];
}
