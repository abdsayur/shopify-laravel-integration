<?php

namespace App\Http\Middleware;

use Closure;

class AddLocaltonetHeader
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
        $response = $next($request);

        // Add the custom header to skip the warning page
        $response->headers->set('alocaltonet-skip-warningrequest', 'true');

        return $response;
    }
}
