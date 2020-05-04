<?php

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
    #return view('welcome');
    return Redirect::to('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin/import/csv/upload', 'Admin\ImportCSVController@index')->name('import.csv.upload')
    ->middleware('auth');
Route::post('/admin/import/csv/save', 'Admin\ImportCSVController@save')->name('import.csv.save')
    ->middleware('auth');
Route::post('/admin/import/csv/process', 'Admin\ImportCSVController@process')->name('import.csv.process')
    ->middleware('auth');

Route::resource('list.element', 'ElementsController')->shallow()->middleware('auth');
Route::resource('element.value', 'ValuesController')->shallow()->middleware('auth');

Route::resource('list', 'ListsController')->middleware('auth');
Route::resource('attribute', 'AttributesController')->middleware('auth');
