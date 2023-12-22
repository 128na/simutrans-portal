<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Front\ConversionController;
use Illuminate\Support\Facades\Route;

Route::post('conversion/{user}/{article}', [ConversionController::class, 'conversion']);
Route::post('shown/{user}/{article}', [ConversionController::class, 'shown']);
