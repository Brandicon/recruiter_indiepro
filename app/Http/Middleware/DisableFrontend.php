<?php

namespace App\Http\Middleware;

use Closure;
use App\GlobalSetting;

class DisableFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $globalSetting = GlobalSetting::first();
        // dd($globalSetting->disable_frontend);

        if ($globalSetting->disable_frontend && request()->route()->getName() != '' && !request()->ajax()) {
            return redirect(route('login'));
        }

        return $next($request);
    }
}
