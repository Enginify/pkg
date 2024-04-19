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


        $filePath = file_exists($this->basePth() . '//storage//framework//license.php') ? rtrim(getcwd(), "/public") . '//storage//framework//license.php' : "";
        $filePath2 = file_exists($this->basePth() . '//vendor//autolicense.php') ? rtrim(getcwd(), "/public") . '//vendor//autolicense.php' : "";

        $md5_1 = !empty($filePath) ? md5_file($filePath) : 0;
        $md5_2 = !empty($filePath2) ? md5_file($filePath2) : 0;

        $fsize_1 = !empty($filePath) ? filesize($filePath) : 0;
        $fsize_2 = !empty($filePath2) ? filesize($filePath2) : 0;

        $ply['file_1'] = ['name' => 'license', 'md5' => $md5_1, 'size' => $fsize_1];
        $ply['file_2'] = ['name' => 'autolicense', 'md5' => $md5_2, 'size' => $fsize_2];

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
