<?php

use App\Http\Controllers\PokemonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Services\PokemonService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});


Route::middleware(['auth', 'verified'])->group(function () {

    //pokemon routes
    Route::get('/dashboard', [PokemonController::class, 'index'])->name('dashboard');
    Route::get('/banned', [PokemonController::class, 'banned'])->name('banned');
    Route::post('/pokemons/{pokemon}/toggle-ban', [PokemonController::class, 'toggleBan'])->name('pokemons.toggleBan');
    Route::get('/pokemons/create', [PokemonController::class, 'create'])->name('pokemons.create');
    Route::post('/pokemons', [PokemonController::class, 'store'])->name('pokemons.store');
    Route::get('/pokemons/{name}', [PokemonController::class, 'show'])->name('pokemons.show');
    Route::get('/pokemons/{id}/edit', [PokemonController::class, 'edit'])->name('pokemons.edit');
    Route::patch('/pokemons/{id}', [PokemonController::class, 'update'])->name('pokemons.update');
    Route::delete('/pokemons/{id}', [PokemonController::class, 'destroy'])->name('pokemons.delete');

    //user routes
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');

    //profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
