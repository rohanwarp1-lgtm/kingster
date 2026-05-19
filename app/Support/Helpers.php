<?php

if (!function_exists('module_path')) {
    function module_path($module, $path = '')
    {
        $modulePath = app_path("Modules/{$module}");
        
        if ($path) {
            return $modulePath . '/' . $path;
        }
        
        return $modulePath;
    }
}

if (!function_exists('module_asset')) {
    function module_asset($module, $path)
    {
        return asset("modules/{$module}/{$path}");
    }
}
