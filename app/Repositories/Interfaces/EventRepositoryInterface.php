<?php

namespace App\Repositories\Interfaces;

interface EventRepositoryInterface
{
    public function getAll();

    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function findByIdForUpdate(int $id);

    public function getUpcomingEvents();

    public function filterByDateRange(string $from, string $to);

    public function getAvailable();

    public function getByVenue(int $venueId);

    public function searchByTitle(string $keyword);

    public function Paginate(int $perPage = 15);
}
