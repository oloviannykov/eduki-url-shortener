<?php

use App\Http\Controllers\Api\UrlShortenerController;
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
Route::any('/', function () {
    return 'API is available';
});

Route::prefix('urls')
    ->name('urls.')
    ->group(function () {
        // Matches "api/urls/" URL
        Route::get('/', [UrlShortenerController::class, 'index'])
            ->name('get');
        // Matches "api/urls/new" URL
        Route::post('/new', [UrlShortenerController::class, 'create'])
            ->name('post');
        // Matches "api/urls/{id}/url" URL
        Route::get('/{id}/url', [UrlShortenerController::class, 'getUrlById'])
            ->where('id', '[0-9A-Za-z]{10,20}')
            ->name('getUrlById');
        // Matches "api/urls/{id}" URL
        Route::delete('/{id}', [UrlShortenerController::class, 'remove'])
            ->where('id', '[0-9A-Za-z]{10,20}')
            ->name('delete');
    });
