#!/bin/bash

# Create Laravel project
composer create-project laravel/laravel .
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Set proper permissions
chmod -R 777 storage bootstrap/cache

# Create a simple API route
cat > routes/api.php << 'EOL'
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello', function () {
    return [
        'message' => 'Hello from Laravel API!',
        'timestamp' => now(),
        'database_connected' => DB::connection()->getPdo() ? true : false,
        'redis_connected' => Redis::connection()->ping() ? true : false,
    ];
});
EOL

# Enable CORS for Vue.js frontend
php artisan cors:install

# Run migrations
php artisan migrate

echo "Laravel project initialized successfully!"
