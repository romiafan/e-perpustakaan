<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genre',
        'publication_year',
        'synopsis',
        'stock_quantity',
        'available_quantity',
    ];

    protected $appends = [
        'is_available',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)->where('status', 'active');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_quantity > 0;
    }
}
