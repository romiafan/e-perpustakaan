<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->string('author', 255);
            // ISBN 10 or 13 digits; store as string length 13 and allow shorter 10-digit values
            $table->string('isbn', 13)->unique();
            $table->string('genre', 100);
            $table->year('publication_year');
            $table->text('synopsis')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('available_quantity')->default(0);
            $table->timestamps();

            // Indexes for performance
            // Composite index for search; FULLTEXT recommended for MySQL/InnoDB
            // We'll add a raw statement in a separate migration if needed for FULLTEXT
            $table->index(['title', 'author']);
            $table->index('genre');
            $table->index('publication_year');
            $table->index('isbn');
            $table->index('available_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
