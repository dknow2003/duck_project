<?php

use App\Permission;
use Illuminate\Database\Seeder;

/**
 * 根据导航生成对应的权限数据。
 */
class PermissionsSeeder extends Seeder
{
    /**
     * @var \App\Menu\Menu 目录数据
     */
    private $menu;

    public function __construct(\App\Menu\Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->menu->toArray() as $menu) {
            if (isset($menu['child']) && is_array($menu['child'])) {
                $this->createPermissions($menu['child']);
                continue;
            }

            $this->createPermission($menu['permission_key'], $menu['display_name']);
        }
    }

    /**
     * 为一组导航创建权限。
     *
     * @param array  $subMenu
     *
     * @return void
     * @see \App\Menu\Menu::menu
     */
    private function createPermissions(array $subMenu)
    {
        foreach ($subMenu as $menu) {
            $this->createPermission($menu['permission_key'], $menu['display_name']);
        }
    }

    /**
     * 为一个导航创建权限。
     *
     * @param string      $name        唯一权限标识。
     * @param string|null $displayName 权限名称。
     * @param string|null $description 简介。
     *
     * @return  void
     */
    protected function createPermission($name, $displayName = null, $description = null)
    {
        foreach ($this->getOperates() as $operate) {
            Permission::create([
                'name'         => "{$name}-{$operate}",
                'display_name' => $displayName ?: null,
                'description'  => $description ? "" : null,
            ]);
        }
    }

    /**
     * 操作名
     *
     * @return array
     */
    private function getOperates()
    {
        return [
            'manage',
        ];
    }
}
