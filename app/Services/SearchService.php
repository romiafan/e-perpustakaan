<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    /** Simple search wrapper returning a collection (non-paginated) limited for quick suggestions. */
    public function searchBooks(string $query, int $limit = 10): Collection
    {
        return Book::query()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();
    }
}
