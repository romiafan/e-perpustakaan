<?php

use App\Models\Book;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new BookService();
});

describe('BookService', function () {
    describe('getBook', function () {
        test('it retrieves a book by id', function () {
            $book = Book::factory()->create(['title' => 'Test Book']);

            $found = $this->service->getBook($book->id);

            expect($found)->toBeInstanceOf(Book::class)
                ->and($found->id)->toBe($book->id)
                ->and($found->title)->toBe('Test Book');
        });

        test('it returns null when book not found', function () {
            $found = $this->service->getBook(99999);

            expect($found)->toBeNull();
        });
    });

    describe('listBooks', function () {
        test('it returns paginated books', function () {
            Book::factory()->count(25)->create();

            $result = $this->service->listBooks();

            expect($result->total())->toBe(25)
                ->and($result->perPage())->toBe(15)
                ->and($result->items())->toHaveCount(15);
        });

        test('it limits results to 50 per page', function () {
            Book::factory()->count(100)->create();

            $result = $this->service->listBooks(['per_page' => 100]);

            expect($result->perPage())->toBe(50)
                ->and($result->items())->toHaveCount(50);
        });

        test('it searches books by title', function () {
            Book::factory()->create(['title' => 'PHP Programming']);
            Book::factory()->create(['title' => 'JavaScript Basics']);
            Book::factory()->create(['title' => 'Advanced PHP']);

            $result = $this->service->listBooks(['search' => 'PHP']);

            expect($result->total())->toBe(2);
            $titles = collect($result->items())->pluck('title')->toArray();
            expect($titles)->toContain('PHP Programming')
                ->and($titles)->toContain('Advanced PHP');
        });

        test('it searches books by author', function () {
            Book::factory()->create(['author' => 'John Doe', 'title' => 'Book One']);
            Book::factory()->create(['author' => 'Jane Smith', 'title' => 'Book Two']);
            Book::factory()->create(['author' => 'John Smith', 'title' => 'Book Three']);

            $result = $this->service->listBooks(['search' => 'John']);

            expect($result->total())->toBe(2);
        });

        test('it filters books by genre', function () {
            Book::factory()->create(['genre' => 'Fiction', 'title' => 'Book One']);
            Book::factory()->create(['genre' => 'Science', 'title' => 'Book Two']);
            Book::factory()->create(['genre' => 'Fiction', 'title' => 'Book Three']);

            $result = $this->service->listBooks(['genre' => 'Fiction']);

            expect($result->total())->toBe(2);
            $genres = collect($result->items())->pluck('genre')->unique()->toArray();
            expect($genres)->toBe(['Fiction']);
        });

        test('it filters books by year', function () {
            Book::factory()->create(['publication_year' => 2020]);
            Book::factory()->create(['publication_year' => 2021]);
            Book::factory()->create(['publication_year' => 2020]);

            $result = $this->service->listBooks(['year' => 2020]);

            expect($result->total())->toBe(2);
        });

        test('it sorts books by title ascending', function () {
            Book::factory()->create(['title' => 'Zebra Book']);
            Book::factory()->create(['title' => 'Apple Book']);
            Book::factory()->create(['title' => 'Mango Book']);

            $result = $this->service->listBooks(['sort' => 'title', 'direction' => 'asc']);

            $titles = collect($result->items())->pluck('title')->toArray();
            expect($titles[0])->toBe('Apple Book')
                ->and($titles[2])->toBe('Zebra Book');
        });

        test('it sorts books by title descending', function () {
            Book::factory()->create(['title' => 'Zebra Book']);
            Book::factory()->create(['title' => 'Apple Book']);
            Book::factory()->create(['title' => 'Mango Book']);

            $result = $this->service->listBooks(['sort' => 'title', 'direction' => 'desc']);

            $titles = collect($result->items())->pluck('title')->toArray();
            expect($titles[0])->toBe('Zebra Book')
                ->and($titles[2])->toBe('Apple Book');
        });

        test('it sorts books by author', function () {
            Book::factory()->create(['author' => 'Zoe Adams']);
            Book::factory()->create(['author' => 'Alice Brown']);

            $result = $this->service->listBooks(['sort' => 'author', 'direction' => 'asc']);

            $authors = collect($result->items())->pluck('author')->toArray();
            expect($authors[0])->toBe('Alice Brown');
        });

        test('it sorts books by publication year', function () {
            Book::factory()->create(['publication_year' => 2022]);
            Book::factory()->create(['publication_year' => 2020]);
            Book::factory()->create(['publication_year' => 2021]);

            $result = $this->service->listBooks(['sort' => 'publication_year', 'direction' => 'asc']);

            $years = collect($result->items())->pluck('publication_year')->toArray();
            expect($years[0])->toBe(2020)
                ->and($years[2])->toBe(2022);
        });

        test('it combines multiple filters', function () {
            Book::factory()->create([
                'title' => 'PHP Guide',
                'genre' => 'Programming',
                'publication_year' => 2020,
            ]);
            Book::factory()->create([
                'title' => 'PHP Advanced',
                'genre' => 'Programming',
                'publication_year' => 2021,
            ]);
            Book::factory()->create([
                'title' => 'Python Basics',
                'genre' => 'Programming',
                'publication_year' => 2020,
            ]);

            $result = $this->service->listBooks([
                'search' => 'PHP',
                'genre' => 'Programming',
                'year' => 2020,
            ]);

            expect($result->total())->toBe(1);
            $book = collect($result->items())->first();
            expect($book->title)->toBe('PHP Guide');
        });

        test('it handles empty filters', function () {
            Book::factory()->count(5)->create();

            $result = $this->service->listBooks([]);

            expect($result->total())->toBe(5);
        });
    });

    describe('listGenres', function () {
        test('it returns unique genres', function () {
            Book::factory()->create(['genre' => 'Fiction']);
            Book::factory()->create(['genre' => 'Science']);
            Book::factory()->create(['genre' => 'Fiction']);
            Book::factory()->create(['genre' => 'History']);

            $genres = $this->service->listGenres();

            expect($genres)->toHaveCount(3);
            $genreList = $genres->pluck('genre')->toArray();
            expect($genreList)->toContain('Fiction')
                ->and($genreList)->toContain('Science')
                ->and($genreList)->toContain('History');
        });

        test('it returns empty collection when no books', function () {
            $genres = $this->service->listGenres();

            expect($genres)->toBeInstanceOf(\Illuminate\Support\Collection::class)
                ->and($genres)->toBeEmpty();
        });

        test('it orders genres alphabetically', function () {
            Book::factory()->create(['genre' => 'Zebra Genre']);
            Book::factory()->create(['genre' => 'Apple Genre']);
            Book::factory()->create(['genre' => 'Mango Genre']);

            $genres = $this->service->listGenres();

            expect($genres->pluck('genre')->toArray()[0])->toBe('Apple Genre')
                ->and($genres->pluck('genre')->toArray()[1])->toBe('Mango Genre')
                ->and($genres->pluck('genre')->toArray()[2])->toBe('Zebra Genre');
        });
    });

    describe('listYears', function () {
        test('it returns unique published years', function () {
            Book::factory()->create(['publication_year' => 2020]);
            Book::factory()->create(['publication_year' => 2021]);
            Book::factory()->create(['publication_year' => 2020]);
            Book::factory()->create(['publication_year' => 2022]);

            $years = $this->service->listYears();

            expect($years)->toHaveCount(3);
            $yearList = $years->pluck('publication_year')->toArray();
            expect($yearList)->toContain(2020)
                ->and($yearList)->toContain(2021)
                ->and($yearList)->toContain(2022);
        });

        test('it returns empty collection when no books', function () {
            $years = $this->service->listYears();

            expect($years)->toBeInstanceOf(\Illuminate\Support\Collection::class)
                ->and($years)->toBeEmpty();
        });

        test('it orders years in descending order', function () {
            Book::factory()->create(['publication_year' => 2019]);
            Book::factory()->create(['publication_year' => 2022]);
            Book::factory()->create(['publication_year' => 2020]);

            $years = $this->service->listYears();

            $yearValues = $years->pluck('publication_year')->toArray();
            expect($yearValues[0])->toBe(2022)
                ->and($yearValues[1])->toBe(2020)
                ->and($yearValues[2])->toBe(2019);
        });
    });
});
