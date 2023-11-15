<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home')->middleware('auth'); 

Route::name('tasks.')
->controller(TaskController::class)
->middleware('auth')
->prefix('tasks')
->group(function(){
    Route::get('/', 'index')->name('index');
    Route::get('{id}/edit', 'edit')->name('edit');
    Route::get('create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::put('{id}/update', 'update')->name('update');
    Route::get('{id}/delete', 'delete')->name('delete');
    Route::delete('{id}/destroy', 'destroy')->name('destroy');
    Route::get('progress', 'progress')->name('progress');
    Route::patch('{id}/move', 'move')->name('move');
});



Route::name('auth.')
->controller(AuthController::class)
->group(function () {
    Route::get('signup', 'signupForm')->name('signupForm')->middleware('guest');
    Route::post('signup', 'signup')->name('signup')->middleware('guest');
    Route::get('login', 'loginForm')->name('loginForm')->middleware('guest');
    Route::post('login', 'login')->name('login')->middleware('guest');
    Route::post('logout', 'logout')->name('logout')->middleware('auth');
});


