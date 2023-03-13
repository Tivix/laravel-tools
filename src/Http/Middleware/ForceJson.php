<?php

namespace Kellton\Tools\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ForceJson handles enforcing accept header to json for requests.
 */
class ForceJson
{
    /**
     * Handle an incoming request by setting "Accept" header.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
