<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Carbon::setLocale(config('app.locale'));

        $fakerLocale = config('app.faker_locale');
        $fakerLocaleArray = explode('_', $fakerLocale);

        setlocale(LC_ALL, $fakerLocale, $fakerLocaleArray[0], $fakerLocaleArray[1]);
        setlocale(LC_TIME, sprintf('%s.utf8', $fakerLocale));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $this->getSchedule($schedule);
        });

        // Passport
        Passport::enablePasswordGrant();
    }

    private function getSchedule(Schedule $schedule): void
    {
        //
    }
}
