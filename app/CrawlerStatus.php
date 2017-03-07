<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrawlerStatus extends Model
{
    protected $table = 'crawler_status';

    protected $fillable = [
        'date', 'status'
    ];

    public $timestamps = false;

    protected $casts = [
        'status' => 'boolean',
    ];
}
