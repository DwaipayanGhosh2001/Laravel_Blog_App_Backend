<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//User routes 
Route::post('/user/register', [UserController::class,'register']);
Route::post('/user/login', [UserController::class,'login']);
Route::post('/user/changePassword', [UserController::class,'changePassword'])->middleware('auth:sanctum');
Route::post('/user/update', [UserController::class,'update'])->middleware('auth:sanctum');
Route::get('/user/details', [UserController::class,'userDetails'])->middleware('auth:sanctum');
Route::post('/user/logout', [UserController::class,'logout'])->middleware('auth:sanctum');


Route::post('/blogs/create', [BlogController::class,'create'])->middleware('auth:sanctum');
//In this function multiple queries can be used at the route at the same time
        //Example: {{base_url}}/blogs?user_id=1&category=Fashion&keyword=Men
Route::get('/blogs', [BlogController::class,'list']);
Route::get('/blogs/{id}', [BlogController::class,'blogDetails']); //give user id
Route::post('/blogs/update/{id}', [BlogController::class,'update'])->middleware('auth:sanctum'); //give blog id
Route::delete('/blogs/delete/{id}', [BlogController::class,'delete'])->middleware('auth:sanctum'); //give blog id
Route::post('/blogs/toggleLike/{id}', [BlogController::class,'toggleLike'])->middleware('auth:sanctum');

Route::post('/comment/create/{id}', [CommentController::class,'create'])->middleware('auth:sanctum'); //give blog id
Route::get('/comment/{id}', [CommentController::class,'list']); //give blog id
Route::post('/comment/update/{id}', [CommentController::class,'update'])->middleware('auth:sanctum');
Route::delete('/comment/delete/{id}', [CommentController::class,'delete'])->middleware('auth:sanctum'); 

// Route::get('/category', [BlogController::class,'getCategory']);

Route::get('/test', function(){
    p("Working");
});
