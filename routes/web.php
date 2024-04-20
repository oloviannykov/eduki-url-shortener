<?php

use App\Http\Controllers\Web\UrlShortenerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return view('index')->with('errorMessage', $request->query('error'));
})->name('home');

Route::get('/go/{id}', [UrlShortenerController::class, 'useUrlHash']);
