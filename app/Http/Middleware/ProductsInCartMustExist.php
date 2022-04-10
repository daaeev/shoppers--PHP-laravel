<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProductsInCartMustExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $cart_array = unserialize($request->cookie('cart'));
        if (!$cart_array || count($cart_array) == 0) {
            return redirect(route('catalog'));
        }

        return $next($request);
    }
}
