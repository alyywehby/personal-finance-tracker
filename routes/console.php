<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('finance:send-monthly-summary')
    ->monthlyOn(1, '08:00')
    ->description('Send monthly financial summary to all users');
