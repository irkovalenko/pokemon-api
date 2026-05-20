<?php

use App\Http\Controllers\PokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [PokemonController::class, 'index']);

Route::get('/banned', [PokemonController::class, 'banned']);

Route::get('/pokemon/{id}', [PokemonController::class, 'show']);

Route::post('/pokemon/{pokemon}/toggle-ban', [PokemonController::class, 'toggleBan']);
