<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pokemons-dashboard', [PokemonController::class, 'index'])->name('pokemons-dashboard');
    Route::get('/banned', [PokemonController::class, 'banned'])->name('banned');
    Route::post('/pokemons/{pokemon}/toggle-ban', [PokemonController::class, 'toggleBan'])->name('pokemons.toggleBan');
    Route::get('/pokemons/{id}', [PokemonController::class, 'show'])->name('pokemons.show');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/{user}', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
