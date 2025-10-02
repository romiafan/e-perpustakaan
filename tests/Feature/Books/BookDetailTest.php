<?php

use App\Models\Book;
use Illuminate\Testing\Fluent\AssertableJson;

it('shows a single book detail with expected structure', function () {
    $book = Book::factory()->create();

    $response = $this->get("/books/{$book->id}");

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'book',
                    fn($b) => $b
                        ->where('id', $book->id)
                        ->hasAll([
                            'title',
                            'author',
                            'isbn',
                            'genre',
                            'publication_year',
                            'synopsis',
                            'stock_quantity',
                            'available_quantity',
                            'is_available'
                        ])
                        ->has('active_reservations_count')
                        ->has('can_reserve')
                )
        );
});

it('returns 404 for missing book', function () {
    $response = $this->get('/books/999999');
    $response->assertStatus(404)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('message')
        );
});
