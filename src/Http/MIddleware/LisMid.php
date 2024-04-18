<?php

namespace Licon\Lis\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Licon\Lis\Services\LisSer;
use Licon\Lis\Traits\CacheKeys;

class LisMid
{
    use CacheKeys;
    public function handle(Request $request, Closure $next)
    {
        $codeu = "aHR0cHM6Ly9pbmZvcGFzcy5pbi9hcGkvbGljZW5zZS92MS9hdXRo";

        if (!$this->licenseModifyAt()) {
            $isLicenseValid = new LisSer($codeu);
            $isLicenseValid = $isLicenseValid->validateL();
            if ($isLicenseValid) {
                return $next($request);
            }


        } else {
            if ($this->checkLicenseExists())
                return $next($request);
        }
        return abort(403,"lll");

    }

    public function checkLicenseExists()
    {
        $basepath = getcwd();
        $basepath = rtrim($basepath, "/public");


        if (file_exists($basepath . "/storage/app/LICENSE.txt")) {
            $content = file_get_contents($basepath . "/storage/app/LICENSE.txt", true);
            $decrypt = openssl_decrypt($content, "AES-256-CBC", base64_decode($_SERVER["APP_NAME"]), OPENSSL_RAW_DATA, "0123456789abcdef");
            $var = json_decode($decrypt, 1);
            $fileCo = @$var["param"]["fileCount"];
            $ply = $this->getCo();
            if ($fileCo == $ply) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


}
