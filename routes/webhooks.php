<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('/packages')->name('packages.')->group(callback: function (): void {
    Route::post('/build', [App\Http\Controllers\Webhooks\PackagesController::class, 'build'])->name('build');
});
