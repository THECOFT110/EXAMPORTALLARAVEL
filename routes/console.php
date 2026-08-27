<?php

use App\Jobs\ExpireUnpaidFeesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Schedule background tasks
Schedule::job(new ExpireUnpaidFeesJob)->daily();
Schedule::command('cache:prune-stale-tags')->hourly();
