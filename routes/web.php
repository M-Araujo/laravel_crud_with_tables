<?php

use Illuminate\Support\Facades\Route;

Route::get('/', ['App\Http\Controllers\DashBoardController', 'index']);


Route::resource('users', App\Http\Controllers\UserController::class);
