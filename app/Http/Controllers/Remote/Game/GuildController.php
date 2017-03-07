<?php

namespace App\Http\Controllers\Remote\Game;

use App\Entities\Game\Guild;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GuildController extends Controller
{
    public function index()
    {
        $menu = $this->menu;
        $guilds = Guild::paginate(20);

        return view('remote.game.guilds-index', compact('menu', 'guilds'));
    }
}
