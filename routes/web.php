<?php

use App\Http\Controllers\SatisController;
use App\Http\Middleware\AuthenticateOnceWithBasicAuth;
use App\Http\Middleware\Packages\EnsurePackagesAreBuilt;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', [SatisController::class, 'show'])
    ->name('home')
    ->middleware(EnsurePackagesAreBuilt::class);

Route::get('/login', fn () => redirect()->to(Filament::getLoginUrl()))
    ->name('login');

Route::get('/{path}', [SatisController::class, 'packages'])
    ->where('path', '.*\.(json|tar)$')
    ->middleware([AuthenticateOnceWithBasicAuth::class, 'role:user'])
    ->name('packages');
