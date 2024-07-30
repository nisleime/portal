<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CheckSystem
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $site_atual = base64_encode(URL::to('http://localhost:8000'));

        if ($site_atual == 'aHR0cDovL2xvY2FsaG9zdDo4MDAw') {
            return $next($request);
        }

        $retorno = [];

        return response()->json($retorno);
    }
}
