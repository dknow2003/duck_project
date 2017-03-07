<?php

namespace App\Entities\Game;

class Role extends Base
{
    protected $table = 'usr_userroleinfo';

    /**
     * 映射数据库注释为表单名
     * @var array
     */
    public $mappedComment = [];

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'UserID';
    }

    public function currencies()
    {
        return $this->hasMany(Currency::class, 'UserID', 'UserID');
    }

    public function equipments()
    {
        return $this->hasMany(EquipmentsList::class, 'UserID', 'UserID');
    }

    public function goods()
    {
        return $this->hasMany(GoodsList::class, 'UserID', 'UserID');
    }

    public function user()
    {
        return $this->belongsTo(Player::class, 'UserID', 'UserID');
    }
}
