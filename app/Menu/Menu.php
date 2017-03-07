<?php

namespace App\Menu;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

class Menu implements ArrayAccess, Arrayable
{
    /**
     * 菜单，注释在前两项中。
     *
     * @var array
     */
    protected static $menu = [
        [
            // 显示名称、链接文字名称
            'display_name'   => '首页',
            // 识别唯一权限的前缀，全局必须唯一，最就是符合 route 或 url
            'permission_key' => 'home',
            // icon 图标 class， 子菜单无图标，可省略
            'icon'           => 'fa fa-th-large',
            // url，这个值将用来生成页面网址，必须唯一
            'url'            => '/',
            // 本页所符合的路由名称，用来决断菜单是否高亮显示（如果本页所使用的路由名称
            // 能符合请求的路由，说明这个菜单应该 active），
            'routes'         => 'home',
        ],
        [
            'display_name'   => '超级管理员',
            'permission_key' => 'admin',
            'url'            => 'admin',
            'icon'           => 'fa fa-user',
            'routes'         => ['admin'],
            'child'          => [
                [
                    'display_name'   => '帐号管理',
                    'permission_key' => 'admin-users',
                    'url'            => 'users',
                    'routes'         => ['users'],
                ],
                [
                    'display_name'   => '角色管理',
                    'permission_key' => 'admin-roles',
                    'url'            => 'roles',
                    'routes'         => ['roles'],
                ],
                [
                    'display_name'   => '服务器管理',
                    'permission_key' => 'admin-servers',
                    'url'            => 'servers',
                    'routes'         => ['servers'],
                ],
                [
                    'display_name'   => '渠道 ID 配置',
                    'permission_key' => 'admin-channels',
                    'url'            => 'channels',
                    'routes'         => ['channels'],
                ],
                [
                    'display_name'   => '系统信息',
                    'permission_key' => 'admin-system-info',
                    'url'            => 'system-info',
                    'routes'         => ['system-info'],
                ],
            ],
        ],
        [
            'display_name'   => '订单',
            'permission_key' => 'orders',
            'icon'           => 'fa fa-cubes',
            'routes'         => 'orders',
            'url'            => 'orders',
            'child'          => [
                [
                    'display_name'   => '订单对账',
                    'permission_key' => 'orders-check',
                    'url'            => 'check',
                    'routes'         => ['check'],
                ],
            ],
        ],
        [
            'display_name'   => '游戏',
            'permission_key' => 'game',
            'icon'           => 'fa fa-gamepad',
            'url'            => 'game',
            'routes'         => 'game',
            'child'          => [
                [
                    'url'            => 'roles',
                    'permission_key' => 'game-roles',
                    'display_name'   => '角色',
                    'routes'         => ['roles'],
                ],
                [
                    'url'            => 'guilds',
                    'permission_key' => 'game-guilds',
                    'display_name'   => '工会',
                    'routes'         => ['guilds'],
                ],
                [
                    'url'            => 'activecards',
                    'permission_key' => 'game-activecards',
                    'display_name'   => '媒体卡使用记录',
                    'routes'         => ['activecards'],
                ],
                [
                    'url'            => 'currencies',
                    'permission_key' => 'game-currencies',
                    'display_name'   => '货币流水',
                    'routes'         => ['currencies'],
                ],
                [
                    'url'            => 'login-logs',
                    'permission_key' => 'game-login-logs',
                    'display_name'   => '登录日志',
                    'routes'         => ['login-logs'],
                ],
            ],
        ],
        [
            'display_name'   => '数据统计',
            'permission_key' => 'analyze',
            'routes'         => 'analyze',
            'url'            => 'analyze',
            'icon'           => 'fa fa-line-chart',
            'child'          => [
                [
                    'url'            => 'summarize',
                    'permission_key' => 'analyze-summarize',
                    'display_name'   => '概况',
                    'routes'         => ['summarize'],
                ],
                [
                    'url'            => 'two-weeks',
                    'permission_key' => 'analyze-two-weeks',
                    'display_name'   => '开服两周数据',
                    'routes'         => ['two-weeks'],
                ],
                [
                    'url'            => 'monthly',
                    'permission_key' => 'analyze-monthly',
                    'display_name'   => '月度数据总汇',
                    'routes'         => ['monthly'],
                ],
                [
                    'url'            => 'ltv',
                    'permission_key' => 'analyze-ltv',
                    'display_name'   => 'LTV',
                    'routes'         => ['ltv'],
                ],
            ],
        ],
        [
            'display_name'   => '在线与注册',
            'permission_key' => 'online',
            'url'            => 'online',
            'routes'         => 'online',
            'icon'           => 'fa fa-desktop',
            'child'          => [
                [
                    'display_name' => '当前数据',
                    'url' => 'current',
                    'permission_key' => 'online-current',
                    'routes' => ['current']
                ],
                [
                    'url'            => 'trending',
                    'permission_key' => 'online-trending',
                    'display_name'   => '在线人数',
                    'routes'         => ['trending'],
                ],
                [
                    'url'            => 'new',
                    'permission_key' => 'online-new',
                    'display_name'   => '新用户统计',
                    'routes'         => ['new'],
                ],
                [
                    'url'            => 'login',
                    'permission_key' => 'online-login',
                    'display_name'   => '登录统计',
                    'routes'         => ['login'],
                ],
                //[
                //    'url'            => 'roles-and-players',
                //    'permission_key' => 'online-roles-and-players',
                //    'display_name'   => '角色/账号创建统计',
                //    'routes'         => ['roles-and-players'],
                //],
                //[
                //    'url'            => 'second-glance',
                //    'permission_key' => 'online-second-glance',
                //    'display_name'   => '二次登录率',
                //    'routes'         => ['second-glance'],
                //],
                [
                    'url'            => 'retention',
                    'permission_key' => 'online-retention',
                    'display_name'   => '留存率',
                    'routes'         => ['retention'],
                ],
                //[
                //    'url'            => 'ip-retention',
                //    'permission_key' => 'online-ip-retention',
                //    'display_name'   => '账号 IP 去重玩家留存率',
                //    'routes'         => ['ip-retention'],
                //],
            ],
        ],
        [
            'display_name'   => '充值与消费',
            'permission_key' => 'expense',
            'url'            => 'expense',
            'routes'         => 'expense',
            'icon'           => 'fa fa-rmb',
            'child'          => [
                [
                    'url'            => 'summarize',
                    'permission_key' => 'expense-summarize',
                    'display_name'   => '概况',
                    'routes'         => ['summarize'],
                ],
                [
                    'url'            => 'hours',
                    'permission_key' => 'expense-hours',
                    'display_name'   => '每小时充值金额',
                    'routes'         => ['hours'],
                ],
                //[
                //    'url'            => 'expense',
                //    'permission_key' => 'expense-expense',
                //    'display_name'   => '各区消费',
                //    'routes'         => ['expense'],
                //],
                [
                    'url'            => 'daily-total',
                    'permission_key' => 'expense-daily-total',
                    'display_name'   => '每天消费总量图表',
                    'routes'         => ['daily-total'],
                ],
                //[
                //    'url'            => 'daily-average',
                //    'permission_key' => 'expense-daily-average',
                //    'display_name'   => '每天平均消费',
                //    'routes'         => ['daily-average'],
                //],
                [
                    'url'            => 'pay-detail',
                    'permission_key' => 'expense-pay-detail',
                    'display_name'   => '充值详情',
                    'routes'         => ['pay-detail'],
                ],
                [
                    'url'            => 'roles-expense',
                    'permission_key' => 'expense-roles-expense',
                    'display_name'   => '角色消费',
                    'routes'         => ['roles-expense'],
                ],
                [
                    'url'            => 'range',
                    'permission_key' => 'expense-range',
                    'display_name'   => '各段充值额',
                    'routes'         => ['range'],
                ],
                [
                    'url'            => 'pay-rate',
                    'permission_key' => 'expense-pay-rate',
                    'display_name'   => '新老玩家付费率',
                    'routes'         => ['pay-rate'],
                ],
            ],
        ],
        [
            'display_name'   => '排行榜',
            'permission_key' => 'rank',
            'url'            => 'rank',
            'routes'         => 'rank',
            'icon'           => 'fa fa-bar-chart-o',
            'child'          => [
                [
                    'url'            => 'pay',
                    'permission_key' => 'rank-pay',
                    'display_name'   => '账号充值排行榜',
                    'routes'         => ['pay'],
                ],
                [
                    'url'            => 'expense',
                    'permission_key' => 'rank-expense',
                    'display_name'   => '账号消费排行榜',
                    'routes'         => ['expense'],
                ],
            ],
        ],
        [
            'display_name'   => '渠道对比',
            'permission_key' => 'channel-comparison',
            'url'            => 'channel-comparison',
            'routes'         => 'channel-comparison',
            'icon'           => 'fa fa-eye',
            'child'          => [
                [
                    'url'            => 'pay-rank',
                    'permission_key' => 'channel-comparison-pay-rank',
                    'display_name'   => '渠道充值排行榜',
                    'routes'         => ['pay-rank'],
                ],
            ],
        ],
        [
            'display_name'   => '渠道需求',
            'permission_key' => 'channel',
            'icon'           => 'fa fa-fire',
            'url'            => 'channel',
            'routes'         => 'channel',
            'child'          => [
                [
                    'url'            => 'ip-login',
                    'permission_key' => 'channel-ip-login',
                    'display_name'   => 'IP 登录账号数统计',
                    'routes'         => ['ip-login'],
                ],
                [
                    'url'            => 'ip-users',
                    'permission_key' => 'channel-ip-users',
                    'display_name'   => 'IP 登录账号列表',
                    'routes'         => ['ip-users'],
                ],
                [
                    'url'            => 'roles',
                    'permission_key' => 'channel-roles',
                    'display_name'   => '角色信息',
                    'routes'         => ['roles'],
                ],
                [
                    'url'            => 'login-log',
                    'permission_key' => 'channel-login-log',
                    'display_name'   => '登录日志（封/解封）',
                    'routes'         => ['login-log'],
                ],
                [
                    'url'            => 'order-pays',
                    'permission_key' => 'channel-order-pays',
                    'display_name'   => '充值订单列表',
                    'routes'         => ['order-pays'],
                ],
                [
                    'url'            => 'operation-log',
                    'permission_key' => 'channel-operation-log',
                    'display_name'   => '用户行为日志',
                    'routes'         => ['operation-log'],
                ],
                //[
                //    'url'            => 'ltv',
                //    'permission_key' => 'channel-ltv',
                //    'display_name'   => 'LTV',
                //    'routes'         => ['ltv'],
                //],
            ],
        ],
    ];

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return static::$menu;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset(static::$menu[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return static::$menu[$offset];
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        static::$menu[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset(static::$menu[$offset]);
    }
}
