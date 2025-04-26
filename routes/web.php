<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutiController;

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/login', [AuthController::class, 'loginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', [CutiController::class, 'index']);
Route::get('/cuti', [CutiController::class, 'list']); 
Route::get('/cuti/create', [CutiController::class, 'create']);
Route::post('/cuti', [CutiController::class, 'store']);
Route::post('/cuti/approve/{id}', [CutiController::class, 'approve'])->name('cuti.approve');
Route::post('/cuti/{id}/reject', [CutiController::class, 'reject'])->name('cuti.reject');

Route::get('/register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/cuti/export', [CutiController::class, 'export']);
Route::post('/cuti/import', [CutiController::class, 'import']);
Route::get('/cuti/template', [CutiController::class, 'template']);


