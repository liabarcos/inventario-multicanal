<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/inventario', 'inventario');
Route::view('/multicanal', 'multicanal');
