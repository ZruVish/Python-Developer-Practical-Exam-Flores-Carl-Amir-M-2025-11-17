<?php

namespace Tests\Feature;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Car Management Feature Tests
 * 
 * Tests CRUD operations and efficient car reordering functionality.
 */
class CarManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new car.
     */
    public function test_can_create_car(): void
    {
        $response = $this->postJson('/api/cars', [
            'name' => 'Car A',
            'color' => 'blue',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Car A',
                    'color' => 'blue',
                ],
            ]);

        $this->assertDatabaseHas('cars', [
            'name' => 'Car A',
            'color' => 'blue',
        ]);
    }

    /**
     * Test creating car requires name and color.
     */
    public function test_create_car_requires_name_and_color(): void
    {
        $response = $this->postJson('/api/cars', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'color']);
    }

    /**
     * Test retrieving all cars in order.
     */
    public function test_can_get_all_cars_ordered(): void
    {
        $car1 = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);
        $car2 = Car::create(['name' => 'Car B', 'color' => 'red', 'position' => 2000]);
        $car3 = Car::create(['name' => 'Car C', 'color' => 'blue', 'position' => 3000]);

        $response = $this->getJson('/api/cars');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('Car A', $data[0]['name']);
        $this->assertEquals('Car B', $data[1]['name']);
        $this->assertEquals('Car C', $data[2]['name']);
    }

    /**
     * Test filtering cars by color.
     */
    public function test_can_filter_cars_by_color(): void
    {
        Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);
        Car::create(['name' => 'Car B', 'color' => 'red', 'position' => 2000]);
        Car::create(['name' => 'Car C', 'color' => 'blue', 'position' => 3000]);
        Car::create(['name' => 'Car D', 'color' => 'red', 'position' => 4000]);

        $response = $this->getJson('/api/cars?color=blue');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Car A', $data[0]['name']);
        $this->assertEquals('Car C', $data[1]['name']);
        $this->assertEquals('blue', $data[0]['color']);
        $this->assertEquals('blue', $data[1]['color']);
    }

    /**
     * Test retrieving a single car.
     */
    public function test_can_get_single_car(): void
    {
        $car = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);

        $response = $this->getJson("/api/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $car->id,
                    'name' => 'Car A',
                    'color' => 'blue',
                ],
            ]);
    }

    /**
     * Test updating a car.
     */
    public function test_can_update_car(): void
    {
        $car = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);

        $response = $this->putJson("/api/cars/{$car->id}", [
            'name' => 'Car B',
            'color' => 'red',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Car B',
                    'color' => 'red',
                ],
            ]);

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'name' => 'Car B',
            'color' => 'red',
        ]);
    }

    /**
     * Test deleting a car.
     */
    public function test_can_delete_car(): void
    {
        $car = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);

        $response = $this->deleteJson("/api/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('cars', [
            'id' => $car->id,
        ]);
    }

    /**
     * Test moving a car to a new position efficiently.
     */
    public function test_can_move_car_before_another(): void
    {
        $carA = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);
        $carB = Car::create(['name' => 'Car B', 'color' => 'red', 'position' => 2000]);
        $carC = Car::create(['name' => 'Car C', 'color' => 'blue', 'position' => 3000]);

        // Move Car C before Car B
        $response = $this->postJson("/api/cars/{$carC->id}/move", [
            'before_car_id' => $carB->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Reload cars and verify order
        $cars = Car::orderBy('position')->get();
        $this->assertEquals('Car A', $cars[0]->name);
        $this->assertEquals('Car C', $cars[1]->name);
        $this->assertEquals('Car B', $cars[2]->name);
    }

    /**
     * Test moving a car to the end.
     */
    public function test_can_move_car_to_end(): void
    {
        $carA = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);
        $carB = Car::create(['name' => 'Car B', 'color' => 'red', 'position' => 2000]);
        $carC = Car::create(['name' => 'Car C', 'color' => 'blue', 'position' => 3000]);

        // Move Car A to the end
        $response = $this->postJson("/api/cars/{$carA->id}/move", [
            'before_car_id' => null,
        ]);

        $response->assertStatus(200);

        // Reload cars and verify order
        $cars = Car::orderBy('position')->get();
        $this->assertEquals('Car B', $cars[0]->name);
        $this->assertEquals('Car C', $cars[1]->name);
        $this->assertEquals('Car A', $cars[2]->name);
    }

    /**
     * Test moving car maintains order when filtering by color.
     */
    public function test_moving_car_maintains_color_order(): void
    {
        $carD = Car::create(['name' => 'Car D', 'color' => 'blue', 'position' => 1000]);
        $carB = Car::create(['name' => 'Car B', 'color' => 'red', 'position' => 2000]);
        $carE = Car::create(['name' => 'Car E', 'color' => 'blue', 'position' => 3000]);
        $carC = Car::create(['name' => 'Car C', 'color' => 'red', 'position' => 4000]);
        $carA = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 5000]);

        // Get blue cars - should be D, E, A
        $response = $this->getJson('/api/cars?color=blue');
        $blueCars = $response->json('data');
        $this->assertEquals('Car D', $blueCars[0]['name']);
        $this->assertEquals('Car E', $blueCars[1]['name']);
        $this->assertEquals('Car A', $blueCars[2]['name']);

        // Move Car C before Car B
        $this->postJson("/api/cars/{$carC->id}/move", [
            'before_car_id' => $carB->id,
        ]);

        // Move Car D before Car A
        $this->postJson("/api/cars/{$carD->id}/move", [
            'before_car_id' => $carA->id,
        ]);

        // Get blue cars again - should now be E, D, A
        $response = $this->getJson('/api/cars?color=blue');
        $blueCars = $response->json('data');
        $this->assertEquals('Car E', $blueCars[0]['name']);
        $this->assertEquals('Car D', $blueCars[1]['name']);
        $this->assertEquals('Car A', $blueCars[2]['name']);
    }

    /**
     * Test that moving a car only updates minimal records.
     */
    public function test_moving_car_is_efficient(): void
    {
        // Create many cars
        $cars = [];
        for ($i = 1; $i <= 10; $i++) {
            $cars[] = Car::create([
                'name' => "Car $i",
                'color' => $i % 2 === 0 ? 'red' : 'blue',
                'position' => $i * 1000,
            ]);
        }

        $initialPositions = Car::pluck('position', 'id')->toArray();

        // Move Car 5 before Car 2
        $this->postJson("/api/cars/{$cars[4]->id}/move", [
            'before_car_id' => $cars[1]->id,
        ]);

        // Verify only Car 5's position changed (in most cases)
        $finalPositions = Car::pluck('position', 'id')->toArray();
        
        $changedCount = 0;
        foreach ($initialPositions as $id => $position) {
            if ($finalPositions[$id] != $position) {
                $changedCount++;
            }
        }

        // In the efficient case, only 1 car should have changed position
        // (though rebalancing might update a few more)
        $this->assertLessThanOrEqual(5, $changedCount, 'Moving should update minimal records');
    }

    /**
     * Test cannot move car before itself.
     */
    public function test_cannot_move_car_before_itself(): void
    {
        $car = Car::create(['name' => 'Car A', 'color' => 'blue', 'position' => 1000]);

        $response = $this->postJson("/api/cars/{$car->id}/move", [
            'before_car_id' => $car->id,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot move car before itself',
            ]);
    }

    /**
     * Test car gets position automatically on creation.
     */
    public function test_car_gets_position_on_creation(): void
    {
        $car = Car::create(['name' => 'Car A', 'color' => 'blue']);

        $this->assertNotNull($car->position);
        $this->assertGreaterThan(0, $car->position);
    }

    /**
     * Test multiple cars get sequential positions.
     */
    public function test_multiple_cars_get_sequential_positions(): void
    {
        $car1 = Car::create(['name' => 'Car A', 'color' => 'blue']);
        $car2 = Car::create(['name' => 'Car B', 'color' => 'red']);
        $car3 = Car::create(['name' => 'Car C', 'color' => 'blue']);

        $this->assertLessThan($car2->position, $car1->position);
        $this->assertLessThan($car3->position, $car2->position);
    }
}
