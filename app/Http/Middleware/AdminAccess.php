<?php

namespace App\Http\Middleware;

use App\Services\Interfaces\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;

class AdminAccess
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->userRepository->getAuthenticated()?->isAdmin()) {
            return redirect(route('home'));
        }

        return $next($request);
    }
}
