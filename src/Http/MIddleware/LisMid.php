<?php

namespace Licon\Lis\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Licon\Lis\Services\LisSer;

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
