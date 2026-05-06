<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('arfims:scan-all')->dailyAt('02:00');
