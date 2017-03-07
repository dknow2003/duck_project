<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrawlerData extends Model
{


    /** 注册人数 */
    const REGISTERS = 1;
    /** 活跃人数 */
    const ACTIVES = 2;
    /** 付费人数 */
    const PAYED = 3;
    /** 付费总额 */
    const AMOUNT = 4;
    /** 最高时在线 */
    const PCU = 5;
    /** 平均在线 */
    const ACU = 6;
    /** 首次登录人数 */
    const NEW_LOGINS = 7;
    /**
     * 按时间段计算的活跃人数
     * @see App\Services\Crawler::getActives()
     */
    const ACTIVES_BY_RANGE = 8;

    protected $fillable = [
        'date', 'value', 'type', 'server_id','data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public $timestamps  = false;

    public $table = 'crawler_data';
    
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        static::boot();
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        app()['config']->set('database.default', 'mysql');
        parent::boot(); // TODO: Change the autogenerated stub
    }
}