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
})->name('home'); 

Route::get('/tasks/', [TaskController::class, 'index'])->name('tasks.index');

Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');

Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');

Route::post('/tasks/', [TaskController::class, 'store'])->name('tasks.store');

Route::put('/tasks/{id}/update' , [TaskController::class, 'update'])->name('tasks.update');

Route::get('/tasks/{id}/delete',[TaskController::class, 'delete'])->name('tasks.delete');

Route::delete('/tasks/{id}/destroy', [TaskController::class, 'destroy'])->name('tasks.destroy');

Route::get('/tasks/progress', [TaskController::class, 'progress'])->name('tasks.progress');

Route::name('auth.')
->controller(AuthController::class)
->group(function () {
    Route::get('signup', 'signupForm')->name('signupForm');
    Route::post('signup', 'signup')->name('signup');
    Route::get('login', 'loginForm')->name('loginForm');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout');
});


