<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ServerRequest;
use App\Server;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = $this->menu;

        $servers = Server::orderBy('id', 'DESC')->paginate(10);

        return view('servers.index', compact('servers', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menu = $this->menu;

        return view('servers.create', compact('menu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServerRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ServerRequest $request)
    {
        $databases = $request->only('host', 'port', 'username', 'pwd', 'database');

        $data = $this->formatConnections($databases);

        $server = Server::create([
            'name'        => $request->get('name'),
            'connections' => $data,
            'start_from' => $request->get('start_from')
        ]);

        //$checkConnections = $this->checkConnections($data);
        //$messageInfo = $this->checkConnectionsMessageInfo($checkConnections);
        //$message = array_merge($messageInfo, ['服务器创建成功！']);

        return redirect('admin/servers')->with('flash_message', '服务器创建成功！');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Server $server
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Server $server)
    {
        $menu = $this->menu;

        $format = $this->formatConnections($server->connections, true);
        foreach ($format as $key => $value) {
            $server->setAttribute($key, $value);
        }
        return view('servers.edit', compact('server', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @param \App\Http\Requests\ServerRequest $request
     * @param \App\Server                      $server
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ServerRequest $request, Server $server)
    {
        $databases = $request->only('host', 'port', 'username', 'pwd', 'database');

        $data = $this->formatConnections($databases);

        $server->update([
            'name'        => $request->get('name'),
            'connections' => $data,
            'start_from' => $request->get('start_from')
        ]);

        return redirect('admin/servers')->with('flash_message', ['服务器修改成功！']);
    }

    public function changeDefault(Request $request)
    {
        $server = Server::find($request->get('id'));

        \DB::table('servers')->update([
            'default' => false
        ]);

        $server->default = true;
        $server->save();

        return redirect('admin/servers')->with('flash_message', '默认服务器设置成功！');
    }
    
    /**
     * @param array $connections
     *
     * @param bool  $arrayToForm
     *
     * @return array
     */
    private function formatConnections(array $connections, $arrayToForm = false)
    {
        $data = [];
        if ($arrayToForm) {
            foreach ($connections as $key => $connection) {
                foreach ($connection as $property => $value) {
                    $data[$property][$key] = $value;
                }
            }

            return $data;
        }

        array_walk($connections, function ($setting, $property) use (&$data) {
            foreach ($setting as $key => $value) {
                $data[$key][$property] = $value;
            }

            return;
        });

        return $data;
    }

    private function checkConnections(array $data)
    {
        $result = [];
        foreach ($data as $key => $connection) {
            $result[$key] = $this->checkConnection($connection);
        }

        return $result;
    }

    private function checkConnection($connection)
    {
        $connection['password'] = $connection['pwd'];
        unset($connection['pwd']);
        $config = app()['config']->get('database.connections.server-default');
        $config = array_merge($config, $connection);
        app()['config']->set('database.connections.server-current', $config);

        try {
            $table = app()['db']->connection('server-current')->select('show tables');
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }

    private function checkConnectionsMessageInfo($checkConnections)
    {
        return [
            [
                $checkConnections[1] ? '游戏数据库连接成功。' : '游戏数据库连接失败。',
                $checkConnections[1] ? 'success' : 'error',
            ],
            [
                $checkConnections[2] ? '游戏数据库连接成功。' : '游戏数据库连接失败。',
                $checkConnections[2] ? 'success' : 'error',
            ],
        ];
    }
}
