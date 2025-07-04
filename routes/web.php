<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
// Route::post('posts_search', [PostController::class, 'index'])->name('posts.search'); // Add POST for AJAX
Route::resource('posts', PostController::class);

Route::get('/', function () {
    return view('welcome');
});
Route::delete('posts/{id}/image', [PostController::class, 'removeImage'])->name('posts.removeImage');