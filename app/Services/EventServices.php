<?php

namespace App\Services;

use App\Repositories\Interfaces\EventRepositoryInterface;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class EventServices
{
    public function __construct(
        protected EventRepositoryInterface $eventRepo,
    ) {}

    public function getAllEvents()
    {
        return $this->eventRepo->getAll();
    }

    public function getEventById($id)
    {
        return $this->eventRepo->findById($id);
    }

    public function createEvent(array $data)
    {
        return $this->eventRepo->create($data);
    }

    public function deleteEvent($id)
    {
        $event = $this->eventRepo->findById($id);
        if (! $event) {
            throw new Exception('Event not found');
        }

        return $this->eventRepo->delete($id);
    }

    public function updateEvent($id, $status)
    {
        $event = $this->eventRepo->findById($id);
        if (! $event) {
            throw new Exception('Event not found');
        }

        return $this->eventRepo->update($id, ['status' => $status]);
    }

    public function getUpcomingEvents()
    {
        return $this->eventRepo->getUpcomingEvents();
    }

    public function filterEventsByDateRange($from, $to)
    {
        return $this->eventRepo->filterByDateRange($from, $to);
    }

    public function getAvailableSeats($eventId)
    {
        return $this->eventRepo->getAvailableByEventId($eventId);
    }

    public function searchByTitle($title)
    {
        return $this->eventRepo->searchByTitle($title)->paginate();
    }

    public function getPaginatedEvents(int $perPage = 15): LengthAwarePaginator
    {
        return $this->eventRepo->paginate($perPage);
    }
}
