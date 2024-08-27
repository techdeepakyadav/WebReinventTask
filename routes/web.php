<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;

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


Route::get('/',[TodoController::class,'index'])->name('todo.index');
Route::get('todos',[TodoController::class,'showAll'])->name('todo.all');
Route::post('todo/store',[TodoController::class,'store'])->name('store');
Route::post('todos/update/{todo}',[TodoController::class,'update'])->name('todo.update');
Route::delete('todos/delete/{todo}',[TodoController::class,'destroy'])->name('todo.destroy');