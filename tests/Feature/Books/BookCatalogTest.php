<?php

use App\Models\Book;
use Illuminate\Testing\Fluent\AssertableJson;

it('lists books with pagination and filters schema', function () {
    Book::factory()->count(3)->create();

    $response = $this->get('/books');

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('data')
                ->has(
                    'meta',
                    fn($meta) => $meta
                        ->hasAll(['current_page', 'from', 'last_page', 'per_page', 'to', 'total'])
                )
                ->has(
                    'filters',
                    fn($filters) => $filters
                        ->has('genres')
                        ->has('years')
                )
        );
});

it('searches books by title', function () {
    Book::factory()->create(['title' => 'Unique Searchable Title']);
    $response = $this->get('/books?search=Unique+Searchable');
    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('data', fn($data) => $data->etc())
                ->has('meta')
                ->has('filters')
        );
});
