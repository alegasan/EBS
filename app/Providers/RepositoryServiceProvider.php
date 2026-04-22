<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\EventRepository;
use App\Repositories\BookingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventRepositoryInterface::class,   EventRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
    }
}