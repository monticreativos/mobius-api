<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->view('site-unavailable', status: 404);
});
