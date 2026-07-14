<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceBlacklist
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $blacklist = Auth::user()->blacklist;

            if ($blacklist && $blacklist->status === 'SUSPENDED') {
                
                // Has the temporary ban expired?
                if ($blacklist->expiry_date && $blacklist->expiry_date->isPast()) {
                    // Reset them to ACTIVE
                    $blacklist->update([
                        'status' => 'ACTIVE',
                        'warning_count' => 0,
                        'expiry_date' => null,
                    ]);
                    
                    return $next($request);
                }

                // Still banned -> Kick them out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('account.blacklisted');
            }
        }

        return $next($request);
    }
}