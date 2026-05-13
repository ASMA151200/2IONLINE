<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::get('/user', [AuthController::class, 'me'])->middleware('auth:sanctum')->name('user');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn () => response()->json(['message' => 'Espace admin']))->name('admin.dashboard');
    
    // User management
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth:sanctum', 'role:formateur'])->group(function () {
    Route::get('/formateur/cours', fn () => response()->json(['message' => 'Espace formateur']))->name('formateur.cours');
});

Route::middleware(['auth:sanctum', 'role:etudiant'])->group(function () {
    Route::get('/etudiant/profil', fn () => response()->json(['message' => 'Espace etudiant']))->name('etudiant.profil');
});
