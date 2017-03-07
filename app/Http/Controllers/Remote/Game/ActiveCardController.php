<?php

namespace App\Http\Controllers\Remote\Game;

use App\Entities\Game\ActiveCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

class ActiveCardController extends Controller
{
    public function index(Request $request)
    {
        $menu = $this->menu;
        if ($code = $request->get('code')) {
            $activecards = ActiveCard::where('CardCode', 'LIKE', "%{$code}%")->paginate(20);

        } else {
            $activecards = ActiveCard::paginate(20);
        }
        return view('remote.game.activecards-index', compact('menu', 'activecards'));
    }
}
