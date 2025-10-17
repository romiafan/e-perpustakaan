<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationService $reservations)
    {
        // Authentication enforced at route level.
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        try {
            $reservation = $this->reservations->createReservation($request->user(), (int) $request->input('book_id'));

            // For Inertia requests, redirect back with success message
            if ($request->header('X-Inertia')) {
                return redirect()->back()->with('success', 'Book reserved successfully! Your reservation expires on ' . $reservation->expires_at->format('M d, Y'));
            }

            // For API requests, return JSON
            return response()->json([
                'message'     => 'Book reserved successfully',
                'reservation' => [
                    'id'         => $reservation->id,
                    'expires_at' => $reservation->expires_at->toIso8601String(),
                ],
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            // For Inertia requests, redirect back with error
            if ($request->header('X-Inertia')) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            // For API requests, return error JSON
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function index(Request $request)
    {
        $groups = $this->reservations->listUserReservations($request->user());

        return response()->json([
            'active'  => $groups['active']->map(fn ($r) => $this->transformReservation($r)),
            'history' => $groups['history']->map(fn ($r) => $this->transformReservation($r)),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $reservation = Reservation::with('book')->findOrFail($id);
        $validator   = Validator::make($request->all(), [
            'status' => ['required', 'in:cancelled,collected'],
        ]);
        if ($validator->fails()) {
            // For Inertia requests, redirect back with errors
            if ($request->header('X-Inertia')) {
                return redirect()->back()->withErrors($validator->errors());
            }

            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $reservation = $this->reservations->updateReservationStatus($request->user(), $reservation, $request->input('status'));

            // For Inertia requests, redirect back with success message
            if ($request->header('X-Inertia')) {
                $message = $request->input('status') === 'cancelled'
                    ? 'Reservation cancelled successfully'
                    : 'Reservation marked as collected';

                return redirect()->back()->with('success', $message);
            }

            // For API requests, return JSON
            return response()->json([
                'reservation' => [
                    'id'     => $reservation->id,
                    'status' => $reservation->status,
                ],
                'message' => 'Reservation updated',
            ]);
        } catch (\RuntimeException $e) {
            // For Inertia requests, redirect back with error
            if ($request->header('X-Inertia')) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            if ($e->getMessage() === 'Cannot modify this reservation') {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
            }

            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function transformReservation(Reservation $r): array
    {
        return [
            'id'   => $r->id,
            'book' => [
                'id'     => $r->book->id,
                'title'  => $r->book->title,
                'author' => $r->book->author,
                'genre'  => $r->book->genre,
            ],
            'status'         => $r->status,
            'reserved_at'    => $r->reserved_at->toIso8601String(),
            'expires_at'     => $r->expires_at->toIso8601String(),
            'collected_at'   => $r->collected_at?->toIso8601String(),
            'days_remaining' => $r->isActive() ? now()->diffInDays($r->expires_at, false) : null,
        ];
    }
}
