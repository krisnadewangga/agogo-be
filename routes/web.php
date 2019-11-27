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
Route::get('/load_notif','NotifikasiController@loadNotif')->name('load_notif');
Route::get('/get_jum_pesanan','NotifikasiController@GetJumPesanan')->name('get_jum_pesanan');
Route::get('/get_jum_pengiriman','NotifikasiController@GetJumPengiriman')->name('get_jum_pengiriman');

Route::get('/baca_notif','NotifikasiController@bacaNotif')->name('baca_notif');

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
Route::post('/set_ongkir','KurirController@SetOngkir')->name('set_ongkir');

Route::resource('transaksi','TransaksiController');
Route::get('/batal_transaksi','TransaksiController@BatalTransaksi')->name('batal_transaksi');
Route::post('/ambil_pesanan','TransaksiController@AmbilPesanan')->name('ambil_pesanan');
Route::resource('topup_saldo','TopupSaldoController');
Route::get('/list_topup_saldo','TopupSaldoController@ListTopupSaldo')->name('list_topup_saldo');
Route::get('/cari_user','TopupSaldoController@CariUser')->name('cari_user');


Route::resource('pengiriman','PengirimanController');

Route::get('pesanan_diterima/{id}','TransaksiController@pesananDiterima')->name('pesanan_diterima');

Route::get('/lap_pendapatan','LaporanController@LapPendapatan')->name('lap_pendapatan');
Route::post('/filter_laporan','LaporanController@FilterLaporan')->name('filter_laporan');
Route::get('/lap_user','LaporanController@LapUser')->name('lap_user');

Route::get('coba',function(){
	return view('welcome');
});

// Route::put('/level_update/{id}','LevelController@update')->name('level_update');
