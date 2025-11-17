<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * CarController
 * 
 * Handles CRUD operations and car reordering for the car management system.
 */
class CarController extends Controller
{
    /**
     * Display a listing of all cars, ordered by position.
     * Optionally filter by color.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $color = $request->query('color');

        if ($color) {
            $cars = Car::getByColor($color);
        } else {
            $cars = Car::getOrdered();
        }

        return response()->json([
            'success' => true,
            'data' => $cars,
        ]);
    }

    /**
     * Store a newly created car.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
        ]);

        $car = Car::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Car created successfully',
            'data' => $car,
        ], 201);
    }

    /**
     * Display the specified car.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $car,
        ]);
    }

    /**
     * Update the specified car.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:255',
        ]);

        $car->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully',
            'data' => $car,
        ]);
    }

    /**
     * Remove the specified car.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found',
            ], 404);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully',
        ]);
    }

    /**
     * Move a car to a new position.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function move(Request $request, string $id): JsonResponse
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found',
            ], 404);
        }

        $validated = $request->validate([
            'before_car_id' => 'nullable|integer|exists:cars,id',
        ]);

        $beforeCarId = $validated['before_car_id'] ?? null;

        if ($beforeCarId && (int)$beforeCarId === (int)$id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot move car before itself',
            ], 400);
        }

        $success = $car->moveTo($beforeCarId);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move car',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Car moved successfully',
            'data' => $car->fresh(),
        ]);
    }
}
