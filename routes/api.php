<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::group(["middleware" => "auth:sanctum"], 
    function(){
        Route::get('userprofile',[AuthController::class,'userProfile']);
        
        Route::post('logout',[AuthController::class,'logout']);

        Route::get('userResource',[AuthController::class,'userResource']);
        
        Route::middleware(AdminMiddleware::class)->group(function() {
            
            Route::get('/admin-dashboard', function(){
                return response()->json([
                    'message' => 'Welcome Admin'
                ]);
            });
        });
    }
);