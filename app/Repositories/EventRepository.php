<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

class EventRepository implements EventRepositoryInterface
{
    public function getAll()
    {
        return Event::with(['venue', 'bookings'])->latest()->get();
    }

    public function findById(int $id)
    {
        return Event::with(['venue', 'bookings', 'attendees'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Event::create($data);
    }

    public function update(int $id, array $data)
    {
        $event = $this->findById($id);
        $event->update($data);

        return $event;
    }

    public function delete(int $id)
    {
        return Event::destroy($id);
    }

    public function findByIdForUpdate(int $id)
    {
        return Event::lockForUpdate()->find($id);
    }

  
    public function getUpcomingEvents()
    {
        return Event::with('venue')
            ->where('status', 'upcoming')
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->get();
    }

   
    public function filterByDateRange(string $from, string $to)
    {
        return Event::with('venue')
            ->whereBetween('start_date', [$from, $to])
            ->orderBy('start_date')
            ->get();
    }

   
    public function getAvailable()
    {
        return Event::with('venue')
            ->where('status', 'upcoming')
            ->where('start_date', '>', now())
            ->withCount([
                'bookings as booked_seats' => fn ($q) => $q->where('status', '!=', 'cancelled')
                    ->select(DB::raw('COALESCE(SUM(seats), 0)')),
            ])
            ->havingRaw('max_attendees > COALESCE(booked_seats, 0)')
            ->get();
    }

  
    public function getByVenue(int $venueId)
    {
        return Event::with('venue')
            ->where('venue_id', $venueId)
            ->latest()
            ->get();
    }

    
    public function searchByTitle(string $keyword)
    {
        return Event::where('title', 'like', "%{$keyword}%")
            ->with('venue')
            ->get();
    }


}
