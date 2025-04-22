<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $orderItem = $request->route('orderItem');

        if (!$orderItem || $orderItem->order->user_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงทรัพยากรนี้');
        }

        return $next($request);
    }
}
