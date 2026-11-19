<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

if (!function_exists('format_jp')) {
    function format_jp($val): string
    {
        if ($val === null || $val === '') {
            return '0';
        }
        $num = (float) $val;
        return (string) ($num == (int) $num ? (int) $num : (float) $num);
    }
}

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
        Paginator::useBootstrapFive();

        Blade::directive('jp', function ($expression) {
            return "<?php echo format_jp($expression); ?>";
        });
    }
}
