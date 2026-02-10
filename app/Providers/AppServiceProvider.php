<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // DeepPerformance: Disable debug bar in production
        if ($this->app->isProduction()) {
            $this->app['config']->set('app.debug', false);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // DeepSecurity: Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // DeepPerformance: STRICT MODE - Throw exception on lazy loading
        // Reasoning: Force eager loading untuk menghindari N+1 queries ke TiDB Frankfurt
        Model::preventLazyLoading(!$this->app->isProduction()); // Disable in production to avoid crashes

        // DeepPerformance: Disable unnecessary Eloquent events
        Model::preventAccessingMissingAttributes(!$this->app->isProduction());

        // DeepPerformance: Query optimization - Log slow queries only in dev
        if (!$this->app->isProduction()) {
            DB::listen(function ($query) {
                if ($query->time > 1000) { // > 1 second
                    logger()->warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }

        // DeepPerformance: Share cached config to all views
        // Reasoning: Avoid re-fetching config on every view render
        View::composer('*', function ($view) {
            $view->with('appName', config('app.name'));
        });

        // DeepPerformance: Preload critical config into memory
        $this->preloadCriticalConfig();
    }

    /**
     * Preload critical configuration into array cache.
     * 
     * DeepReasoning: Config values tidak berubah selama request lifecycle.
     * DeepTeknik: Load once, use everywhere.
     * 
     * @return void
     */
    protected function preloadCriticalConfig(): void
    {
        // Cache config for request lifecycle (array driver)
        Cache::store('array')->rememberForever('critical_config', function () {
            return [
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
            ];
        });
    }

}
