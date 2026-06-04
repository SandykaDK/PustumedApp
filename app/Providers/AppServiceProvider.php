<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set custom pagination view
        Paginator::useBootstrap();

        // Use default session-based authentication for web routes.

        View::composer('components.sidebar', function ($view) {
            $pendingPemusnahanCount = 0;
            $approvedPemusnahanCount = 0;

            if (Auth::check()) {
                if (Auth::user()?->role === 'kepala_pustu') {
                    $pendingPemusnahanCount = DB::table('pemusnahan_obat')
                        ->where('status', 'pending')
                        ->count();
                }

                if (Auth::user()?->role === 'petugas_obat') {
                    $approvedPemusnahanCount = DB::table('pemusnahan_obat')
                        ->where('status', 'approved')
                        ->count();
                }
            }

            $view->with('sidebarPendingPemusnahanCount', $pendingPemusnahanCount)
                 ->with('sidebarApprovedPemusnahanCount', $approvedPemusnahanCount);
        });
    }
}
