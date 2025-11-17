<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RideGuide: Car Management System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('Logo.png') }}">
    
    <!-- Google Fonts - Orbitron -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Tailwind CSS CDN fallback -->
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <style>
        * {
            font-family: 'Orbitron', sans-serif;
        }
        
        body {
            background: #0a0a0f;
            color: #00ffff;
            position: relative;
            min-height: 100vh;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset("bg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.15;
            filter: blur(8px);
            z-index: -1;
            pointer-events: none;
        }
        
        .cyberpunk-container {
            position: relative;
            z-index: 1;
        }
        
        .cyberpunk-card {
            background: rgba(10, 10, 15, 0.85);
            border: 2px solid #00ffff;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3), inset 0 0 20px rgba(0, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .cyberpunk-card:hover {
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.5), inset 0 0 30px rgba(0, 255, 255, 0.2);
            border-color: #00ffff;
        }
        
        .cyberpunk-button {
            background: linear-gradient(135deg, #00ffff 0%, #00ffff 100%);
            border: 2px solid #00ffff;
            color: #0a0a0f;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
            transition: all 0.3s;
        }
        
        .cyberpunk-button:hover {
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.7);
            transform: translateY(-2px);
        }
        
        .cyberpunk-input {
            background: rgba(10, 10, 15, 0.9);
            border: 2px solid #00ffff;
            color: #00ffff;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }
        
        .cyberpunk-input:focus {
            outline: none;
            border-color: #00ffff;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
        }
        
        .cyberpunk-title {
            text-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff, 0 0 30px #00ffff;
            color: #00ffff;
            font-weight: 900;
            letter-spacing: 2px;
        }
        
        .cyberpunk-subtitle {
            color: #00ffff;
            text-shadow: 0 0 10px #00ffff;
        }
        
        .car-item {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .car-item:hover {
            transform: translateY(-2px);
        }
        .car-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }
        .car-card {
            transition: all 0.2s;
            cursor: pointer;
            background: rgba(10, 10, 15, 0.85);
            border: 2px solid #00ffff;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        .car-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0 25px rgba(0, 255, 255, 0.6), 0 0 15px rgba(0, 255, 255, 0.4);
            border-color: #00ffff;
        }
        .car-card.drag-over {
            border: 3px solid #00ffff;
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.8);
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #00ffff;
            animation: spin 1s ease-in-out infinite;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .cyberpunk-modal {
            background: rgba(10, 10, 15, 0.95);
            border: 2px solid #00ffff;
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.6), inset 0 0 30px rgba(0, 255, 255, 0.1);
            backdrop-filter: blur(20px);
        }
        
        .cyberpunk-footer {
            background: rgba(10, 10, 15, 0.9);
            border-top: 2px solid #00ffff;
            box-shadow: 0 -5px 20px rgba(0, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .logo-container {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        
        .logo-img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 0 15px #00ffff);
            flex-shrink: 0;
        }
        
        .header-text-container {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="cyberpunk-container container mx-auto px-4 py-8 max-w-6xl min-h-screen pb-24">
        <!-- Header -->
        <header class="mb-8">
            <div class="logo-container">
                <img src="{{ asset('Logo.png') }}" alt="RideGuide Logo" class="logo-img" onerror="this.style.display='none'">
                <div class="header-text-container">
                    <h1 class="cyberpunk-title text-4xl font-bold mb-2">RideGuide: Car Management System</h1>
                    <p class="cyberpunk-subtitle text-lg">Manage and reorder your car collection efficiently</p>
                </div>
            </div>
        </header>

        <!-- Filters and Actions -->
        <div class="cyberpunk-card rounded-lg p-6 mb-6">
            <div class="flex flex-col gap-4">
                <!-- Search and Filter Row -->
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                    <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full md:w-auto">
                        <!-- Search Bar -->
                        <div class="flex-1 min-w-[200px]">
                            <label for="searchBar" class="block text-sm font-medium text-cyan-400 mb-2" style="text-shadow: 0 0 5px #00ffff;">Search Cars:</label>
                            <input type="text" id="searchBar" placeholder="Search by car name..." 
                                   class="cyberpunk-input w-full px-4 py-2 rounded-md text-cyan-400 font-semibold placeholder-cyan-500"
                                   style="background: rgba(10, 10, 15, 0.9);">
                        </div>
                        
                        <!-- Color Filter -->
                        <div class="flex items-center gap-3">
                            <label for="colorFilter" class="text-sm font-medium text-cyan-400 whitespace-nowrap" style="text-shadow: 0 0 5px #00ffff;">Filter by Color:</label>
                            <select id="colorFilter" class="cyberpunk-input px-4 py-2 rounded-md text-cyan-400 font-semibold">
                                <option value="" style="background: #0a0a0f; color: #00ffff;">All Cars</option>
                                <option value="blue" style="background: #0a0a0f; color: #00ffff;">Blue Cars</option>
                                <option value="red" style="background: #0a0a0f; color: #00ffff;">Red Cars</option>
                                <option value="green" style="background: #0a0a0f; color: #00ff00;">Green Cars</option>
                                <option value="yellow" style="background: #0a0a0f; color: #ffff00;">Yellow Cars</option>
                                <option value="black" style="background: #0a0a0f; color: #ffffff;">Black Cars</option>
                                <option value="white" style="background: #0a0a0f; color: #ffffff;">White Cars</option>
                            </select>
                        </div>
                    </div>
                    
                    <button id="addCarBtn" class="cyberpunk-button px-6 py-2 rounded-md transition whitespace-nowrap">
                        + Add New Car
                    </button>
                </div>
            </div>
        </div>

        <!-- Car Grid -->
        <div id="carsContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center py-8 text-cyan-400">
                <div class="loading mx-auto"></div>
                <p class="mt-4" style="text-shadow: 0 0 10px #00ffff;">Loading cars...</p>
            </div>
        </div>

        <!-- Add/Edit Car Modal -->
        <div id="carModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
            <div class="cyberpunk-modal rounded-lg p-6 w-full max-w-md mx-4">
                <h2 id="modalTitle" class="cyberpunk-title text-2xl font-bold mb-4 text-center">Add New Car</h2>
                <form id="carForm">
                    <input type="hidden" id="carId" name="id">
                    
                    <div class="mb-4">
                        <label for="carName" class="block text-sm font-medium text-cyan-400 mb-2" style="text-shadow: 0 0 5px #00ffff;">Car Name</label>
                        <input type="text" id="carName" name="name" required
                               class="cyberpunk-input w-full px-3 py-2 rounded-md">
                    </div>
                    
                    <div class="mb-4">
                        <label for="carColor" class="block text-sm font-medium text-cyan-400 mb-2" style="text-shadow: 0 0 5px #00ffff;">Color</label>
                        <select id="carColor" name="color" required
                                class="cyberpunk-input w-full px-3 py-2 rounded-md text-cyan-400 font-semibold">
                            <option value="" style="background: #0a0a0f; color: #00ffff;">Select a color</option>
                            <option value="blue" style="background: #0a0a0f; color: #00ffff;">Blue</option>
                            <option value="red" style="background: #0a0a0f; color: #00ffff;">Red</option>
                            <option value="green" style="background: #0a0a0f; color: #00ff00;">Green</option>
                            <option value="yellow" style="background: #0a0a0f; color: #ffff00;">Yellow</option>
                            <option value="black" style="background: #0a0a0f; color: #ffffff;">Black</option>
                            <option value="white" style="background: #0a0a0f; color: #ffffff;">White</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" class="cyberpunk-button flex-1 px-4 py-2 rounded-md transition">
                            Save
                        </button>
                        <button type="button" id="cancelBtn" class="flex-1 px-4 py-2 bg-gray-800 text-cyan-400 border-2 border-cyan-400 rounded-md hover:bg-gray-700 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Car Action Modal (Edit/Delete) -->
        <div id="carActionModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden" style="display: none;">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm px-4">
                <div class="cyberpunk-modal rounded-lg p-6">
                    <h2 id="actionModalTitle" class="cyberpunk-title text-2xl font-bold mb-4 text-center">Car Actions</h2>
                    <div id="actionCarInfo" class="mb-6 text-center">
                        <!-- Car info will be inserted here -->
                    </div>
                    <div class="flex flex-col gap-3">
                        <button id="editCarBtn" class="cyberpunk-button w-full px-4 py-2 rounded-md transition">
                            Edit Car
                        </button>
                        <button id="deleteCarBtn" class="w-full px-4 py-2 bg-red-900 text-red-400 border-2 border-red-500 rounded-md hover:bg-red-800 transition font-bold" style="box-shadow: 0 0 15px rgba(255, 0, 0, 0.5);">
                            Delete Car
                        </button>
                        <button id="closeActionModalBtn" class="w-full px-4 py-2 bg-gray-800 text-cyan-400 border-2 border-cyan-400 rounded-md hover:bg-gray-700 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="cyberpunk-footer fixed bottom-0 left-0 right-0 py-4 px-6 text-center">
        <p class="text-cyan-400 font-semibold" style="text-shadow: 0 0 10px #00ffff;">
            &copy; 2025 RideGuide. All rights reserved.
        </p>
    </footer>

    <script>
        $(document).ready(function() {
            let currentFilter = '';
            let searchQuery = '';
            let draggedCarId = null;
            let draggedOverCarId = null;

            // CSRF token setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load cars on page load
            loadCars();

            // Search bar
            $('#searchBar').on('input', function() {
                searchQuery = $(this).val().toLowerCase().trim();
                filterAndRenderCars();
            });

            // Filter dropdown
            $('#colorFilter').on('change', function() {
                currentFilter = $(this).val();
                filterAndRenderCars();
            });

            // Add car button
            $('#addCarBtn').on('click', function() {
                $('#carForm')[0].reset();
                $('#carId').val('');
                $('#modalTitle').text('Add New Car');
                $('#carModal').removeClass('hidden').addClass('flex');
            });

            // Cancel button
            $('#cancelBtn').on('click', function() {
                $('#carModal').addClass('hidden').removeClass('flex');
            });

            // Close modal on outside click
            $('#carModal').on('click', function(e) {
                if ($(e.target).is('#carModal')) {
                    $(this).addClass('hidden').removeClass('flex');
                }
            });

            // Submit form
            $('#carForm').on('submit', function(e) {
                e.preventDefault();
                const carId = $('#carId').val();
                const data = {
                    name: $('#carName').val(),
                    color: $('#carColor').val()
                };

                if (carId) {
                    updateCar(carId, data);
                } else {
                    createCar(data);
                }
            });

            // Store all cars for client-side filtering
            let allCars = [];

            // Load cars function
            function loadCars() {
                const url = currentFilter 
                    ? `/api/cars?color=${currentFilter}` 
                    : '/api/cars';
                
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            allCars = response.data;
                            filterAndRenderCars();
                        } else {
                            allCars = [];
                            $('#carsContainer').html('<div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center py-8 text-cyan-400" style="text-shadow: 0 0 10px #00ffff;">No cars found.</div>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading cars:', xhr);
                        let errorMsg = 'Error loading cars: ';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg += xhr.responseJSON.message;
                        } else if (xhr.status === 0) {
                            errorMsg += 'Network error. Please check your connection.';
                        } else {
                            errorMsg += 'HTTP ' + xhr.status;
                        }
                        $('#carsContainer').html('<div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center py-8 text-cyan-400" style="text-shadow: 0 0 10px #00ffff;">' + errorMsg + '</div>');
                    }
                });
            }

            // Filter and render cars based on search and color filter
            function filterAndRenderCars() {
                let filteredCars = allCars;

                // Apply search filter
                if (searchQuery) {
                    filteredCars = filteredCars.filter(function(car) {
                        return car.name.toLowerCase().includes(searchQuery);
                    });
                }

                // Render filtered cars
                if (filteredCars.length === 0) {
                    $('#carsContainer').html('<div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center py-8 text-cyan-400" style="text-shadow: 0 0 10px #00ffff;">No cars found matching your search.</div>');
                } else {
                    renderCars(filteredCars);
                }
            }

            // Render cars as cards in 5x5 grid
            function renderCars(cars) {
                const container = $('#carsContainer');
                container.empty();

                if (cars.length === 0) {
                    container.html('<div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center py-8 text-cyan-400" style="text-shadow: 0 0 10px #00ffff;">No cars found.</div>');
                    return;
                }

                cars.forEach(function(car, index) {
                    const carCard = $(`
                        <div class="car-card car-item rounded-lg p-4 flex flex-col items-center justify-center cursor-move relative" 
                             data-id="${car.id}" 
                             draggable="true"
                             style="min-height: 180px;">
                            <div class="mb-3">
                                ${getCarSVG(car.color)}
                            </div>
                            <h3 class="font-semibold text-lg text-cyan-400 text-center" style="text-shadow: 0 0 10px #00ffff;">${escapeHtml(car.name)}</h3>
                        </div>
                    `);

                    // Click handler to open action modal
                    carCard.on('click', function(e) {
                        // Don't open modal if dragging
                        if (!draggedCarId) {
                            e.stopPropagation();
                            openCarActionModal(car);
                        }
                    });

                    // Drag and drop handlers
                    carCard.on('dragstart', function(e) {
                        draggedCarId = $(this).data('id');
                        $(this).addClass('dragging');
                        e.originalEvent.dataTransfer.effectAllowed = 'move';
                        e.stopPropagation(); // Prevent click event
                    });

                    carCard.on('dragend', function() {
                        $(this).removeClass('dragging');
                        $('.car-card').removeClass('drag-over');
                        draggedCarId = null;
                        draggedOverCarId = null;
                    });

                    carCard.on('dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.originalEvent.dataTransfer.dropEffect = 'move';
                        if (draggedCarId && draggedCarId !== $(this).data('id')) {
                            draggedOverCarId = $(this).data('id');
                            $(this).addClass('drag-over');
                        }
                    });

                    carCard.on('dragleave', function() {
                        $(this).removeClass('drag-over');
                    });

                    carCard.on('drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).removeClass('drag-over');
                        
                        if (draggedCarId && draggedOverCarId && draggedCarId !== draggedOverCarId) {
                            moveCar(draggedCarId, draggedOverCarId);
                        }
                    });

                    container.append(carCard);
                });
            }

            // Open car action modal (Edit/Delete)
            function openCarActionModal(car) {
                const colorValue = getColorValue(car.color);
                $('#actionCarInfo').html(`
                    <div class="mb-4 flex justify-center">
                        ${getCarSVG(car.color, '80px')}
                    </div>
                    <h3 class="font-bold text-xl text-cyan-400 mb-2" style="text-shadow: 0 0 10px #00ffff;">${escapeHtml(car.name)}</h3>
                    <p class="text-sm text-cyan-300 capitalize" style="text-shadow: 0 0 5px #00ffff;">${escapeHtml(car.color)}</p>
                `);
                
                $('#editCarBtn').data('id', car.id);
                $('#deleteCarBtn').data('id', car.id);
                
                $('#carActionModal').css('display', 'block');
            }

            // Close action modal handlers
            $('#closeActionModalBtn').on('click', function() {
                $('#carActionModal').css('display', 'none');
            });

            $('#carActionModal').on('click', function(e) {
                // Close if clicking on the backdrop (the outer div)
                if ($(e.target).attr('id') === 'carActionModal') {
                    $(this).css('display', 'none');
                }
            });

            // Edit button in action modal
            $('#editCarBtn').on('click', function() {
                const carId = $(this).data('id');
                $('#carActionModal').css('display', 'none');
                editCar(carId);
            });

            // Delete button in action modal
            $('#deleteCarBtn').on('click', function() {
                const carId = $(this).data('id');
                if (confirm('Are you sure you want to delete this car?')) {
                    $('#carActionModal').css('display', 'none');
                    deleteCar(carId);
                }
            });

            // Create car
            function createCar(data) {
                $.ajax({
                    url: '/api/cars',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        $('#carModal').addClass('hidden').removeClass('flex');
                        $('#searchBar').val(''); // Clear search after creating
                        searchQuery = '';
                        loadCars();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMsg = 'Error creating car: ';
                        if (Object.keys(errors).length > 0) {
                            errorMsg += Object.values(errors).flat().join(', ');
                        } else {
                            errorMsg += xhr.responseJSON?.message || 'Unknown error';
                        }
                        alert(errorMsg);
                    }
                });
            }

            // Update car
            function updateCar(id, data) {
                $.ajax({
                    url: `/api/cars/${id}`,
                    method: 'PUT',
                    data: data,
                    success: function(response) {
                        $('#carModal').addClass('hidden').removeClass('flex');
                        $('#searchBar').val(''); // Clear search after updating
                        searchQuery = '';
                        loadCars();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        let errorMsg = 'Error updating car: ';
                        if (Object.keys(errors).length > 0) {
                            errorMsg += Object.values(errors).flat().join(', ');
                        } else {
                            errorMsg += xhr.responseJSON?.message || 'Unknown error';
                        }
                        alert(errorMsg);
                    }
                });
            }

            // Delete car
            function deleteCar(id) {
                $.ajax({
                    url: `/api/cars/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        $('#searchBar').val(''); // Clear search after deleting
                        searchQuery = '';
                        loadCars();
                    },
                    error: function(xhr) {
                        alert('Error deleting car: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            }

            // Edit car
            function editCar(id) {
                $.ajax({
                    url: `/api/cars/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const car = response.data;
                        $('#carId').val(car.id);
                        $('#carName').val(car.name);
                        $('#carColor').val(car.color);
                        $('#modalTitle').text('Edit Car');
                        $('#carModal').removeClass('hidden').addClass('flex');
                    },
                    error: function(xhr) {
                        alert('Error loading car: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            }

            // Move car
            function moveCar(carId, beforeCarId) {
                $.ajax({
                    url: `/api/cars/${carId}/move`,
                    method: 'POST',
                    data: { before_car_id: beforeCarId },
                    success: function(response) {
                        loadCars();
                        // Keep search query when moving
                    },
                    error: function(xhr) {
                        alert('Error moving car: ' + (xhr.responseJSON?.message || 'Unknown error'));
                        loadCars(); // Reload to reset UI
                    }
                });
            }

            // Helper functions
            function getColorValue(color) {
                const colors = {
                    'blue': '#3b82f6',
                    'red': '#ef4444',
                    'green': '#10b981',
                    'yellow': '#fbbf24',
                    'black': '#1f2937',
                    'white': '#f3f4f6'
                };
                return colors[color.toLowerCase()] || '#6b7280';
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            // Generate cyberpunk-styled SVG car icon based on color
            function getCarSVG(color, size = '60px') {
                const colorValue = getColorValue(color);
                // Determine glow color based on car color
                let glowColor = '#00ffff'; // Default cyan glow
                if (color.toLowerCase() === 'red') glowColor = '#00ffff';
                else if (color.toLowerCase() === 'green') glowColor = '#00ff00';
                else if (color.toLowerCase() === 'yellow') glowColor = '#ffff00';
                else if (color.toLowerCase() === 'blue') glowColor = '#00ffff';
                
                return `
                    <svg width="${size}" height="${size}" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <filter id="glow-${color}">
                                <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                                <feMerge>
                                    <feMergeNode in="coloredBlur"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                            <linearGradient id="carGradient-${color}" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:${colorValue};stop-opacity:1" />
                                <stop offset="100%" style="stop-color:${colorValue};stop-opacity:0.7" />
                            </linearGradient>
                        </defs>
                        <!-- Glow effect behind car -->
                        <rect x="12" y="37" width="76" height="41" rx="7" fill="${glowColor}" opacity="0.3" filter="url(#glow-${color})"/>
                        <!-- Car body with gradient -->
                        <rect x="15" y="40" width="70" height="35" rx="5" fill="url(#carGradient-${color})" stroke="${glowColor}" stroke-width="2" filter="url(#glow-${color})"/>
                        <!-- Car roof -->
                        <path d="M 25 40 L 35 20 L 65 20 L 75 40 Z" fill="url(#carGradient-${color})" stroke="${glowColor}" stroke-width="2" filter="url(#glow-${color})"/>
                        <!-- Futuristic windows with grid -->
                        <rect x="30" y="25" width="15" height="12" rx="2" fill="#0a0a0f" opacity="0.9" stroke="${glowColor}" stroke-width="1"/>
                        <line x1="37.5" y1="25" x2="37.5" y2="37" stroke="${glowColor}" stroke-width="0.5" opacity="0.6"/>
                        <rect x="55" y="25" width="15" height="12" rx="2" fill="#0a0a0f" opacity="0.9" stroke="${glowColor}" stroke-width="1"/>
                        <line x1="62.5" y1="25" x2="62.5" y2="37" stroke="${glowColor}" stroke-width="0.5" opacity="0.6"/>
                        <!-- Futuristic wheels with neon rim -->
                        <circle cx="30" cy="75" r="9" fill="#0a0a0f" stroke="${glowColor}" stroke-width="2" filter="url(#glow-${color})"/>
                        <circle cx="30" cy="75" r="6" fill="#1a1a2e" stroke="${glowColor}" stroke-width="1"/>
                        <circle cx="30" cy="75" r="3" fill="${glowColor}" opacity="0.5"/>
                        <circle cx="70" cy="75" r="9" fill="#0a0a0f" stroke="${glowColor}" stroke-width="2" filter="url(#glow-${color})"/>
                        <circle cx="70" cy="75" r="6" fill="#1a1a2e" stroke="${glowColor}" stroke-width="1"/>
                        <circle cx="70" cy="75" r="3" fill="${glowColor}" opacity="0.5"/>
                        <!-- Neon headlights -->
                        <circle cx="85" cy="50" r="5" fill="${glowColor}" opacity="0.8" filter="url(#glow-${color})"/>
                        <circle cx="85" cy="50" r="3" fill="#ffffff" opacity="0.9"/>
                        <!-- Cyberpunk door line with glow -->
                        <line x1="50" y1="40" x2="50" y2="75" stroke="${glowColor}" stroke-width="2" opacity="0.6" filter="url(#glow-${color})"/>
                        <!-- Futuristic side details -->
                        <rect x="20" y="45" width="3" height="8" rx="1" fill="${glowColor}" opacity="0.7"/>
                        <rect x="77" y="45" width="3" height="8" rx="1" fill="${glowColor}" opacity="0.7"/>
                    </svg>
                `;
            }
        });
    </script>
</body>
</html>

