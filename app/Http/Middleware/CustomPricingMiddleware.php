<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class CustomPricingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user_type = $request->user()->type;
        $product_price = $request->product->price;
        switch ($user_type) {
            case 'gold':
                $product_price = $product_price * 0.8;
                break;
            case 'silver':
                $product_price = $product_price * 0.9;
                break;
        }
        $request->product->price = $product_price;
        return $next($request);
    }
}
