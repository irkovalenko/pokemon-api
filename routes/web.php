<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
    Route::get('/pokemons/{pokemon:uuid}/show', [PokemonController::class, 'show'])->name('pokemons.show');
    Route::get('/pokemons/{pokemon:uuid}/edit', [PokemonController::class, 'edit'])->name('pokemons.edit');
    Route::post('/pokemons/{pokemon:uuid}/update', [PokemonController::class, 'update'])->name('pokemons.update');
    Route::delete('/pokemons/{pokemon:uuid}', [PokemonController::class, 'destroy'])->name('pokemons.delete');

    //abilities routes
    Route::get('/abilities/search', [AbilityController::class, 'searchAbility'])->name('abilities.search');
    Route::patch('/abilities/{ability}', [AbilityController::class, 'update'])->name('abilities.update');


    //comments routes
    // web.php
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

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
