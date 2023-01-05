<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FtpAuth
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
        try {
            config([
                'filesystems.disks.ftp.username' => session()->get('username'),
                'filesystems.disks.ftp.password' => session()->get('password')
            ]);
            Storage::directories();
            return $next($request);
        } catch (\Throwable $th) {
            return redirect()->route('login')->with([
                'errorMessage' => "Unable to identify credentials"
            ]);
        }
    }
}
