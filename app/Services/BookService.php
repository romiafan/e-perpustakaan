<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    /**
     * List books with optional filters and pagination.
     * @param array{search?:string,genre?:string,year?:int,isbn?:string,sort?:string,direction?:string,per_page?:int} $filters
     */
    public function listBooks(array $filters = []): LengthAwarePaginator
    {
        $query = Book::query();

        if (! empty($filters['search'])) {
            $q = $filters['search'];
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%");
            });
        }

        if (! empty($filters['genre'])) {
            $query->where('genre', $filters['genre']);
        }

        if (! empty($filters['year'])) {
            $query->where('publication_year', (int) $filters['year']);
        }

        if (! empty($filters['isbn'])) {
            $query->where('isbn', $filters['isbn']);
        }

        $sort = $filters['sort'] ?? 'title';
        $direction = $filters['direction'] ?? 'asc';
        $query->orderBy(in_array($sort, ['title', 'author', 'publication_year']) ? $sort : 'title', $direction === 'desc' ? 'desc' : 'asc');

        $perPage = isset($filters['per_page']) ? min((int) $filters['per_page'], 50) : 15;
        return $query->paginate($perPage);
    }

    /** Retrieve a single book or null. */
    public function getBook(int $id): ?Book
    {
        return Book::find($id);
    }

    /** Distinct genres for filters. */
    public function listGenres(): Collection
    {
        return Book::select('genre')->distinct()->orderBy('genre')->get();
    }

    /** Distinct publication years. */
    public function listYears(): Collection
    {
        return Book::select('publication_year')->distinct()->orderBy('publication_year', 'desc')->get();
    }
}
