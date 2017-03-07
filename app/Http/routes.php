<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Authentication Routes...
$this->get('login', 'Auth\AuthController@showLoginForm');
$this->post('login', 'Auth\AuthController@login');
$this->get('logout', 'Auth\AuthController@logout');

// ------ Add by abraham, disabled reset password and register from Routing::Auth();
// Registration Routes...
//$this->get('register', 'Auth\AuthController@showRegistrationForm');
//$this->post('register', 'Auth\AuthController@register');

// Password Reset Routes...
//$this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
//$this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
//$this->post('password/reset', 'Auth\PasswordController@reset');
// App
Route::group([
    // All user must logged in first.
    'middleware' => [
        'auth',
        'ability:superadmin,*',
    ],
], function () {

    Route::group([
        'middleware' => ['ability:superadmin,*']
    ], function(){
        // Super admin
        Route::group([
            'prefix' => 'admin',
        ], function () {
            // Users
            Route::group(['middleware' => ['ability:*,admin-users-manage']], function () {
                Route::post('users/change-status', 'UserController@changeStatus');
                Route::resource('users', 'UserController');
            });
            // Roles
            Route::group(['middleware' => ['ability:*,admin-roles-manage']], function () {
                Route::resource('roles', 'RoleController');
            });

            // Server list
            Route::group(['middleware' => ['ability:*,admin-servers-manage']], function () {
                Route::post('servers/change-default', 'ServerController@changeDefault');
                Route::resource('servers', 'ServerController');
            });

            // Channel
            Route::group(['middleware' => ['ability:*,admin-channels-manage']], function () {
                Route::resource('channels', 'ChannelController');
            });

            // System info
            Route::group(['middleware' => ['ability:*,admin-system-info-manage']], function () {
                Route::get('system-info', 'HomeController@systemInfo')->name('admin.system-info');
                Route::get('phpinfo', 'HomeController@phpinfo');
            });
        });

        // Orders
        Route::group([
            'prefix' => 'orders',
            'namespace' => 'Remote\Order'
        ], function () {
            // checking orders
            Route::group(['middleware' => ['ability:*,orders-check-manage']], function () {
                Route::get('check', 'OrderController@check')->name('orders.check');
            });
        });

        // Game
        Route::group([
            'prefix' => 'game',
            'namespace' => 'Remote\Game'
        ], function () {
            // 角色 钻石 物品等。
            Route::get('roles', [
                'middleware' => 'ability:*,game-roles-manage', 'uses' => 'RoleController@index',
            ])->name('game.roles');
            Route::get('roles/{role}', [
                'middleware' => 'ability:*,game-roles-manage', 'uses' => 'RoleController@show',
            ])->name('game.roles.show');


            // 行会
            Route::get('guilds', [
                'middleware' => 'ability:*,game-guilds-manage', 'uses' => 'GuildController@index',
            ])->name('game.guilds');
            // 媒体卡
            Route::get('activecards', [
                'middleware' => 'ability:*,game-activecards-manage', 'uses' => 'ActiveCardController@index',
            ])->name('game.activecards');
            // 登录日志
            Route::get('login-logs', [
                'middleware' => 'ability:*,game-login-logs-manage', 'uses' => 'LoginLogController@index',
            ])->name('game.login-logs');
            // 游戏货币日志
            Route::get('currencies', [
                'middleware' => 'ability:*,game-currencies-manage', 'uses' => 'CurrencyController@index',
            ])->name('game.currencies');
        });

        // Analyze
        Route::group([
            'prefix' => 'analyze',
            'namespace' => 'Remote\Analyze'
        ], function () {
            // 概况。
            Route::get('summarize', [
                'middleware' => 'ability:*,analyze-summarize-manage', 'uses' => 'AnalyzeController@summarize',
            ])->name('analyze.summarize');
            // 概况页面异步数据
            Route::get('summarize/json', [
                'middleware' => 'ability:*,analyze-summarize-manage', 'uses' => 'AnalyzeController@json',
            ])->name('analyze.summarize.json');
            // 半月
            Route::get('two-weeks', [
                'middleware' => 'ability:*,analyze-two-weeks-manage', 'uses' => 'AnalyzeController@twoWeeks',
            ])->name('analyze.two-weeks');
            // 月度
            Route::get('monthly', [
                'middleware' => 'ability:*,analyze-monthly-manage', 'uses' => 'AnalyzeController@monthly',
            ])->name('analyze.monthly');
            // Ltv
            Route::get('ltv', [
                'middleware' => 'ability:*,analyze-ltv-manage', 'uses' => 'AnalyzeController@ltv',
            ])->name('analyze.ltv');
        });

        // Online
        Route::group([
            'prefix' => 'online',
            'namespace' => 'Remote\Online'
        ], function () {
            //  当前数据
            Route::get('current', [
                'middleware' => 'ability:*,online-current-manage', 'uses' => 'OnlineController@current',
            ])->name('online.current');
            // 人数趋势
            Route::get('trending', [
                'middleware' => 'ability:*,online-trending-manage', 'uses' => 'OnlineController@trending',
            ])->name('online.trending');
            // 新老玩家
            Route::get('new', [
                'middleware' => 'ability:*,online-new-manage', 'uses' => 'OnlineController@newPlayers',
            ])->name('online.new');
            // 登录趋势
            Route::get('login', [
                'middleware' => 'ability:*,online-login-manage', 'uses' => 'OnlineController@login',
            ])->name('online.login');
            // 留存率
            Route::get('retention', [
                'middleware' => 'ability:*,online-retention-manage', 'uses' => 'OnlineController@retention',
            ])->name('online.retention');
        });

        // Expense
        Route::group([
            'prefix' => 'expense',
            'namespace' => 'Remote\Expense'
        ], function () {
            // summarize
            Route::get('summarize', [
                'middleware' => 'ability:*,expense-summarize-manage', 'uses' => 'ExpenseController@summarize',
            ])->name('expense.summarize');
            // Hours
            Route::get('hours', [
                'middleware' => 'ability:*,expense-hours-manage', 'uses' => 'ExpenseController@hours',
            ])->name('expense.hours');

            //// expense
            //Route::get('expense', [
            //    'middleware' => 'ability:*,expense-expense-manage', 'uses' => 'ExpenseController@placeholder',
            //])->name('expense.expense');
            // daily-total
            Route::get('daily-total', [
                'middleware' => 'ability:*,expense-daily-total-manage', 'uses' => 'ExpenseController@dailyTotal',
            ])->name('expense.daily-total');
            //// daily-average
            //Route::get('daily-average', [
            //    'middleware' => 'ability:*,expense-daily-average-manage', 'uses' => 'ExpenseController@placeholder',
            //])->name('expense.daily-average');
            // pay-detail
            Route::get('pay-detail', [
                'middleware' => 'ability:*,expense-pay-detail-manage', 'uses' => 'ExpenseController@payDetail',
            ])->name('expense.pay-detail');
            // roles-expense
            Route::get('roles-expense', [
                'middleware' => 'ability:*,expense-roles-expense-manage', 'uses' => 'ExpenseController@roleExpense',
            ])->name('expense.roles-expense');
            // range-pay
            Route::get('range', [
                'middleware' => 'ability:*,expense-range-manage', 'uses' => 'ExpenseController@range',
            ])->name('expense.range');
            // range-percent
            //Route::get('range-percent', [
            //    'middleware' => 'ability:*,expense-range-percent-manage', 'uses' => 'ExpenseController@placeholder',
            //])->name('expense.range-percent');
            // pay-rate
            Route::get('pay-rate', [
                'middleware' => 'ability:*,expense-pay-rate-manage', 'uses' => 'ExpenseController@payRate',
            ])->name('expense.pay-rate');
        });

        // Rank
        Route::group([
            'prefix' => 'rank',
            'namespace' => 'Remote\Rank'
        ], function () {
            // summarize
            Route::get('pay', [
                'middleware' => 'ability:*,rank-pay-manage', 'uses' => 'RankController@pay',
            ])->name('rank.pay');
            // daily
            Route::get('expense', [
                'middleware' => 'ability:*,rank-expense-manage', 'uses' => 'RankController@expense',
            ])->name('rank.expense');
        });

        // channel comparison
        Route::group([
            'prefix' => 'channel-comparison',
            'namespace' => 'Remote\ChannelComparison'
        ], function () {
            // pay-rank
            Route::get('pay-rank', [
                'middleware' => 'ability:*,channel-comparison-pay-rank-manage', 'uses' => 'ChannelComparisonController@payRank',
            ])->name('channel-comparison.pay-rank');
        });

        // channel
        Route::group([
            'prefix' => 'channel',
            'namespace' => 'Remote\Channel'
        ], function () {
            // pay-rank
            Route::get('ip-login', [
                'middleware' => 'ability:*,channel-ip-login-manage', 'uses' => 'ChannelController@ipLogin',
            ])->name('channel.ip-login');
            // ip users
            Route::get('ip-users', [
                'middleware' => 'ability:*,channel-ip-users-manage', 'uses' => 'ChannelController@ipUsers',
            ])->name('channel.ip-users');
            // roles
            Route::get('roles', [
                'middleware' => 'ability:*,channel-roles-manage', 'uses' => 'ChannelController@roles',
            ])->name('channel.roles');
            Route::get('roles/{role}', [
                'middleware' => 'ability:*,channel-roles-manage', 'uses' => 'ChannelController@roleShow',
            ])->name('channel.roles.show');
            // login log
            Route::get('login-log', [
                'middleware' => 'ability:*,channel-login-log-manage', 'uses' => 'ChannelController@loginLog',
            ])->name('channel.login-log');
            // only for get : login log user state
            //Route::get('user-state', [
            //    'middleware' => 'ability:*,channel-login-log-manage', 'uses' => 'ChannelController@changeState',
            //]);
            // order pays
            Route::get('order-pays', [
                'middleware' => 'ability:*,channel-order-pays-manage', 'uses' => 'ChannelController@orderPays',
            ])->name('channel.order-pays');
            // operation log
            Route::get('operation-log', [
                'middleware' => 'ability:*,channel-operation-log-manage', 'uses' => 'ChannelController@operationLog',
            ])->name('channel.operation-log');
            // ltv
            Route::get('ltv', [
                'middleware' => 'ability:*,channel-ltv-manage', 'uses' => 'ChannelController@ltv',
            ])->name('channel.ltv');
        });
    });
    // Homepage
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home/json', 'HomeController@json')->name('home.json');
    Route::get('switch-server', 'UserController@switchServer');
});


Route::get('/api/roles', '\App\Http\Controllers\Remote\Game\RoleController@json')->name('game.roles.json');
