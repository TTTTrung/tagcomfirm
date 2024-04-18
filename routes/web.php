<?php

use App\Http\Controllers\ApprovedplanController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PlancreateController;
use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/plan',[PlancreateController::class, 'index'])->name('plan');
Route::get('/part',[PartController::class, 'index'])->name('part');
Route::get('/listplan',[ApprovedplanController::class, 'index'])->name('listplan');
Route::get('/createuser',[UserController::class, 'index'])->name('createuser');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
