<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register','Api\UserController@register');
Route::post('lengkapi_alamat', 'Api\UserController@LengkapiALamat');
Route::get('profil_user','Api\UserController@ProfilUser');
Route::post('login','Api\UserController@login');

Route::get('list_kategori','Api\MasterController@ListKategori');
Route::get('list_item_all','Api\MasterController@ListItemAll');
Route::get('list_item_perkat','Api\MasterController@ListItemPerKat');
Route::get('detail_item','Api\MasterController@DetailItem');

Route::post('transaksi','Api\TransaksiController@Store');
Route::get('list_transaksi','Api\TransaksiController@ListTransaksi');
Route::get('detail_transaksi','Api\TransaksiController@DetailTransaksi');
Route::get('get_ongkir','Api\TransaksiController@GetOngkir');

//notifikasi
Route::get('list_notifikasi', 'Api\NotifikasiController@tampilNotifikasi');
Route::POST('read_notifiikasi', 'Api\NotifikasiController@readNotifikasi');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
