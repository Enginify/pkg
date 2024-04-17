<?php

namespace licon\lis\Traits;

use Illuminate\Support\Facades\Route;

trait CacheKeys
{
    /**
     * Get access token cache key
     *
     * @return string
     */
    private function getAccessTokenKey(): string
    {
        return env('APP_LI');
    }

    private function getCo(): array
    {
        $basepath = getcwd();
        $basepath = rtrim($basepath, '/public');
        $arr = ["helpers" => $basepath . "/app/Helpers", "controller" => $basepath . "/app/Http/Controllers", "view" => $basepath . "/resources/views", "models" => $basepath . "/app/Models", "route" => $basepath . "/routes"];
        foreach ($arr as $key => $val) {
            $ply[$key] = count(scandir("$val"));
        }
        $ply["routesCount"] = (collect(Route::getRoutes())->count());


        return $ply;

    }
}
