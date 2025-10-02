<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationService $reservations)
    {
        // Authentication enforced at route level.
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $reservation = $this->reservations->createReservation($request->user(), (int) $request->input('book_id'));
        } catch (\RuntimeException $e) {
            $key = str_contains($e->getMessage(), 'active reservation') ? 'user' : 'book_id';
            return response()->json([
                'errors'  => [$key => [$e->getMessage()]],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'reservation' => [
                'book'       => [
                    'id'     => $reservation->book->id,
                    'title'  => $reservation->book->title,
                    'author' => $reservation->book->author,
                ],
                'status'      => $reservation->status,
                'reserved_at' => $reservation->reserved_at->toIso8601String(),
                'expires_at'  => $reservation->expires_at->toIso8601String(),
            ],
            'message' => 'Reservation created',
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $groups = $this->reservations->listUserReservations($request->user());
        return response()->json([
            'active'  => $groups['active']->map(fn($r) => $this->transformReservation($r)),
            'history' => $groups['history']->map(fn($r) => $this->transformReservation($r)),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $reservation = Reservation::with('book')->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:cancelled,collected'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $reservation = $this->reservations->updateReservationStatus($request->user(), $reservation, $request->input('status'));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Cannot modify this reservation') {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
            }
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'reservation' => [
                'id'     => $reservation->id,
                'status' => $reservation->status,
            ],
            'message' => 'Reservation updated',
        ]);
    }

    private function transformReservation(Reservation $r): array
    {
        return [
            'id'         => $r->id,
            'book'       => [
                'id'     => $r->book->id,
                'title'  => $r->book->title,
                'author' => $r->book->author,
                'genre'  => $r->book->genre,
            ],
            'status'      => $r->status,
            'reserved_at' => $r->reserved_at->toIso8601String(),
            'expires_at'  => $r->expires_at->toIso8601String(),
            'collected_at' => $r->collected_at?->toIso8601String(),
            'days_remaining' => $r->isActive() ? now()->diffInDays($r->expires_at, false) : null,
        ];
    }
}
