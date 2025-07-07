<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();
// * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
// while true; do php artisan schedule:run; sleep 60; done


Schedule::command('send:sareeWhatsAppMessage')->dailyAt('08:00');
// Schedule::command('send:sareeWhatsAppMessage')->everyMinute();