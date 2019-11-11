<?php

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


Auth::routes();


Route::get('/', 'HomeController@index')->name('home');

Route::get('/aktifasi/{id}','AktifasiAkunController@Aktifasi')->name('aktifasi');
Route::resource('level','LevelController');
Route::resource('administrator','AdministratorController');
Route::resource('kategori','KategoriController');
Route::resource('item','ItemController');
Route::POST('/store_gambarItem','ItemController@store_gambarItem')->name('store_gambarItem');
Route::GET('/ganti_gambar_utama/{id}','ItemController@ganti_gambar_utama')->name('ganti_gambar_utama');
Route::DELETE('/hapus_gambar_item/{id}','ItemController@hapus_gambar_item')->name('hapus_gambar_item');

Route::POST('/input_stock/{id}','StockerController@store')->name('input_stock');
Route::DELETE('/hapus_stock/{id}','StockerController@destroy')->name('hapus_stock');

Route::resource('kurir','KurirController');

Route::get('coba',function(){
	return view('welcome');
});


// Route::put('/level_update/{id}','LevelController@update')->name('level_update');
