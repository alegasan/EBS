<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Services\BookingService;
use Exception;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
    ) {}

    public function index()
    {

        $perPage = min(max((int) request()->get('per_page', 15), 1), 100);

        $bookings = $this->bookingService->getAllBookings($perPage);

        return Inertia::render('Bookings/Index',
            [
                'bookings' => $bookings,
            ]
        );
    }

    public function create()
    {
        return Inertia::render('Bookings/Create');
    }

    public function store(BookingRequest $request)
    {
        try {
            $booking = $this->bookingService->book($request->validated());

            return response()->json(['message' => 'Booking successful', 'booking' => $booking], 201);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Booking failed',
            ], 409);
        }
    }

    public function confirm(int $id)
    {
        try {
            $booking = $this->bookingService->confirm($id);

            return response()->json([
                'message' => 'Booking confirmed successfully',
                'booking' => $booking,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Booking not found',
            ], 404);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Confirmation failed',
            ], 409);
        }
    }

    public function cancel(int $id)
    {
        try {
            $this->bookingService->cancel($id);

            return response()->json(['message' => 'Booking cancelled successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Booking not found',
            ], 404);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Cancellation failed',
            ], 409);
        }
    }
}
