<?php

namespace App\Entities\Game;

class Currency extends Base
{
    public static $operType  = [
        1 => '无',
        2 => '收入',
        3 => '消耗',
    ];

    public static $moneyType = [
        0 => '未知',
        1 => 'RMB',
        2 => '金币',
        3 => '水晶',
        4 => 'PVP 货币',
        5 => '绑定钻石',
        6 => '粮食',
        7 => '工会币',
    ];

    public static $remarkMap = [
        'SellGoods'                    => '出售物品',
        'FightOver'                    => '战斗结束',
        'SweepConsumeCrystal'          => '扫荡消耗',
        'ResetTowerHero'               => '推塔疲劳重置',
        'SweepTowerToMax'              => '扫荡塔到最高层',
        'ResetTowerCountRequest'       => '重置塔次数',
        'UnlockFormation'              => '解锁阵型',
        'UpgradeFormation'             => '阵型升级',
        'GMRecharge'                   => 'GM请求RMB充值',
        'CreateGuild'                  => '创建公会',
        'GuildDonate'                  => '公会捐献',
        'GuildMercenaryPve'            => '公会佣兵PVE',
        'HeroFixedEquipUpgrade'        => '英雄固定装备升级',
        'ResetTowerHero'               => '重置塔中的英雄',
        'HeroFixedEquipUpgradeToLevel' => '英雄固定装备升级到指定等级',
        'EquipCompose'                 => '装备合成',
        'EquipUpgrade'                 => '装备升级',
        'EquipUpgradeToLevel'          => '装备升级到指定等级',
        'EquipAdvance'                 => '装备进阶',
        'EquipResetSkillRequest'       => '装备洗炼',
        'RecvMailAttach'               => '邮件',
        'RecvMailAllAttach'            => '所有邮件',
        'FreshExploreTask'             => '刷新冒险任务',
        'StartAdventure'               => '开始冒险',
        'FindAdvOpponent'              => '寻找一次冒险对手',
        '10TimesSpecialDraw'           => '限时抽卡10连',
        '1TimeSpecialDraw'             => '限时抽卡1次',
        'BuyGoods'                     => '购买商店物品',
        '1TimeLuckyDraw'               => '单抽英雄',
        '10TimesLuckyDraw'             => '10连抽英雄',
        'Auction onSale'               => '拍卖出售',
        'AuctionBuy'                   => '拍卖购买',
        'DailBuy'                      => '每日操作',
        'AuctionReturnMoney'           => '拍卖失败退款',
        'BuyFund'                      => '购买基金',
        'Signin'                       => '签到',
        'EATCT_7DaySale'               => '7天打折物品',
        'EATCT_Fund'                   => '基金任务奖励',
        'EATCT_VipLevel'               => 'VIP任务奖励',
        'UseItem'                      => '使用物品记录',
        'ReceiveOutput'                => '产出表记录',
        'UnlockAvatar'                 => '解锁时装',
        'TaskFinished'                 => '完成任务',
    ];

    protected     $table     = 'log_moneyinout';

    public function role()
    {
        return $this->belongsTo(Role::class, 'UserID', 'UserID');
    }

    public function presentOperType($operation)
    {
        return isset(static::$operType[$operation]) ? static::$operType[$operation] : $operation;
    }

    public function presentMoneyType($money)
    {
        return isset(static::$moneyType[$money]) ? static::$moneyType[$money] : $money;
    }

    public function presentRemark($remark)
    {
        return isset(static::$remarkMap[$remark]) ? static::$remarkMap[$remark] : $remark;
    }
}
