<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\MarkFinishedBookings;
use App\Console\Commands\RefreshRuangStatus;

/**
 * Schedule Commands
 * 
 * Commands ini akan berjalan otomatis jika cron job sudah disetup
 * Cron: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
 */

// Mark finished bookings dan return default users - setiap 5 menit
Schedule::command(MarkFinishedBookings::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('MarkFinishedBookings: Command executed successfully');
    })
    ->onFailure(function () {
        \Log::error('MarkFinishedBookings: Command failed');
    });

// Refresh room status - setiap 10 menit
Schedule::command(RefreshRuangStatus::class)
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('RefreshRuangStatus: Command executed successfully');
    })
    ->onFailure(function () {
        \Log::error('RefreshRuangStatus: Command failed');
    });

/**
 * Artisan Commands
 * 
 * Custom artisan commands yang bisa dijalankan manual
 */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:mark-finished', function () {
    $this->call(MarkFinishedBookings::class);
})->purpose('Mark finished bookings and update room status');
