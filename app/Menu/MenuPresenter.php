<?php

namespace App\Menu;

use App\Server;
use Entrust;

/**
 * Class MenuPresenter
 * 菜单视图表示层。
 *
 * @package App\Menu
 */
class MenuPresenter implements MenuPresenterInterface
{
    /**
     * 菜单配置。
     *
     * @var \App\Menu\Menu
     */
    private $menu;

    /**
     * 活动的菜单名称
     *
     * @var string
     */
    private $activeName = '';

    /**
     * 菜单 HTML。
     * @var string
     */
    private $html = '';

    /**
     * MenuPresenter constructor.
     *
     * @param \App\Menu\Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu->toArray();
        // 我们首先把子菜单使用的所有路由全部附加到父菜单上，这样如果一个子菜单应该是
        // 活动状态的时候（active），其父菜单也能对应的加亮。
        $this->menu = $this->payloadParentRoutesFromChild($this->menu);
        // 然后我们决断一个菜单对当前用户是否可见，并将这种效果同时影响到父菜单上（如果有）。
        $this->menu = $this->applyPermissionDisplay($this->menu);
        $this->html = $this->init();
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * 返回菜单 HTML。
     *
     * @return string
     */
    public function render()
    {
        return $this->html ?: $this->html = $this->init();
        // TODO: Implement render() method.
    }

    /**
     * 初始化菜单 HTML。
     *
     * @return string
     */
    public function init()
    {
        $html = '';

        foreach ($this->menu as $item) {
            if ($item['show']) {
                if ($this->hasChild($item)) {
                    $html .= $this->buildChildItem($item);
                } else {
                    $html .= $this->buildTopLevelItem($item);
                }
            }
        }

        return $html;
    }

    /**
     * 一个菜单是否有子菜单。
     *
     * @param array $item
     *
     * @return bool
     */
    private function hasChild(array $item)
    {
        return isset($item['child'])
        && is_array($child = $item['child'])
        && count($child) > 0;
    }

    /**
     * 获取一个对应菜单的子菜单。
     *
     * @param array $item
     *
     * @return array
     */
    private function getChild(array $item)
    {
        return $item['child'];
    }

    /**
     * 构造顶层菜单 HTML。
     *
     * @param array $item
     *
     * @return string
     */
    private function buildTopLevelItem(array $item)
    {
        $url = $this->getUrl($item['url']);
        $icon = isset($item['icon']) ? "<i class=\"{$item['icon']}\"></i>" : '';
        $name = $this->getDisplayName($item);
        $active = $this->getActive($item);
        if (is_active($item['routes'])) {
            $this->activeName = $name;
        }

        return "<li{$active}><a href=\"{$url}\">{$icon}<span class=\"nav-label\">{$name}</span></a></li>\n";
    }

    /**
     * 构造每个子菜单的 HTML。
     *
     * @param array $item
     *
     * @return string
     */
    private function buildChildItem(array $item)
    {
        $html = '';
        $name = $this->getDisplayName($item);
        $icon = $this->getIcon($item);
        $active = $this->getActive($item);
        $collapse = $this->getCollapse($item);

        $html .= "<li{$active}>
  <a href=\"#\">{$icon}<span class=\"nav-label\">{$name}</span><span class=\"fa arrow\"></span></a>
  <ul class=\"nav nav-second-level{$collapse}\">\n";
        foreach ($this->getChild($item) as $itemChild) {
            if ($itemChild['show']) {
                $childUrl = $this->getUrl(($item['url'] ? $item['url'] . '/' : '') . $itemChild['url']);
                $childName = $this->getDisplayName($itemChild);
                $childActive = $this->getActive($itemChild);
                if (is_active($itemChild['routes'])) {
                    $this->activeName = $childName;
                }

                $html .=     "<li{$childActive}><a href=\"{$childUrl}\">{$childName}</a></li>\n";

            }
        }
        $html .= "</ul></li>\n";

        return $html;
    }

    /**
     * 获取一个菜单的 URL。
     *
     * @param string $url
     *
     * @return string
     */
    private function getUrl($url)
    {
        return isset($url) ? url($url) : '';
    }

    /**
     * 获取一个菜单的显示名称。
     *
     * @param array $item
     *
     * @return string
     */
    private function getDisplayName(array $item)
    {
        return isset($item['display_name']) ? $item['display_name'] : '';
    }

    /**
     * 获取一个菜单的图标。
     *
     * @param array $item
     *
     * @return string
     */
    private function getIcon(array $item)
    {
        return isset($item['icon']) ? "<i class=\"{$item['icon']}\"></i>" : '';
    }

    /**
     * 子菜单使用的所有路由全部附加到父菜单上。
     *
     * @param array $menu
     *
     * @return array
     */
    private function payloadParentRoutesFromChild(array $menu)
    {
        foreach ($menu as $key => $item) {
            $prefix = is_array($menu[$key]['routes']) ? $menu[$key]['routes'][0] : $menu[$key]['routes'];
            $menu[$key]['routes'] = [];
            if ($this->hasChild($item)) {
                foreach ($this->getChild($item) as $itemKey => $childItem) {
                    $childItemRoute = is_array($childItem['routes']) ? $childItem['routes'][0] : $childItem['routes'];
                    array_push($menu[$key]['routes'], ($prefix ? $prefix. '.' : '') . $childItemRoute);
                    // 如果父级菜单有路由，也附加给子菜单
                    $menu[$key]['child'][$itemKey]['routes'] = [($prefix ? $prefix . '.' : '') . $childItemRoute];
                }
            } else {
                $menu[$key]['routes'] = $prefix;
            }
        }
        return $menu;
    }

    /**
     * 判断一个菜单是否是活动状态。
     *
     * Note: 这个方法之前需要执行 {self::payloadParentRoutesFromChild()} 以便自动为父级菜单
     * 附加路由。
     *
     * @param array $item
     *
     * @return string
     */
    private function getActive(array $item)
    {
        return is_active($item['routes']) ? ' class="active"' : '';
    }

    /**
     * 获取一个菜单是否应该折叠（用于 CSS，一个菜单如果是活动状态则不该折叠）。
     *
     * @param array $item
     *
     * @return string
     */
    private function getCollapse($item)
    {
        return is_active($item['routes']) ? '' : ' collapse';
    }

    /**
     * 为每一个菜单判断是否对于当前用户显示，并把结果增加到菜单的 $item['show'] 上。
     *
     * @param array $menu
     *
     * @return array
     */
    private function applyPermissionDisplay(array $menu)
    {
        // 我们遍历所有菜单和其子菜单，如果一个菜单对当前用户可见，我们就设置 show 为真。
        // 如果一个父级菜单的任何子菜单应该对用户可见，父菜单也应该为可见。
        foreach ($menu as $key => $item) {
            $menu[$key]['show'] = false;
            if ($this->hasChild($item)) {
                $childShow = false;

                foreach ($this->getChild($item) as $childKey => $childItem) {
                    $menu[$key]['child'][$childKey]['show'] = false;
                    if (Entrust::can($childItem['permission_key'] . '-manage')) {
                        $childShow = true;
                        $menu[$key]['child'][$childKey]['show'] = true;
                    }
                }
                $menu[$key]['show'] = $childShow;
            } else {
                $menu[$key]['show'] = Entrust::can($item['permission_key'] . '-manage');
            }
        }

        return $menu;
    }

    /**
     * @return string
     */
    public function getActiveName()
    {
        return $this->activeName;
    }

    /**
     * Servers
     */
    public function servers()
    {
        return Server::pluck('name', 'id');
    }

    /**
     * 对当前用户可用的服务器
     */
    public function availableServers()
    {
        $servers = \Auth::user()->available_servers;
        return $availabel = Server::whereIn('id', $servers)->pluck('name', 'id');
    }

    public function selectedServer()
    {
        if (!$id = \Auth::user()->selected_server) {
            return;
        }
        return  Server::find($id)->name;
    }
}
