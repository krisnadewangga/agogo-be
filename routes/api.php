<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserCollection;
use App\User;
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
Route::post('update_profil','Api\UserController@updateProfil');
Route::post('reset_password','Api\UserController@resetPassword');
Route::post('login','Api\UserController@login');
Route::get('get_saldo','Api\UserController@getSaldo');

Route::post('kirim_pesan','Api\PesanController@kirimPesan');
Route::get('list_pesan', 'Api\PesanController@listPesan');
Route::get('pesan_by_id', 'Api\PesanController@pesanById');
Route::post('baca_pesan_tiap_user','Api\PesanController@bacaPesanTiapUser');
Route::post('baca_pesan_tiap_id','Api\PesanController@bacaPesanTiapId');
Route::post('hapus_pesan','Api\PesanController@hapusPesan');

Route::get('list_promo','Api\MasterController@listPromo');
Route::get('list_kategori','Api\MasterController@ListKategori');
Route::get('list_item_all','Api\MasterController@ListItemAll');
Route::get('list_item_perkat','Api\MasterController@ListItemPerKat');
Route::get('cari_item','Api\MasterController@CariItem');
Route::get('top_ten','Api\MasterController@topTen');
Route::get('detail_item','Api\MasterController@DetailItem');

Route::post('transaksi','Api\TransaksiController@Store');
Route::get('get_otp','Api\TransaksiController@GetOtp');
Route::get('list_transaksi','Api\TransaksiController@ListTransaksi');
Route::get('detail_transaksi','Api\TransaksiController@DetailTransaksi');
Route::get('get_ongkir','Api\TransaksiController@GetOngkir');
Route::post('ajukan_batal_pesanan','Api\TransaksiController@AjukanBatalPesanan');
Route::get('list_penggunaan_saldo','Api\TransaksiController@ListPenggunaanSaldo');

//notifikasi
Route::get('list_notifikasi', 'Api\NotifikasiController@tampilNotifikasi');
Route::POST('read_notifiikasi', 'Api\NotifikasiController@readNotifikasi');

Route::get('cek_versi','Api\MasterController@CekVersi');


//get user
Route::get('users','Api\react\UserController@getUser');

//Auth API
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Api\react\UserController@login');
    Route::post('signup', 'AuthController@signup');
    Route::get('logout', 'AuthController@logout');
    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        
       // Route::get('user', 'AuthController@user');
    });
});



//produk
Route::get('product/{id}', 'Api\react\ProductController@show');

//Kas API
Route::post('/postKas', 'Api\react\KasController@postKas');
Route::get('cekKas/{id}','Api\react\KasController@cekKas');
Route::get('/getTrx/{id}', 'Api\react\KasController@getTrx');
Route::get('getTrxTest','Api\react\KasController@getTrxTest');
Route::post('/CheckApproval', 'Api\react\KasController@CheckApproval');
Route::put('/updateKas/{id}', 'Api\react\KasController@updateKas');




// react
Route::get('categories', 'Api\react\ProductController@categories');
Route::get('products', 'Api\react\ProductController@products');
Route::post('orders','Api\react\OrderController@postOrder');
Route::get('cekInvoice', 'Api\react\OrderController@checkLastInvoice');
Route::get('get_transaksi/{no_transaksi}','Api\react\OrderController@getTransaksi');
Route::post('bayar_transaksi','Api\react\OrderController@bayarTransaksiM');

//simpan transaksi 
Route::post('/keepOrders', 'Api\react\OrderController@keepOrder');
Route::get('/orders','Api\react\OrderController@getUnpaidOrders');
Route::get('/order/{id}', 'Api\react\OrderController@getOrderDetail');
Route::delete('/order/{id}', 'Api\react\OrderController@deleteOrder');


//Refund API
Route::post('/refunds', 'Api\react\OrderController@postRefunds');

//paid
Route::get('/PaidOrders', 'Api\react\OrderController@getPaidOrders');
Route::get('/paid_preorders', 'Api\react\PreorderController@paid_preorder');


// cek invoice 
Route::get('/cekPOInvoice', 'Api\react\PreorderController@checkLastInvoicePesanan');

// preoders
Route::get('/preorders', 'Api\react\PreorderController@index');
Route::post('preorders','Api\react\PreorderController@store');
Route::get('/preorder/{id}', 'Api\react\PreorderController@show');
Route::post('/bayarPreorder', 'Api\react\PreorderController@bayarPreorder');
Route::post('/editPreorders', 'Api\react\PreorderController@editPreorder');
Route::put('/cancelPreorder/{id}', 'Api\react\PreorderController@cancelPreorder');

//produksi 

Route::get('/availProducts', 'Api\react\ProduksiController@getAvailProduct');
Route::get('/notAvailProducts', 'Api\react\ProduksiController@getNotAvailProduct');


Route::get('/TrxByProduct/{id}', 'Api\react\ProduksiController@getTrxByProduct');
Route::get('/GetLastDate', 'Api\react\ProduksiController@GetLastDate');
Route::post('/postProduction', 'Api\react\ProduksiController@postProduction');
Route::post('/ubahTanggal', 'Api\react\ProduksiController@ubahTanggal');
//Route::put('/updateStock/{id}', 'Api\react\ProduksiController@updateStock');

Route::get('coba','Api\react\OrderController@Coba');


// Kurir Api
Route::post('login_kurir','Api\KurirController@login');
Route::get('detail_kurir','Api\KurirController@DetailKurir');
Route::get('job_now','Api\KurirController@JobNow');
Route::get('list_job','Api\KurirController@ListJob');
Route::post('selesaikan_job','Api\KurirController@SelesaikanJob');

Route::get('payment_channel','Api\TripayController@paymentChannel');
Route::post('payment','Api\TripayController@Transaksi');
Route::post('callback_tripay', 'Api\TripayController@Callback');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

