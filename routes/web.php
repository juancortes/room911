<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

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

Route::group(['middleware' => ['auth']], function() {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('depratamentoProduccions', App\Http\Controllers\DepratamentoProduccionController::class);
    //Route::post('/users/importCsvUsers', [UserController::class,'importCsvUsers'])->name('users.importCsvUsers');
    Route::post('import', function () {
        Excel::import(new UsersImport, request()->file('file'));
        return redirect()->back()->with('success','Data Imported Successfully');
    });

});


