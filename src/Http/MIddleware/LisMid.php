<?php

namespace licon\lis\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use licon\lis\Services\LisSer;

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
