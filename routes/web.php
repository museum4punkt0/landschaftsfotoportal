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

Route::get('/admin/import/csv/upload', 'Admin\ImportCSVController@index')->name('import.csv.upload');
Route::post('/admin/import/csv/save', 'Admin\ImportCSVController@save')->name('import.csv.save');
Route::get('/admin/import/csv/preview', 'Admin\ImportCSVController@preview')->name('import.csv.preview');
Route::post('/admin/import/csv/process', 'Admin\ImportCSVController@process')->name('import.csv.process');

Route::get('/admin/import/taxa/upload', 'Admin\ImportTaxaController@index')->name('import.taxa.upload');
Route::post('/admin/import/taxa/save', 'Admin\ImportTaxaController@save')->name('import.taxa.save');
Route::get('/admin/import/taxa/preview', 'Admin\ImportTaxaController@preview')->name('import.taxa.preview');
Route::post('/admin/import/taxa/process', 'Admin\ImportTaxaController@process')->name('import.taxa.process');

Route::get('/admin/import/items/upload', 'Admin\ImportItemsController@index')->name('import.items.upload');
Route::post('/admin/import/items/save', 'Admin\ImportItemsController@save')->name('import.items.save');
Route::get('/admin/import/items/preview', 'Admin\ImportItemsController@preview')->name('import.items.preview');
Route::post('/admin/import/items/process', 'Admin\ImportItemsController@process')->name('import.items.process');

Route::resource('admin/colmap', 'Admin\ColumnMappingController');
Route::resource('admin/column', 'Admin\ColumnController');
Route::resource('admin/detail', 'Admin\DetailController');
Route::get('admin/item/new', 'Admin\ItemController@new')->name('item.new');
Route::resource('admin/item', 'Admin\ItemController');
Route::resource('admin/taxon', 'Admin\TaxonController');

Route::resource('admin/lists/list.element', 'Admin\Lists\ElementController')->shallow();
Route::resource('admin/lists/element.value', 'Admin\Lists\ValueController')->shallow();

Route::get('admin/lists/list/internal', 'Admin\Lists\ListController@internal')->name('list.internal');
Route::get('admin/lists/list/{id}/tree', 'Admin\Lists\ListController@tree')->name('list.tree');
Route::resource('admin/lists/list', 'Admin\Lists\ListController');
Route::resource('admin/lists/attribute', 'Admin\Lists\AttributeController');
