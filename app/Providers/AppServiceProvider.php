<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('option', function (string $expression) {
            $params = explode(',', $expression);
            $key = trim($params[0]);
            $default = $params[1] ?? "''";
            return "<?php echo \App\Models\Option::getValue($key, $default); ?>";
        });
    }
}
