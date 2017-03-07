<?php

if (!function_exists('is_active')) {
    /**
     * 返回一个菜单是否应该加上 active，通过识别路由名称。
     *
     * @param mixed $routeName
     *
     * @return boolean
     */
    function is_active($routeName)
    {
        //dd(app()['request']->route());
        $keys = is_array($routeName) ? $routeName : [$routeName];
        $requestRouteName = app()['request']->route()->getName();
        $splitRouteName = explode('.', $requestRouteName);
        $shouldActive = false;
        foreach ($keys as $key) {
            $requestRouteResult = count($splitRouteName) > 1 ? $splitRouteName[0] . '.' . $splitRouteName[1] : $splitRouteName[0];
            if ($key === ($requestRouteResult)) {
                $shouldActive = true;
                break;
            }
        }

        return $shouldActive;
    }
}

if (!function_exists('random_color')) {
    function random_color()
    {
        $rand = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
        $color = '#' . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)];

        return $color;
    }
}
