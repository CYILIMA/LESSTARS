<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedMember
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_member_approved) {
            return redirect()->route('members.login')
                ->withErrors(['member_email' => 'Your membership is not currently approved.']);
        }

        return $next($request);
    }
}
