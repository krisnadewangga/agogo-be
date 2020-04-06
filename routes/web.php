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
use App\Events\PusherEvent;
use App\Helpers\SendNotif;

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/get_bulan','HomeController@getBulan')->name('get_bulan');
Route::get('/get_top_ten','HomeController@getTopTen')->name('get_top_ten');
Route::get('/set_grafik','HomeController@setGrafik')->name('set_grafik');
Route::get('/load_notif','NotifikasiController@loadNotif')->name('load_notif');
Route::get('/get_jum_pesanan','NotifikasiController@GetJumPesanan')->name('get_jum_pesanan');
Route::get('/get_jum_pengiriman','NotifikasiController@GetJumPengiriman')->name('get_jum_pengiriman');
Route::get('/get_jum_ap','NotifikasiController@GetJumAP')->name('get_jum_ap');
Route::get('/get_jum_kp','NotifikasiController@GetJumKP')->name('get_jum_kp');
Route::get('/list_notifikasi','NotifikasiController@index')->name('list_notifikasi');
Route::get('/baca_notif','NotifikasiController@bacaNotif')->name('baca_notif');
Route::get('/in_ganti_password','ProfilController@inGantiPassword')->name('in_ganti_password');
Route::POST('/submit_ganti_password','ProfilController@gantiPassword')->name('submitGantiPassword');
Route::POST('/submit_ganti_fp','ProfilController@gantiFotoProfil')->name('submit_ganti_fp');

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
Route::get('/pengajuan_batal_pesanan','TransaksiController@PengajuanBatalPesanan')->name('pengajuan_batal_pesanan');
Route::get('/konfirmasi_pembayaran','TransaksiController@KonfirmasiPembayaran')->name('konfirmasi_pembayaran');

Route::get('/batal_transaksi','TransaksiController@BatalTransaksi')->name('batal_transaksi');
Route::post('/ambil_pesanan','TransaksiController@AmbilPesanan')->name('ambil_pesanan');
Route::resource('topup_saldo','TopupSaldoController');
Route::get('/list_topup_saldo','TopupSaldoController@ListTopupSaldo')->name('list_topup_saldo');
Route::get('/cari_user','TopupSaldoController@CariUser')->name('cari_user');
Route::resource('pengiriman','PengirimanController');
Route::get('pesanan_diterima/{id}','TransaksiController@pesananDiterima')->name('pesanan_diterima');
Route::get('konfir_pembayaran/{id}','TransaksiController@konfirBayar')->name('konfir_pembayaran');

Route::get('/lap_pendapatan','LaporanController@LapPendapatan')->name('lap_pendapatan');
Route::get('/detail_transaksi/{id}','LaporanController@DetailTransaksi')->name('detail_transaksi'); // untuk transaksi dari react
Route::post('/filter_laporan','LaporanController@FilterLaporan')->name('filter_laporan');
Route::get('/lap_user','LaporanController@LapUser')->name('lap_user');
Route::get('/detail_user/{id}','LaporanController@DetailUser')->name('detail_user');
Route::get('/blokir_user/{id}','LaporanController@BlokirUser')->name('blokir_user');

Route::get('/penjualan','LaporanController@ShowPenjualan')->name('penjualan');
Route::get('/set_grafik_penjualan','LaporanController@setGrafikPenjualan')->name('set_grafik_penjualan');
Route::get('/dataPenjualan','LaporanController@showDataPenjualan')->name('data_penjualan');
Route::get('/set_data_penjualan','LaporanController@setDataPenjualan')->name('set_data_penjualan');

Route::get('/list_promo_selesai', 'PromoController@listPromoSelesai')->name('list_promo_selesai');
Route::resource('/setup_promo','PromoController');

Route::get('/msessages','MessageController@Index');
Route::get('/dashboard_pesan','PesanController@dashboardPesan');
Route::get('/list_pesan','PesanController@listPesan');
Route::post('/kirim_pesan','PesanController@kirimPesan')->name('kirim_pesan');
Route::get('/hapus_pesan/{id}','PesanController@hapusPesan')->name('hapus_pesan');
Route::get('/get_jum_pesan','PesanController@getJumPesan')->name('get_jum_pesan');
Route::get('/baca_pesan/{id}','PesanController@bacaPesan')->name('baca_pesan');

Route::post('/get_pesanan','TransaksiController@filterTransaksi')->name('get_pesanan');

Route::get('/tes_event',function(){
	$message = ['user_id' => 21, 'name' => 'Fajrin Ismail', 'waktu' => '2020-01-01', 'jumPesan' => 0];
	SendNotif::SendNotPesan('2',$message);
});

