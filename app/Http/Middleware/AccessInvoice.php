<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessInvoice
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
        $document = DB::table('documents')->where('id', '=', $request->id)->first();

        if (is_null($document)) {
            abort(404);
        }

        if (!auth('web')->check()) {
            abort(403);
        }

        $user = auth('web')->user();

        if ($user->is_admin == "N") {
            if ($user->companies->where('cnpj_cpf', $document->cnpj_cpf)->first() == null) {
                abort(403);
            }
        }

        return $next($request);
    }
}
