<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrowserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('')->group(function () {
    Route::get('', [BrowserController::class, 'index']);
    Route::get('{name}', [BrowserController::class, 'openFolder']);
    Route::get('download/{file}', [BrowserController::class, 'download']);
});



Route::get('/testing', function () {
    return Storage::allDirectories();
});