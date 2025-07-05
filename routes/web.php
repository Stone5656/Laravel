<?php

use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

Route::middleware(['web'])->group(function () {
    Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('l5-swagger.default.docs');
});
