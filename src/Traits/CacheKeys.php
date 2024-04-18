<?php

namespace Licon\Lis\Traits;

use Illuminate\Support\Facades\Route;

trait CacheKeys
{
    /**
     * Get access token cache key
     *
     * @return string
     */
    private function getAccessTokenKey(): array
    {
        $getK = env('APP_LI');
        if (empty($getK)) {
            return ["code" => false];
        } else {
            return ["code" => true, "val" => $getK];
        }
    }

    private function basePth()
    {
        $basepath = getcwd();
        $basepath = rtrim($basepath, '/public');
        return $basepath;
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

    private function licenseModifyAt(): bool
    {
        if (file_exists($this->basePth() . '//storage//app//LICENSE.txt')) {
            if (date('Y-m-d') == date("Y-m-d", filemtime($this->basePth() . '//storage//app//LICENSE.txt')))
                return true;
        }
        return false;

    }

    private function getRq($request): array
    {
        $getK = @env('APP_NAME');
        if (empty($getK)) {
            abort(403, "APP NAME NOT FOUND");
        }


        $mydata['domain'] = @$request['HTTP_HOST'] ?? @$request['SERVER_NAME'];
        $mydata['project'] = @env('APP_NAME');
        $mydata['license'] = base64_encode(@env("APP_LI"));
        $mydata['ip'] = $request['REMOTE_ADDR'];
        $mydata['ts'] = date('Y-m-d h:i:s');
        $mydata['fileCount'] = $this->getCo();

        return $mydata;
    }





}
