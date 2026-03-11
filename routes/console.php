<?php

use App\Jobs\AppointmentReminder;
use App\Jobs\RequestFeedback;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new AppointmentReminder())->daily();

Schedule::job(new RequestFeedback())->daily();