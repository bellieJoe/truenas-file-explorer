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
    Route::get('', [BrowserController::class, 'index'])->middleware('ftp-auth');
    Route::get('login', [BrowserController::class, 'login'])->name('login');
    Route::get('logout', [BrowserController::class, 'login'])->name('logout');
    Route::post('signin', [BrowserController::class, 'signin'])->name('signin');
    Route::get('browse', [BrowserController::class, 'openFolder'])->name('browse')->middleware('ftp-auth');
    Route::get('navigate', [BrowserController::class, 'navigateTo'])->middleware('ftp-auth');
    Route::get('download', [BrowserController::class, 'download'])->middleware('ftp-auth');
    Route::get('make-directory', [BrowserController::class, 'makeDirectory'])->middleware('ftp-auth');
    Route::post('upload', [BrowserController::class, 'upload'])->middleware('ftp-auth');
    Route::post('delete-many', [BrowserController::class, 'deleteMany'])->middleware('ftp-auth');
});



Route::get('/testing', function () {
    return Storage::allDirectories();
});