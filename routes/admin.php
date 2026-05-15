<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\InscriptionController;

Route::prefix('admin')
->middleware([
    'auth:sanctum',
    'role:admin'
])

->group(function(){

    Route::get(
        '/dashboard',
        fn()=>response()->json([
            'message'=>'Espace admin'
        ])
    );

    Route::apiResource(
        'users',
        UserController::class
    );

    Route::apiResource(
        'formations',
        FormationController::class
    );

    Route::post(
        '/inscriptions',
        [InscriptionController::class,'inscrire']
    );

});