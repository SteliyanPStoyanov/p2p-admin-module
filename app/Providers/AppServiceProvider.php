<?php

namespace App\Providers;

use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Core\Traits\StringFormatterTrait;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;

class AppServiceProvider extends ServiceProvider
{
    use StringFormatterTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     *     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        View::share('breadcrumb', $this->getBreadcrumb());
    }

    private function getBreadcrumb(): string
    {
        $segments = request()->segments();
        if (
            is_numeric(end($segments))
            || preg_match('/_/', end($segments))
        ) {
            array_pop($segments);
        }

        $breadcrumb = '';
        $segmentCount = count($segments);
        for ($i = 1; $i < $segmentCount; $i++) {
            if (is_numeric($segments[$i])) {
                continue;
            }
            $str = __('common.' . ucfirst($this->fmtSnakeCaseToCamelCase($segments[$i])));
            if ($i != ($segmentCount - 1)) {
                $str = '<a href="'
                    . url(implode('/', array_slice($segments, 0, $i + 1)))
                    . '">' . $str . '</a>';
            }
            $breadcrumb .= '<li class="breadcrumb-item">' . $str . '</li>';
        }

        return $breadcrumb;
    }
}
