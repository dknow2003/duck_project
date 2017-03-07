<?php

namespace App\Menu;

/**
 *
 * Interface MenuPresenterInterface
 *
 * @package App\Menu
 */
interface MenuPresenterInterface
{
    /**
     * 返回一个菜单的 HTML 最终内容。
     *
     * @return string
     */
    public function render();
}
