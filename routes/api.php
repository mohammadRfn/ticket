<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketMessageController;

Route::post('register', [AuthController::class, 'register']);
//Route::post('login',    [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user',    function(Request $req) { return $req->user(); });
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('tickets',            [TicketController::class, 'index']);
    Route::post('tickets',           [TicketController::class, 'store']);
    Route::post('tickets/{id}/close', [TicketController::class, 'close']);

    Route::get('tickets/{id}/messages',  [TicketMessageController::class, 'index']);
    Route::post('tickets/{id}/messages', [TicketMessageController::class, 'store']);
});
