<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Requests\ChannelRequest;
use Illuminate\Http\Request;

use App\Http\Requests;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = $this->menu;

        $channels = Channel::paginate(10);
        return view('channels.index', compact('menu', 'channels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menu = $this->menu;

        return view('channels.create', compact('menu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @param \App\Http\Requests\ChannelRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ChannelRequest $request)
    {
        Channel::create([
            'name' => $request->get('name'),
            'channel_id' => $request->get('channel_id'),
        ]);

        return redirect('admin/channels')->with('flash_message', ['渠道创建成功！']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Channel $channel
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Channel $channel)
    {
        $menu = $this->menu;

        return view('channels.edit', compact('menu', 'channel'));
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @param \App\Http\Requests\ChannelRequest $request
     * @param \App\Channel                      $channel
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ChannelRequest $request, Channel $channel)
    {
        $channel->update($request->all());

        return redirect('admin/channels')->with('flash_message', '修改渠道成功！');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
