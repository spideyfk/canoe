<?php

use App\Http\Controllers\AliasController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\FundManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/funds', [FundController::class, 'index']);
Route::post('/funds', [FundController::class, 'store']);
Route::put('/funds/{fund}', [FundController::class, 'update']);
Route::get('/funds/potential-duplicates', [FundController::class, 'getPotentialDuplicates']);

Route::post('/fund-managers', [FundManagerController::class, 'store']);

Route::post('/companies', [CompanyController::class, 'store']);

Route::post('/aliases', [AliasController::class, 'store']);
