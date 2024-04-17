<?php

namespace licon\licon\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use licon\licon\Services\LisSer;
use LaravelReady\LicenseConnector\Support\DomainSupport;

class LisMid
{
    public function handle(Request $request, Closure $next)
    {
        // dd($request->all());
        $isLicenseValid = new LisSer();
        $isLicenseValid = $isLicenseValid->validateL();
        if ($isLicenseValid) {
            return $next($request);
        }

        // return abort(403);
    }
}
