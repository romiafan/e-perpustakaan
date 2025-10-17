<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Inertia pages for frontend
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/catalog', [BookController::class, 'catalog'])->name('books.catalog');

    Route::get('/books/{id}/details', function ($id) {
        $book = Book::with('reservations')->find($id);

        if (! $book) {
            // Provide a default book for display when book not found
            $bookData = [
                'id'                        => $id,
                'title'                     => 'Sample Book Title',
                'author'                    => 'Unknown Author',
                'genre'                     => 'General',
                'publication_year'          => date('Y'),
                'isbn'                      => '978-0-000-00000-0',
                'synopsis'                  => 'This is a sample book. The actual book data could not be found in the database. Please check with the library staff or browse other available books.',
                'stock_quantity'            => 0,
                'available_quantity'        => 0,
                'is_available'              => false,
                'can_reserve'               => false,
                'active_reservations_count' => 0,
            ];
        } else {
            // Calculate availability
            $activeReservations = $book->reservations()->where('status', 'active')->count();

            // Check if user can reserve (only one active reservation allowed)
            $userHasActiveReservation = Auth::check() &&
                Reservation::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->exists();

            $bookData = [
                'id'                        => $book->id,
                'title'                     => $book->title,
                'author'                    => $book->author,
                'genre'                     => $book->genre,
                'publication_year'          => $book->publication_year,
                'isbn'                      => $book->isbn,
                'synopsis'                  => $book->synopsis,
                'stock_quantity'            => $book->stock_quantity,
                'available_quantity'        => $book->available_quantity,
                'is_available'              => $book->available_quantity > 0,
                'active_reservations_count' => $activeReservations,
                'can_reserve'               => ! $userHasActiveReservation && $book->available_quantity > 0,
            ];
        }

        return Inertia::render('Books/Detail', ['book' => $bookData]);
    })->name('books.detail');

    Route::get('/my-reservations', function () {
        $user = Auth::user();

        // Get user's reservations
        $reservations = $user->reservations()->with('book')->get();

        // Format reservations for the frontend
        $active = $reservations->where('status', 'active')->map(function ($reservation) {
            return [
                'id'   => $reservation->id,
                'book' => [
                    'id'     => $reservation->book->id,
                    'title'  => $reservation->book->title,
                    'author' => $reservation->book->author,
                    'genre'  => $reservation->book->genre,
                ],
                'status'         => $reservation->status,
                'reserved_at'    => $reservation->reserved_at->toIso8601String(),
                'expires_at'     => $reservation->expires_at->toIso8601String(),
                'collected_at'   => $reservation->collected_at?->toIso8601String(),
                'days_remaining' => now()->diffInDays($reservation->expires_at, false),
            ];
        })->values();

        $history = $reservations->whereIn('status', ['collected', 'expired', 'cancelled'])->map(function ($reservation) {
            return [
                'id'   => $reservation->id,
                'book' => [
                    'id'     => $reservation->book->id,
                    'title'  => $reservation->book->title,
                    'author' => $reservation->book->author,
                    'genre'  => $reservation->book->genre,
                ],
                'status'         => $reservation->status,
                'reserved_at'    => $reservation->reserved_at->toIso8601String(),
                'expires_at'     => $reservation->expires_at->toIso8601String(),
                'collected_at'   => $reservation->collected_at?->toIso8601String(),
                'days_remaining' => null,
            ];
        })->values();

        return Inertia::render('Reservations/Index', [
            'reservations' => [
                'active'  => $active,
                'history' => $history,
            ],
        ]);
    })->name('reservations.index');

    Route::get('/my-profile', function () {
        $user = Auth::user();

        // Get user's reservation statistics
        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        $totalBorrowed = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['active', 'returned'])
            ->count();

        // Get recent activity
        $recentActivity = Reservation::where('user_id', $user->id)
            ->with('book')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($reservation) {
                return [
                    'type'        => 'reservation',
                    'book_title'  => $reservation->book->title ?? 'Unknown Book',
                    'description' => 'Reservation ' . ucfirst($reservation->status),
                    'date'        => $reservation->updated_at->toISOString(),
                ];
            });

        $profile = [
            'user' => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'role'              => $user->role ?? 'member',
                'email_verified_at' => $user->email_verified_at?->toISOString(),
                'created_at'        => $user->created_at->toISOString(),
            ],
            'stats' => [
                'active_reservations' => $activeReservations,
                'total_borrowed'      => $totalBorrowed,
                'account_status'      => 'active',
            ],
            'recent_activity' => $recentActivity,
        ];

        return Inertia::render('Profile/Index', [
            'profile' => $profile,
            'errors'  => [],
        ]);
    })->name('profile.index');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

// Contract JSON endpoints (could be moved to dedicated routes file if desired)
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

// Profile endpoints - handle auth manually to return 401 instead of redirect
Route::get('/profile', [ProfileController::class, 'show']);
Route::patch('/profile', [ProfileController::class, 'update']);

Route::middleware('auth')->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::patch('/reservations/{id}', [ReservationController::class, 'update']);
});
