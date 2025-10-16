<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780061120084',
                'genre' => 'Fiction',
                'publication_year' => 1960,
                'synopsis' => 'A classic novel of a lawyer in the Depression-era South defending a black man charged with the rape of a white woman.',
                'stock_quantity' => 5,
                'available_quantity' => 5,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '9780451524935',
                'genre' => 'Science Fiction',
                'publication_year' => 1949,
                'synopsis' => 'A dystopian social science fiction novel and cautionary tale about the dangers of totalitarianism.',
                'stock_quantity' => 3,
                'available_quantity' => 3,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'isbn' => '9780141439518',
                'genre' => 'Romance',
                'publication_year' => 1813,
                'synopsis' => 'A romantic novel of manners that follows the character development of Elizabeth Bennet.',
                'stock_quantity' => 4,
                'available_quantity' => 4,
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'genre' => 'Fiction',
                'publication_year' => 1925,
                'synopsis' => 'A novel about the American Dream in the Roaring Twenties, told through the eyes of Nick Carraway.',
                'stock_quantity' => 6,
                'available_quantity' => 6,
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'isbn' => '9780747532699',
                'genre' => 'Fantasy',
                'publication_year' => 1997,
                'synopsis' => 'A young wizard discovers his magical heritage on his eleventh birthday.',
                'stock_quantity' => 8,
                'available_quantity' => 8,
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780547928227',
                'genre' => 'Fantasy',
                'publication_year' => 1937,
                'synopsis' => 'A fantasy novel about the quest of home-loving hobbit Bilbo Baggins.',
                'stock_quantity' => 4,
                'available_quantity' => 4,
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'isbn' => '9780316769174',
                'genre' => 'Fiction',
                'publication_year' => 1951,
                'synopsis' => 'The story of teenage rebellion and alienation told through the perspective of Holden Caulfield.',
                'stock_quantity' => 3,
                'available_quantity' => 3,
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780544003415',
                'genre' => 'Fantasy',
                'publication_year' => 1954,
                'synopsis' => 'An epic high-fantasy novel about the quest to destroy the One Ring.',
                'stock_quantity' => 5,
                'available_quantity' => 5,
            ],
            [
                'title' => 'Animal Farm',
                'author' => 'George Orwell',
                'isbn' => '9780451526342',
                'genre' => 'Political Fiction',
                'publication_year' => 1945,
                'synopsis' => 'A satirical allegorical novella about a group of farm animals who rebel against their human farmer.',
                'stock_quantity' => 4,
                'available_quantity' => 4,
            ],
            [
                'title' => 'The Chronicles of Narnia',
                'author' => 'C.S. Lewis',
                'isbn' => '9780066238500',
                'genre' => 'Fantasy',
                'publication_year' => 1950,
                'synopsis' => 'A series of seven fantasy novels featuring children who explore the magical land of Narnia.',
                'stock_quantity' => 6,
                'available_quantity' => 6,
            ],
            [
                'title' => 'Brave New World',
                'author' => 'Aldous Huxley',
                'isbn' => '9780060850524',
                'genre' => 'Science Fiction',
                'publication_year' => 1932,
                'synopsis' => 'A dystopian novel set in a futuristic World State of genetically modified citizens.',
                'stock_quantity' => 3,
                'available_quantity' => 3,
            ],
            [
                'title' => 'The Alchemist',
                'author' => 'Paulo Coelho',
                'isbn' => '9780062315007',
                'genre' => 'Fiction',
                'publication_year' => 1988,
                'synopsis' => 'A philosophical novel about a young Andalusian shepherd on his quest for treasure.',
                'stock_quantity' => 7,
                'available_quantity' => 7,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
