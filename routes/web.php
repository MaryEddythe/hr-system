<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\CreditsController;


Route::get('/', fn() => redirect()->route('employees.index'));

use App\Http\Controllers\EmployeeController;

Route::resource('employees', EmployeeController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);


Route::post('employees/{employee}/upload', [EmployeeController::class, 'uploadFile'])
    ->name('employees.upload');

Route::resource('divisions', DivisionController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::get('credits', [CreditsController::class, 'index'])
    ->name('credits.index');

Route::get('credits/cto', [CreditsController::class, 'cto'])
    ->name('credits.cto');


Route::post('credits', [CreditsController::class, 'store'])
    ->name('credits.store');

Route::get('credits/{credit}/edit', [CreditsController::class, 'edit'])
    ->name('credits.edit');

Route::put('credits/{credit}', [CreditsController::class, 'update'])
    ->name('credits.update');

Route::delete('credits/{credit}', [CreditsController::class, 'destroy'])
    ->name('credits.destroy');


Route::get('employees/{employee}/leave-history', [EmployeeController::class, 'leaveHistory'])
    ->name('employees.leave-history');


Route::get('api/employees/search', [CreditsController::class, 'search'])
    ->name('api.employees.search');
