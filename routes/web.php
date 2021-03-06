<?php

use App\Http\Controllers\MessageController;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('user-list', [MessageController::class, 'user_list']);
    Route::get('user-messages/{id}', [MessageController::class, 'user_messages']);
    Route::post('/send-message', [MessageController::class, 'insert_message']);
    Route::get('delete-single-message/{message}', [MessageController::class, 'delete_single_message']);
    Route::get('delete-all-message/{id}', [MessageController::class, 'delete_all_message']);
});
