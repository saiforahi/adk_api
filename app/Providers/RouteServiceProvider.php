<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/brand.php'));

            Route::middleware('api')->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/category.php'));
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/product.php'));
            Route::middleware('api')
                ->prefix('api/supplier')
                ->namespace($this->namespace)
                ->group(base_path('routes/supplier.php'));
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/warehouse.php'));
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/purchaseOrder.php'));
            Route::middleware('api')
                ->prefix('api/dealer')
                ->namespace($this->namespace)
                ->group(base_path('routes/dealer.php'));
            Route::middleware('api')
                ->prefix('api/pre_n_sub')
                ->namespace($this->namespace)
                ->group(base_path('routes/pre_n_sub_dealers.php'));
            Route::middleware('api')
                ->prefix('api/dashboard')
                ->namespace($this->namespace)
                ->group(base_path('routes/dashboard.php'));
            Route::middleware('api')
                ->prefix('api/commission')
                ->namespace($this->namespace)
                ->group(base_path('routes/commission.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
