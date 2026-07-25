<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('auth')->group(function () {
    Route::redirect('dashboard', 'projects')->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)
        ->scoped();
});

require __DIR__.'/settings.php';
