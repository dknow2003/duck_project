<?php

namespace App\Http\Controllers\Remote\Channel;

use App\Channel;
use App\Entities\Financial\Order;
use App\Entities\Game\LoginLog;
use App\Entities\Game\Operation;
use App\Entities\Game\Player;
use App\Entities\Game\Role;
use App\Menu\Menu;
use App\Menu\MenuPresenter;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
{
    public static $operationMap = [
        'Login'                => '角色登陆',
        'ApGift'               => '领取体力',
        'BuyGoods'             => '商店购买物品',
        'CharpterStars'        => '章节累计星数',
        'CompleteAdventure'    => '冒险完成记录',
        'ConsumeBindCrystal'   => '直接消耗绑定钻石数',
        'ConsumeUnBindCrystal' => '绑定钻石数量不足时,又消耗了未绑定钻石数量',
        'ConsumeCrystal'       => '直接消耗钻石数量',
        'DailBuy'              => '购买每日操作记录',
        'DailBuyTuitu'         => '购买每日推图操作记录',
        'EquipUpgrade'         => '装备强化',
        'FixedEquipUpgrade'    => '英雄固定装备强化',
        'HeroDraw'             => '扭蛋抽取英雄',
        'LadderCareerRank'     => '记录天梯等级',
        'ManorGather'          => '庄源资源采集',
        'MatchFight'           => '记录PVP请求成功',
        'RobPrisonerOver'      => '俘虏战结束',
        'SendGift'             => '送体力成功',
        'SpecialHeroDraw	'  => '活动抽角色',
        'Tower'                => '爬塔记录',
        'TowerMaxNormal	'   => '普通塔最高层记录',
        'Tuitu'                => '推图记录',
        'TuituSection'         => '推图章节记录',
        'MallBuy'              => '玩家RMB购买礼包记录',
        'HighestHonor'         => '最高荣誉记录',
        'PayBindingCrystal'    => '玩家RMB购买礼包获得的绑定钻石',
        'ChangeRoleName'       => '修改角色名',
        'ModifyRoleInfo'       => '修改角色信息',
        'BindGameAccount'      => '绑定游戏帐号',
        'RolePropUpd'          => '角色属性更新',
        'UseGoods'             => '使用物品记录',
        'EquipResetSkill'      => '装备洗炼',
        'AuctionBuy'           => '拍卖记录',
        'Signin'               => '签到',
        'EATCT_Card'           => '活动月卡记录',
        'EATCT_7DaySale'       => '7天打折任务记录',
    ];

    private       $channel_id;

    public function __construct(Request $request)
    {
        parent::__construct(new MenuPresenter(new Menu()));
        $this->channel_id = Auth::user()->channel ?: 0;
        $this->channel = \App\Channel::where('channel_id', $this->channel_id)->first();
    }

    public function placeholder()
    {
        $menu = $this->menu;

        return view('remote.channel.index', compact('menu'));
    }

    public function orderPays(Request $request)
    {
        $menu = $this->menu;

        $orders = (new Order())->newQuery();

        if ($aid = $request->get('aid')) {
            $orders->where('aid', $aid);
        } elseif ($serial = $request->get('serial')) {
            $orders->where('orderSerial', $serial);
        }

        if ($channelID = $this->channel_id) {
            $orders->where('channelId', $channelID);
        }

        $orders = $orders->with('role')->paginate(20);
        $channel = $this->channel;

        return view('remote.channel.order-pays', compact('menu', 'orders', 'channel'));
    }

    public function loginLog(Request $request)
    {
        $menu = $this->menu;
        $channel = $this->channel;

        $loginLogs = LoginLog::from(DB::raw('log_onlineinfo log'))
                             ->join(DB::raw('usr_userinfo user'), function ($join) {
                                 $join->on(DB::raw('log.UserID'), '=', DB::raw('user.UserID'));
                             })
                             ->with('role')
                             ->orderBy('log.LoginTime', 'DESC');

        if ($channelId = $this->channel_id) {
            $loginLogs->where('user.RegFrom', $channelId);
        }

        if ($roleId = $request->get('user_id')) {
            $loginLogs->where('user.UserID', trim($roleId));
        } elseif ($roleName = $request->get('role_name')) {
            $roles = Role::where('RoleName', 'LIKE', "%{$roleName}%")->get()->pluck('UserID');
            $loginLogs->whereIn('user.UserID', $roles);
        }

	// filter from and to
	if ($from = $request->get('from')) {
	    $loginLogs->where('log.LoginTime', '>=', $from);
	}
        
        if ($to = $request->get('to')) {
            $loginLogs->where('log.LoginTime', '<=', $to);
	}

        $loginLogs = $loginLogs->paginate(20);

        //dd($loginLogs);
        return view('remote.channel.login-log', compact('menu', 'loginLogs', 'channel'));
    }

    public function ipLogin(Request $request)
    {
        $menu = $this->menu;
        $channel = $this->channel;

        $ips = LoginLog::from(DB::raw('log_onlineinfo log'))
                       ->with('role')
                       ->select(DB::raw('COUNT(DISTINCT(log.UserID)) as count, LoginIP'))
                       ->groupBy(DB::raw('LoginIP'))
                       ->orderBy(DB::raw('COUNT(DISTINCT(log.UserID))'), 'DESC')
                       ->join(DB::raw('usr_userinfo user'), function ($join) {
                           $join->on(DB::raw('user.UserID'), '=', DB::raw('log.UserID'));
                       });
        if ($channelID = $this->channel_id) {
            $ips->where(DB::raw('user.RegFrom'), '=', $channelID);
        }

        if ($ip = $request->get('ip')) {
            $ips->where(DB::raw('log.LoginIP'), $ip);
        }

        $ips = $ips->paginate(20);

        return view('remote.channel.ip-login', compact('ips', 'menu', 'channel'));
    }

    public function ipUsers(Request $request)
    {
        $menu = $this->menu;
        $channel = $this->channel;

        $loginLogs = LoginLog::from(DB::raw('log_onlineinfo log'))
                             ->join(DB::raw('usr_userinfo user'), function ($join) {
                                 $join->on(DB::raw('log.UserID'), '=', DB::raw('user.UserID'));
                             })
                             ->with('role')
                             ->orderBy('log.LoginTime', 'DESC');

        if ($channelId = $this->channel_id) {
            $loginLogs->where('user.RegFrom', $channelId);
        }

        if ($ip = $request->get('ip')) {
            $loginLogs->where(DB::raw('log.LoginIP'), trim($ip));
        }

        $loginLogs = $loginLogs->paginate(20);

        //dd($loginLogs);
        return view('remote.channel.ip-users', compact('menu', 'loginLogs', 'channel'));
    }

    public function operationLog(Request $request)
    {
        $menu = $this->menu;
        $channel = $this->channel;
        $logs = Operation::from(DB::raw('usr_useroperactinfo log'))
                         ->with('role')
                         ->orderBy('log.UserID', 'DESC')
                         ->join(DB::raw('usr_userinfo user'), function ($join) {
                             $join->on(DB::raw('user.UserID'), '=', DB::raw('log.UserID'));
                         });
        if ($userId = $request->get('user_id')) {
            $logs->where(DB::raw('log.UserID'), trim($userId));
        }

        if ($channelId = $this->channel_id) {
            $logs->where('user.RegFrom', $channelId);
        }

        if ($operationType = trim($request->get('operation_class'))) {
            $logs->where('OperClass', $operationType);
        }

        // get all operation class for select form.
        $operations = Operation::select(DB::raw('DISTINCT(OperClass)'))->pluck('OperClass');

        $operations = array_merge(array_combine($operations->all(), $operations->all()), static::$operationMap);
        $logs = $logs->paginate(20);

        return view('remote.channel.operation-log', compact('menu', 'logs', 'channel', 'operations'));
    }

    public static function getOperationNameOrValue($operation)
    {
        return isset(static::$operationMap[$operation]) ? static::$operationMap[$operation] : $operation;
    }

    public function roles(Request $request)
    {
        $menu = $this->menu;
        $channel = $this->channel;

        // order
        if ($request->has('order') && in_array($orderBy = $request->get('order'), ['level', 'crystal'])) {
            $roles = Role::orderBy("role.{$orderBy}", 'DESC');
        } else {
            $roles = Role::orderBy('role.CreateTime', 'DESC');
        }

        $roles->from(DB::raw('usr_userroleinfo role'))
              ->join(DB::raw('usr_userinfo user'), function ($join) {
                  $join->on(DB::raw('user.UserID'), '=', DB::raw('role.UserID'));
              });

        // channel id
        if ($channelId = $this->channel_id) {
            $roles->where('user.RegFrom', $channelId);
        }

        // search
        if ($id = $request->get('user_id')) {
            $roles->where('role.UserID', '=', trim($id));
        } elseif ($name = $request->get('role_name')) {
            $name = trim($name);
            $roles->where('role.RoleName', 'LIKE', "%{$name}%");
        }

        $roles = $roles->paginate(20);

        return view('remote.channel.roles', compact('menu', 'roles', 'channel'));
    }

    public function roleShow($roleId, Request $request)
    {
        $role = Role::findOrFail($roleId);
        // 设置了渠道，但是又没权限，给他弹开。
        if ($this->channel_id && isset($role->user) && $this->channel_id != $role->user->RegFrom) {
            abort('403');
        }
        $menu = $this->menu;
        $channel = $this->channel;
        $role = $this->mapColumnComment($role);
        $goodsList = $role->goods()->get();
        $equipmentsList = $role->equipments()->get();

        return view('remote.channel.role-show', compact('menu', 'role', 'channel', 'goodsList', 'equipmentsList'));
    }

    private function mapColumnComment(Model $model)
    {
        $table = $model->getTable();
        $fullColumn = DB::select("SHOW FULL COLUMNS FROM {$table}");
        $mapped = [];
        foreach ($fullColumn as $column) {
            if (isset($model['attributes'][$key = $column->Field])) {
                $mapped[$column->Comment] = $model['attributes'][$key];
            }
        }
        $model->mappedComment = $mapped;

        return $model;
    }
}
