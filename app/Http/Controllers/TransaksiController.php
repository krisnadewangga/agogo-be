<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use App\Kurir;

class TransaksiController extends Controller
{
    public function index()
    {
    	$transaksi = Transaksi::where('status','1')->get();

    	return view('transaksi.index',compact('transaksi'));
    }

    public function show($id)
    {
    	$transaksi = Transaksi::findOrFail($id);
        $kurir = Kurir::where('status_aktif','1')->get();
    	// return $transaksi;
    	return view('transaksi.detail',compact('transaksi','kurir'));
    }
}
