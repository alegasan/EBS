<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Exception;

class EventServices
{
    public function __construct(
        protected EventRepositoryInterface $eventRepo,
    ) {}

    public function getAllEvents()
    {
        return $this->eventRepo->getAll();
    }


    

}