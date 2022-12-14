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
    return Redirect::to('frontend');
});

Auth::routes([
    'register' => config('ui.user_registration', false),
    'verify' => true,
]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/frontend', 'HomeController@frontend')->name('frontend');
Route::get('/impressum', 'HomeController@frontend')->name('impressum');
Route::get('/privacy', 'HomeController@frontend')->name('datenschutz');
Route::get('/credits', 'HomeController@frontend')->name('danksagung');
Route::get('/about', 'HomeController@frontend')->name('über das portal');
Route::get('/locale/{locale}', 'HomeController@locale')->name('locale');
Route::get('/search', 'SearchController@index')->name('search.index');

Route::get('/email/change', 'Auth\ChangeEmailController@change')->name('email.change');
Route::post('/email/store', 'Auth\ChangeEmailController@store')->name('email.store');

Route::get('sipnr/{sipnr}', 'BfnController@redirectSipnr')->name('bfn.sipnr');

Route::get('item/show/own', 'ItemController@own')->name('item.show.own');
Route::get('download/{item}', 'ItemController@download')->name('item.download');
Route::get('gallery', 'ItemController@gallery')->name('item.gallery');
Route::get('timeline', 'ItemController@timeline')->name('item.timeline');
Route::get('map', 'ItemController@map')->name('item.map');
Route::delete('item/{item}/draft', 'ItemController@destroyDraft')->name('item.destroy.draft');
Route::resource('/item', 'ItemController')->except(['index'])->names([
    'create' => 'item.create.own',
    'store' => 'item.store.own',
    'show' => 'item.show.public',
    'edit' => 'item.edit.own',
    'update' => 'item.update.own',
    'destroy' => 'item.destroy.own'
]);

Route::get('image/random', 'ImageController@getRandom')->name('image.random');

Route::get('menu/children', 'AjaxMenuController@getChildren')->name('menu.children');
Route::get('map/all', 'AjaxMapController@all')->name('map.all');
Route::get('map/search', 'AjaxMapController@searchResults')->name('map.search');
Route::get('map/config/', 'AjaxMapController@getConfig')->name('map.config');
Route::get('map/points/', 'AjaxMapController@getPointFeaturesForItem')->name('map.points');
Route::get('map/polygons/', 'AjaxMapController@getPolygonFeaturesForItem')->name('map.polygons');
Route::post('comment/{item}/store', 'AjaxCommentController@store')->name('comment.store');
Route::post('comment/{comment}/update', 'AjaxCommentController@update')->name('ajax.comment.update');
Route::post('comment/{comment}/destroy', 'AjaxCommentController@destroy')->name('ajax.comment.destroy');
Route::get('comment', 'CommentController@index')->name('comment.index');
Route::post('cart/{item}/add', 'AjaxCartController@add')->name('cart.add');
Route::post('cart/{cart}/remove', 'AjaxCartController@remove')->name('cart.remove');
Route::get('cart', 'CartController@index')->name('cart.index');

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
Route::get('/admin/import/items/fix_ext', 'Admin\ImportItemsController@fix_ext')->name('import.items.fix_ext');
Route::get('admin/import/items/line', 'Admin\AjaxImportController@importLine')->name('ajax.import.line');
Route::get('admin/import/items/latlon', 'Admin\AjaxImportController@importLatLon')->name('ajax.import.latlon');

Route::get('admin/colmap/map/{item_type?}', 'Admin\ColumnMappingController@map')->name('colmap.map');
Route::post('admin/colmap/map/store', 'Admin\ColumnMappingController@map_store')->name('colmap.map.store');
Route::get('admin/colmap/sort/{item_type?}', 'Admin\ColumnMappingController@sort')->name('colmap.sort');
Route::post('admin/colmap/sort/store', 'Admin\ColumnMappingController@sort_store')->name('colmap.sort.store');
Route::get('admin/colmap/autocomplete', 'Admin\ColumnMappingController@autocomplete')->name('colmap.autocomplete');
Route::get('admin/colmap/publish/{colmap?}', 'Admin\ColumnMappingController@publish')->name('colmap.publish');
Route::resource('admin/colmap', 'Admin\ColumnMappingController');
Route::get('admin/column/autocomplete', 'Admin\ColumnController@autocomplete')->name('column.autocomplete');
Route::resource('admin/column', 'Admin\ColumnController');
Route::get('admin/detail/orphans', 'Admin\DetailController@removeOrphans')->name('detail.orphans');
Route::resource('admin/detail', 'Admin\DetailController');

Route::get('admin/item/new', 'Admin\ItemController@new')->name('item.new');
Route::get('admin/titles/create', 'Admin\ItemController@createTitles')->name('titles.create');
Route::post('admin/titles/store', 'Admin\ItemController@storeTitles')->name('titles.store');
Route::get('admin/item/unpublished', 'Admin\ItemController@list_unpublished')->name('item.unpublished');
Route::get('admin/item/publish/{item?}', 'Admin\ItemController@publish')->name('item.publish');
Route::get('admin/item/autocomplete', 'Admin\ItemController@autocomplete')->name('item.autocomplete');
Route::get('admin/item/orphans', 'Admin\ItemController@removeOrphans')->name('item.orphans');
Route::resource('admin/item', 'Admin\ItemController');
Route::get('admin/comment', 'Admin\CommentController@list_all')->name('comment.all');
Route::get('admin/comment/unpublished', 'Admin\CommentController@list_unpublished')->name('comment.unpublished');
Route::get('admin/comment/publish/{comment?}', 'Admin\CommentController@publish')->name('comment.publish');
Route::resource('admin/item.comment', 'Admin\CommentController')->shallow();
Route::get('admin/revision/deleted', 'Admin\ItemRevisionController@deleted')->name('revision.deleted');
Route::delete('admin/revision/{revision}/draft', 'Admin\ItemRevisionController@destroyDraft')->name('revision.destroy.draft');
Route::resource('admin/revision', 'Admin\ItemRevisionController');
Route::get('admin/taxon/autocomplete', 'Admin\TaxonController@autocomplete')->name('taxon.autocomplete');
Route::resource('admin/taxon', 'Admin\TaxonController');
Route::resource('admin/user', 'Admin\UserController');

Route::get('admin/lists/list/{id}/element/autocomplete', 'Admin\Lists\ElementController@autocomplete')->name('element.autocomplete');
Route::get('admin/lists/list/{id}/element/create_batch',
'Admin\Lists\ElementController@createBatch')->name('element.create_batch');
Route::post('admin/lists/list/{id}/element/store_batch',
'Admin\Lists\ElementController@storeBatch')->name('list.element.store_batch');
Route::resource('admin/lists/list.element', 'Admin\Lists\ElementController')->shallow();
Route::resource('admin/lists/element.value', 'Admin\Lists\ValueController')->shallow();

Route::get('admin/list/types', 'Admin\Lists\ListController@showItemTypes')->name('list.item_types');
Route::get('admin/list/groups', 'Admin\Lists\ListController@showColumnGroups')->name('list.column_groups');
Route::get('admin/list/translations', 'Admin\Lists\ListController@showTranslations')->name('list.translations');
Route::get('admin/lists/list/internal', 'Admin\Lists\ListController@internal')->name('list.internal');
Route::get('admin/lists/list/{list}/tree', 'Admin\Lists\ListController@tree')->name('list.tree');
Route::get('admin/lists/list/{list}/export', 'Admin\Lists\ListController@export')->name('list.export');
Route::resource('admin/lists/list', 'Admin\Lists\ListController');
Route::resource('admin/lists/attribute', 'Admin\Lists\AttributeController');

Route::get('admin/module/new', 'Admin\ModuleController@new')->name('module.new');
Route::resource('admin/module', 'Admin\ModuleController');
