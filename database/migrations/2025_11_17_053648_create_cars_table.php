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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            // Using decimal for position to allow efficient reordering
            // Position values have gaps that can be filled when moving cars
            $table->decimal('position', 20, 10)->index();
            $table->timestamps();
            
            // Index on color for efficient filtering by color
            $table->index('color');
            // Composite index for efficient queries: color + position
            $table->index(['color', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
