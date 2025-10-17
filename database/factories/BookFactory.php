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
        $words = ['The', 'Guide', 'Introduction', 'Advanced', 'Complete', 'Modern', 'Essential', 'Practical'];
        $authors = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Williams', 'Charlie Brown'];
        $title = $words[array_rand($words)] . ' ' . $words[array_rand($words)] . ' Book';
        $stock = rand(1, 10);

        // Generate placeholder book cover using picsum.photos
        $imageId = rand(1, 1000);
        $coverImage = "https://picsum.photos/seed/{$imageId}/400/600";

        return [
            'title'              => $title . ' ' . rand(1, 100),
            'author'             => $authors[array_rand($authors)],
            'isbn'               => '978' . rand(1000000000, 9999999999),
            'genre'              => ['Technology', 'Programming', 'Science', 'Fiction', 'History'][array_rand(['Technology', 'Programming', 'Science', 'Fiction', 'History'])],
            'publication_year'   => rand(2000, 2024),
            'synopsis'           => 'This is a sample book synopsis. It contains information about the book content.',
            'cover_image'        => $coverImage,
            'stock_quantity'     => $stock,
            'available_quantity' => $stock,
        ];
    }
}
