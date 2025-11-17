<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Car Model
 * 
 * Manages an ordered list of cars with efficient reordering capabilities.
 * Uses decimal position values with gaps to minimize database updates when moving cars.
 */
class Car extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'decimal:10',
    ];

    /**
     * Boot the model.
     * Automatically assign position when creating a new car.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($car) {
            if (is_null($car->position)) {
                $car->position = static::getNextPosition();
            }
        });
    }

    /**
     * Get the next position value (after the last car).
     *
     * @return float
     */
    public static function getNextPosition(): float
    {
        $lastCar = static::orderBy('position', 'desc')->first();
        return $lastCar ? $lastCar->position + 1000 : 1000;
    }

    /**
     * Get all cars ordered by position.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOrdered(): Collection
    {
        return static::orderBy('position')->get();
    }

    /**
     * Get cars by color, ordered by position.
     *
     * @param string $color
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByColor(string $color): Collection
    {
        return static::where('color', $color)
            ->orderBy('position')
            ->get();
    }

    /**
     * Move this car to a new position efficiently.
     * 
     * @param int|null $beforeCarId The ID of the car to place this car before (null = move to end)
     * @return bool
     */
    public function moveTo(?int $beforeCarId = null): bool
    {
        if ($beforeCarId === null) {
            // Move to end
            $lastCar = static::orderBy('position', 'desc')->first();
            if ($lastCar && $lastCar->id === $this->id) {
                return true; // Already at the end
            }
            $newPosition = static::getNextPosition();
        } else {
            $beforeCar = static::find($beforeCarId);
            if (!$beforeCar) {
                return false;
            }

            // Find the car before the target position
            $previousCar = static::where('position', '<', $beforeCar->position)
                ->orderBy('position', 'desc')
                ->first();

            if ($previousCar) {
                // Calculate position between previous and target
                $newPosition = ($previousCar->position + $beforeCar->position) / 2;
            } else {
                // Moving to the beginning
                $newPosition = $beforeCar->position / 2;
            }

            // If positions are too close, rebalance
            if (abs($newPosition - $beforeCar->position) < 0.0001) {
                return $this->rebalanceAround($beforeCarId);
            }
        }

        $this->position = $newPosition;
        return $this->save();
    }

    /**
     * Rebalance positions around a target car when positions get too close.
     * This is a fallback when simple position calculation isn't sufficient.
     *
     * @param int $targetCarId
     * @return bool
     */
    protected function rebalanceAround(int $targetCarId): bool
    {
        $targetCar = static::find($targetCarId);
        if (!$targetCar) {
            return false;
        }

        // Get cars around the target position
        $cars = static::whereBetween('position', [
            $targetCar->position - 100,
            $targetCar->position + 100
        ])
        ->orderBy('position')
        ->get();

        if ($cars->count() < 2) {
            // Simple case: just place before target
            $this->position = $targetCar->position / 2;
            return $this->save();
        }

        // Find where to insert
        $insertIndex = $cars->search(function ($car) use ($targetCar) {
            return $car->id === $targetCar->id;
        });

        if ($insertIndex === false) {
            return false;
        }

        // Rebalance positions with gaps
        $basePosition = $targetCar->position - 50;
        $gap = 10.0;

        foreach ($cars as $index => $car) {
            if ($car->id === $this->id) {
                continue; // Skip self
            }
            if ($index < $insertIndex) {
                $car->position = $basePosition + ($index * $gap);
            } else {
                $car->position = $basePosition + (($index + 1) * $gap);
            }
            $car->save();
        }

        // Set position for the moved car
        $this->position = $basePosition + ($insertIndex * $gap);
        return $this->save();
    }
}
