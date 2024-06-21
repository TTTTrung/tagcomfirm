<?php

use App\Http\Controllers\ApprovedplanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PlancreateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UnlockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use League\CommonMark\Extension\SmartPunct\DashParser;
use Spatie\Permission\Middleware\RoleMiddleware;

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

Route::get('', fn()=> to_route('landing'));

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');


Route::middleware(['auth',RoleMiddleware::class.':superAdmin|plAdmin|plSuperAdmin'])->group(function(){
    Route::get('/plan',[PlancreateController::class, 'index'])->name('plan');
    Route::get('/part',[PartController::class, 'index'])->name('part');
});
Route::middleware(['auth',RoleMiddleware::class.':superAdmin|plAdmin|plSuperAdmin|commoner'])->group(function(){
    
    Route::get('/listplan',[ApprovedplanController::class, 'index'])->name('listplan');
});

Route::middleware(['auth',RoleMiddleware::class.':superAdmin'])->group(function(){
    Route::get('/createuser',[UserController::class, 'index'])->name('createuser');
});

Route::middleware(['auth',RoleMiddleware::class.':scanner'])->group(function(){
    Route::get('/scan',[ScanController::class, 'index'])->name('scanconfirm');
});

Route::middleware(['auth',RoleMiddleware::class.':lock'])->group(function(){
    Route::get('/unlock',[UnlockController::class, 'index'])->name('unlock');
});

 Route::middleware(['auth'])->group(function(){
    Route::get('/history',[HistoryController::class, 'index'])->name('historyscan');
    Route::get('/landing', [DashboardController::class,'index'])->name('landing');
 });

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
