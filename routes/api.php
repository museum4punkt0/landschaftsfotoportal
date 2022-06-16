<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('v1/taxon/fwTaxonId/{taxon}/items', 'Api\v1\TaxonController@listItemsByFwTaxon');
Route::get('v1/specimen/{item}', 'Api\v1\ItemController@showSpecimen')->name('api.item.show.specimen');
Route::get('v1/image/random', 'Api\v1\ItemController@showRandomImage')->name('api.item.show.random_image');
