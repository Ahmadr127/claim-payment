<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Billing\ChargeController;
use App\Http\Controllers\Billing\BillController;

/*
|--------------------------------------------------------------------------
| AJAX Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider (or bootstrap/app.php) 
| and all of them will be assigned to the "ajax" middleware group.
|
*/

// Middleware can be added here later (e.g. auth:sanctum)
Route::prefix('hospitalizations')->group(function () {
    
    // Charge Recording
    Route::post('{id}/charges/room', [ChargeController::class, 'recordRoom']);
    Route::post('{id}/charges/service', [ChargeController::class, 'recordService']);
    Route::post('{id}/charges/medication', [ChargeController::class, 'recordMedication']);
    
    // Bill Preview
    Route::get('{id}/bill-summary', [BillController::class, 'preview']);

});
