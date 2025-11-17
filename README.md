# RideGuide: Car Management System

A cyberpunk-themed web application for storing and managing an ordered list of up to 100,000 cars in a relational database. This system provides efficient CRUD operations with optimized reordering capabilities that minimize database updates, featuring a modern card-based interface with neon aesthetics.

## Problem Statement

Design and develop a web page for storing and managing (CRUD) an ordered list of 100,000 cars in a relational database with the following requirements:

1. **Ordered List**: Cars must maintain a specific order
2. **Color Filtering**: Query cars by color while preserving order (e.g., "show blue cars" returns Cars D, E, A in that specific order)
3. **Efficient Reordering**: Cars need to be moved often with minimal database updates (e.g., Car C can be moved in front of Car B, Car D can be moved in front of Car A)

## Features

- ✅ **Full CRUD Operations**: Create, Read, Update, Delete cars
- ✅ **Efficient Ordering**: Decimal position system with gaps for minimal updates
- ✅ **Color Filtering**: Query cars by color while maintaining order
- ✅ **Search Functionality**: Real-time search by car name
- ✅ **Card-Based Grid Layout**: 5x5 responsive grid with cyberpunk-styled cards
- ✅ **SVG Car Icons**: Custom cyberpunk-styled car icons with neon glows
- ✅ **Drag & Drop Reordering**: Intuitive UI for moving cars between positions
- ✅ **Click-to-Edit/Delete**: Modal popup for car actions
- ✅ **Cyberpunk Theme**: Neon cyan aesthetics with Orbitron font
- ✅ **Responsive Design**: Works seamlessly on desktop and mobile devices
- ✅ **Scalable**: Optimized for handling 100,000+ cars efficiently
- ✅ **Test-Driven Development**: Comprehensive test suite with 15+ feature tests

## Technologies Used

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: HTML5, jQuery, Tailwind CSS (with CDN fallback)
- **Typography**: Orbitron Google Font (cyberpunk aesthetic)
- **Database**: MySQL (also supports SQLite, PostgreSQL)
- **Architecture**: MVC (Model-View-Controller)
- **Testing**: PHPUnit with Feature Tests
- **UI Design**: Cyberpunk theme with neon cyan (#00ffff) accents
- **Icons**: Custom SVG car icons with dynamic color-based glows
- **Responsive Design**: Mobile-first approach (2-5 column grid)

## Database Schema

### Cars Table

```sql
CREATE TABLE cars (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    color VARCHAR(255) NOT NULL,
    position DECIMAL(20, 10) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_position (position),
    INDEX idx_color (color),
    INDEX idx_color_position (color, position)
);
```

### Key Design Decisions

1. **Decimal Position Field**: Uses `DECIMAL(20, 10)` to store position values with high precision
2. **Position Gaps**: Initial positions are spaced 1000 units apart, allowing efficient insertion between items
3. **Indexes**: 
   - Single index on `position` for ordered queries
   - Single index on `color` for filtering
   - Composite index on `(color, position)` for efficient color-filtered ordered queries

### Efficient Reordering Algorithm

The system uses a **gap-based position system**:

- When moving a car, calculate a new position between two existing positions
- Formula: `newPosition = (previousPosition + targetPosition) / 2`
- If positions get too close (< 0.0001), trigger a rebalancing operation
- This approach typically updates **only 1 record** per move operation

## Installation & Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ (or SQLite/PostgreSQL)
- Node.js and npm (for frontend assets)

### Step 1: Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 2: Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Update the database configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 3: Generate Application Key

```bash
php artisan key:generate
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Seed Sample Data (Optional)

```bash
php artisan db:seed
```

This will create 5 sample cars matching the problem example:
- Car D (blue) → Car B (red) → Car E (blue) → Car C (red) → Car A (blue)

### Step 6: Add Assets (Required)

Place the following files in the `public` directory:
- `Logo.png` - RideGuide logo (appears in header and browser tab)
- `bg.jpg` - Background image (blurred and used as backdrop)

### Step 7: Build Frontend Assets

```bash
npm run build
```

Or for development with hot reloading:

```bash
npm run dev
```

### Step 8: Start the Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## API Endpoints

All endpoints return JSON responses.

### Get All Cars

```http
GET /api/cars
GET /api/cars?color=blue
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Car D",
            "color": "blue",
            "position": "1000.0000000000",
            "created_at": "2025-11-17T05:00:00.000000Z",
            "updated_at": "2025-11-17T05:00:00.000000Z"
        }
    ]
}
```

### Get Single Car

```http
GET /api/cars/{id}
```

### Create Car

```http
POST /api/cars
Content-Type: application/json

{
    "name": "Car F",
    "color": "red"
}
```

### Update Car

```http
PUT /api/cars/{id}
Content-Type: application/json

{
    "name": "Car F Updated",
    "color": "blue"
}
```

### Delete Car

```http
DELETE /api/cars/{id}
```

### Move Car

```http
POST /api/cars/{id}/move
Content-Type: application/json

{
    "before_car_id": 2  // null to move to end
}
```

**Response:**
```json
{
    "success": true,
    "message": "Car moved successfully",
    "data": {
        "id": 1,
        "name": "Car D",
        "position": "1500.0000000000"
    }
}
```

## User Interface

### Cyberpunk Theme

The application features a dark cyberpunk aesthetic with:
- **Neon Cyan Accents**: All interactive elements use #00ffff with glowing effects
- **Orbitron Font**: Futuristic Google Font throughout the interface
- **Blurred Background**: Custom background image (bg.jpg) with low opacity and blur
- **Glassmorphism Cards**: Semi-transparent cards with backdrop blur effects
- **Neon Borders**: Glowing borders on all interactive elements
- **SVG Car Icons**: Custom cyberpunk-styled car icons with color-matched glows

### Card Grid Layout

- **5-Column Grid** on large screens (responsive: 2-5 columns)
- Each card displays:
  - Cyberpunk-styled SVG car icon (colored by car color)
  - Car name with neon cyan glow
  - Click to open action modal (Edit/Delete)
  - Drag to reorder

### Search & Filter

- **Search Bar**: Real-time search by car name (client-side filtering)
- **Color Filter**: Dropdown to filter by car color
- **Combined Filtering**: Search and color filter work together

## Usage Examples

### Example 1: Search for a Car

1. Type "Car A" in the search bar
2. The grid instantly filters to show only matching cars
3. Clear the search to see all cars again

### Example 2: Filter Blue Cars

1. Select "Blue Cars" from the color filter dropdown
2. The system returns all blue cars in their current order
3. Example result: Car D, Car E, Car A (in that order)

### Example 3: Move Car C Before Car B

1. Drag Car C card and drop it on Car B card
2. The system calculates a new position between Car A and Car B
3. Only Car C's position is updated in the database
4. The order is now: Car D, Car C, Car B, Car E, Car A

### Example 4: Edit or Delete a Car

1. Click on any car card
2. A modal popup appears with car details
3. Choose "Edit Car" to modify or "Delete Car" to remove
4. Or click "Cancel" to close the modal

## Testing

The project includes comprehensive feature tests following Test-Driven Development (TDD) principles.

### Run All Tests

```bash
php artisan test
```

### Run Specific Test

```bash
php artisan test --filter test_can_create_car
```

### Test Coverage

The test suite includes:

- ✅ Car creation with validation
- ✅ Retrieving all cars in order
- ✅ Filtering cars by color
- ✅ Updating cars
- ✅ Deleting cars
- ✅ Moving cars efficiently
- ✅ Order maintenance after moves
- ✅ Edge cases (moving car before itself, etc.)

## Architecture

### MVC Structure

```
app/
├── Http/
│   └── Controllers/
│       └── CarController.php    # Handles all CRUD and move operations
├── Models/
│   └── Car.php                  # Model with ordering logic
database/
├── migrations/
│   └── 2025_11_17_053648_create_cars_table.php
└── seeders/
    └── CarSeeder.php            # Sample data seeder
resources/
└── views/
    └── cars/
        └── index.blade.php      # Main UI with jQuery, cyberpunk theme, card grid
public/
├── Logo.png                     # RideGuide logo (header & favicon)
└── bg.jpg                       # Background image (blurred backdrop)
routes/
└── web.php                      # API routes
tests/
└── Feature/
    └── CarManagementTest.php   # Comprehensive test suite
```

### Key Classes

#### Car Model (`app/Models/Car.php`)

- `getOrdered()`: Get all cars ordered by position
- `getByColor($color)`: Get cars by color, ordered by position
- `moveTo($beforeCarId)`: Efficiently move car to new position
- `getNextPosition()`: Calculate next position for new cars

#### CarController (`app/Http/Controllers/CarController.php`)

- `index()`: List all cars (with optional color filter)
- `store()`: Create new car
- `show($id)`: Get single car
- `update($id)`: Update car
- `destroy($id)`: Delete car
- `move($id)`: Move car to new position

## UI/UX Features

### Visual Design
- **Cyberpunk Theme**: Dark background (#0a0a0f) with neon cyan (#00ffff) accents
- **Card-Based Interface**: 5x5 responsive grid layout
- **Custom SVG Icons**: Cyberpunk-styled car icons with dynamic glows
- **Glassmorphism**: Semi-transparent cards with backdrop blur
- **Neon Effects**: Glowing borders, text shadows, and hover effects
- **Responsive Grid**: 2 columns (mobile) → 3 (tablet) → 4 (desktop) → 5 (large)

### Interaction Features
- **Real-Time Search**: Instant filtering as you type
- **Color Filtering**: Dropdown to filter by car color
- **Click-to-Action**: Click cards to edit/delete
- **Drag & Drop**: Visual feedback when dragging cards
- **Modal Popups**: Centered modals for car actions and forms

## Performance Considerations

### For 100,000 Cars

1. **Indexes**: Composite index on `(color, position)` ensures fast color-filtered queries
2. **Position Gaps**: Initial 1000-unit gaps allow ~1000 moves before rebalancing
3. **Minimal Updates**: Most moves update only 1 record
4. **Rebalancing**: Automatic rebalancing when positions get too close
5. **Client-Side Search**: Search filtering happens in browser for instant results

### Optimization Strategies

- Uses database indexes for efficient queries
- Decimal precision allows millions of insertions before rebalancing
- Rebalancing only affects cars in a local range (100 units)
- Client-side search reduces server load
- Batch operations can be added for bulk moves

## Development

### Code Style

- Follows PSR-12 coding standards
- Clean OOP design with proper separation of concerns
- Comprehensive PHPDoc comments
- Sensible code-level documentation

### Adding Features

1. Write tests first (TDD approach)
2. Implement feature
3. Ensure all tests pass
4. Update documentation

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Design Notes

### Cyberpunk Theme Elements
- **Color Palette**: Dark backgrounds with neon cyan (#00ffff) accents
- **Typography**: Orbitron font for futuristic aesthetic
- **Effects**: Glowing borders, text shadows, backdrop blur
- **Icons**: Custom SVG car icons with color-matched neon glows
- **Layout**: Card-based grid with glassmorphism effects

### Assets Required
- `public/Logo.png` - Company logo (used in header and favicon)
- `public/bg.jpg` - Background image (blurred, low opacity)

## Author

Carl Amir Flores - Python Developer Practical Exam - 2025-11-17
