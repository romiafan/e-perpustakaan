<?php

use App\Models\{User, Book};
use Illuminate\Testing\Fluent\AssertableJson;

it('executes book search and reservation flow', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['title' => 'Search Flow Title', 'available_quantity' => 2]);

    $this->actingAs($user);

    $this->get('/books?search=Search+Flow')->assertStatus(200);

    $create = $this->post('/reservations', ['book_id' => $book->id]);
    $create->assertStatus(201)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('reservation')
        );

    $this->get('/reservations')->assertStatus(200);
});
