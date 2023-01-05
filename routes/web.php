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
    Route::get('browse', [BrowserController::class, 'openFolder'])->name('browse');
    Route::get('navigate', [BrowserController::class, 'navigateTo']);
    Route::get('download', [BrowserController::class, 'download']);
    Route::get('make-directory', [BrowserController::class, 'makeDirectory']);
    Route::post('upload', [BrowserController::class, 'upload']);
    Route::post('delete-many', [BrowserController::class, 'deleteMany']);
});



Route::get('/testing', function () {
    return Storage::allDirectories();
});