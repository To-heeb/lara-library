<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Library;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ValidateLibrary
{
    use HttpResponses;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $library_id = Library::getLibrary();

        if ($library_id != Auth::user()->library_id && Auth::user()->role != "admin")
            return $this->error('', "You are not authorized to make this request", Response::HTTP_UNAUTHORIZED);;

        return $next($request);
    }
}
