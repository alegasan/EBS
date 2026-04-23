<?php

namespace App\Models;

use Database\Factories\VenueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'location', 'capacity'])]
class Venue extends Model
{
    /** @use HasFactory<VenueFactory> */
    use HasFactory;
}
