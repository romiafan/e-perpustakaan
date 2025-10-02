<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);
        $stock = $this->faker->numberBetween(1, 10);
        return [
            'title'             => $title,
            'author'            => $this->faker->name(),
            'isbn'              => $this->faker->unique()->isbn13(),
            'genre'             => $this->faker->randomElement(['Technology', 'Programming', 'Science', 'Fiction', 'History']),
            'publication_year'  => $this->faker->year(),
            'synopsis'          => $this->faker->paragraph(),
            'stock_quantity'    => $stock,
            'available_quantity' => $stock,
        ];
    }
}
