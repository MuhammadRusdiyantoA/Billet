<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [UserController::class, 'login']);
Route::post('/register/buyer', [UserController::class, 'regBuyer']);
Route::post('/register/host', [UserController::class, 'regHost']);
Route::get('/event/random', [EventController::class, 'randomEvents']);

Route::get('/unauthorized', [UserController::class, 'unauthorized'])->name('unauth');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user/profile', [UserController::class, 'showProfile'])->middleware(['auth:sanctum', 'ability:admin,buyer,host']);
Route::put('/user/profile', [UserController::class, 'updateProfile'])->middleware(['auth:sanctum', 'ability:admin,buyer,host']);
Route::delete('/user/profile', [UserController::class, 'deleteProfile'])->middleware(['auth:sanctum', 'ability:buyer,host']);
Route::get('/transaction/profile', [TransactionController::class, 'transactionProfile'])->middleware(['auth:sanctum', 'ability:admin,buyer,host']);

// Buyer Route Group
Route::group(['middleware' => ['auth:sanctum', 'ability:buyer']], function() {
    Route::get('/ticket/buyer', [TicketController::class, 'buyerTicket']);
    Route::post('/ticket/buy', [TicketController::class, 'buy']);
    Route::post('/user/add-bal', [UserController::class, 'addBalance']);
});

// Host Route Group
Route::group(['middleware' => ['auth:sanctum', 'ability:host']], function() {
    Route::get('/event/host', [EventController::class, 'indexHostEvent']);
    Route::get('/event/host/{id}', [EventController::class, 'showHostEvent']);
    Route::post('/event/host', [EventController::class, 'createHostEvent']);
    Route::put('/event/host/{id}', [EventController::class, 'updateHostEvent']);
    Route::delete('/event/host/{id}', [EventController::class, 'deleteHostEvent']);
});

// Admin Route Group
Route::group(['middleware' => ['auth:sanctum', 'ability:admin']], function() {
    Route::resource('/user', UserController::class)->except(['create', 'edit']);
    Route::resource('/event', EventController::class)->except(['create', 'edit']);
    Route::resource('/role', RoleController::class)->except(['create', 'edit']);
    Route::resource('/ticket', TicketController::class)->except(['create', 'edit']);
    Route::resource('/transaction', TransactionController::class)->except(['create', 'edit']);
});

