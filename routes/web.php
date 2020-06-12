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
Route::get('/locale/{locale}', 'HomeController@locale')->name('locale');

Route::get('/admin/import/csv/upload', 'Admin\ImportCSVController@index')->name('import.csv.upload')
    ->middleware('auth');
Route::post('/admin/import/csv/save', 'Admin\ImportCSVController@save')->name('import.csv.save')
    ->middleware('auth');
Route::get('/admin/import/csv/preview', 'Admin\ImportCSVController@preview')->name('import.csv.preview')
    ->middleware('auth');
Route::post('/admin/import/csv/process', 'Admin\ImportCSVController@process')->name('import.csv.process')
    ->middleware('auth');

Route::get('/admin/import/taxa/upload', 'Admin\ImportTaxaController@index')->name('import.taxa.upload');
Route::post('/admin/import/taxa/save', 'Admin\ImportTaxaController@save')->name('import.taxa.save');
Route::get('/admin/import/taxa/preview', 'Admin\ImportTaxaController@preview')->name('import.taxa.preview');
Route::post('/admin/import/taxa/process', 'Admin\ImportTaxaController@process')->name('import.taxa.process');

Route::resource('admin/colmap', 'Admin\ColumnMappingController')->middleware('auth');
Route::resource('admin/column', 'Admin\ColumnController')->middleware('auth');
Route::resource('admin/detail', 'Admin\DetailController')->middleware('auth');
Route::get('admin/item/new', 'Admin\ItemController@new')->name('item.new')->middleware('auth');
Route::resource('admin/item', 'Admin\ItemController')->middleware('auth');
Route::resource('admin/taxon', 'Admin\TaxonController')->middleware('auth');

Route::resource('admin/lists/list.element', 'Admin\Lists\ElementController')->shallow()->middleware('auth');
Route::resource('admin/lists/element.value', 'Admin\Lists\ValueController')->shallow()->middleware('auth');

Route::get('admin/lists/list/internal', 'Admin\Lists\ListController@internal')->name('list.internal')->middleware('auth');
Route::get('admin/lists/list/{id}/tree', 'Admin\Lists\ListController@tree')->name('list.tree')->middleware('auth');
Route::resource('admin/lists/list', 'Admin\Lists\ListController')->middleware('auth');
Route::resource('admin/lists/attribute', 'Admin\Lists\AttributeController')->middleware('auth');
