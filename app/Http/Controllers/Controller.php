<?php

namespace App\Http\Controllers;

use App\Menu\MenuPresenter;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * @var \App\Menu\MenuPresenter
     */
    public $menu;

    public function __construct(MenuPresenter $menu)
    {
        $this->menu = $menu;
    }
}
