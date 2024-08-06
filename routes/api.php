<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Front\ConversionController;
use Illuminate\Support\Facades\Route;

Route::post('conversion/{article}', (new ConversionController)->conversion(...));
Route::post('shown/{article}', (new ConversionController)->shown(...));
