<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});
// Route::get('/generate',[UserController::class,'show_graph'])->name('graph');
Route::get('/networkgraph/referalid',[UserController::class,'referalid'])->name('networkgraph.referalid');
Route::get('/networkgraph/createdby',[UserController::class,'createdby'])->name('networkgraph.createdby');
