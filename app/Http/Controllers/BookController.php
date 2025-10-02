<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function __construct(private readonly BookService $books) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'genre', 'year', 'isbn', 'sort', 'direction', 'per_page']);
        $paginator = $this->books->listBooks($filters);
        $genres = $this->books->listGenres()->pluck('genre');
        $years = $this->books->listYears()->pluck('publication_year');

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from'         => $paginator->firstItem(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'to'           => $paginator->lastItem(),
                'total'        => $paginator->total(),
            ],
            'filters' => [
                'genres' => $genres,
                'years'  => $years,
            ],
        ]);
    }

    public function show(int $id)
    {
        $book = $this->books->getBook($id);
        if (! $book) {
            return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }

        // Only return expected fields (exclude timestamps)
        $bookData = $book->only([
            'id',
            'title',
            'author',
            'isbn',
            'genre',
            'publication_year',
            'synopsis',
            'stock_quantity',
            'available_quantity',
            'is_available'
        ]);
        $bookData['active_reservations_count'] = $book->activeReservations()->count();
        $bookData['can_reserve'] = $book->available_quantity > 0; // User-specific logic placeholder

        return response()->json([
            'book' => $bookData,
        ]);
    }
}
