<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $connection = 'mysql';
    
    protected $fillable = [
        'name', 'channel_id', 'description'
    ];
}
