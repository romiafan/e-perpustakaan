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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'collected', 'expired', 'cancelled'])->default('active');
            $table->timestamp('reserved_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->timestamp('collected_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('expires_at');

            // NOTE: We want only one active reservation per user (not per book). Partial unique index
            // cannot be expressed directly in schema builder for ENUM condition; we enforce at app level.
            // Keep broader unique to prevent duplicate active reservation on same book; adjust later if needed.
            $table->unique(['user_id', 'book_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
