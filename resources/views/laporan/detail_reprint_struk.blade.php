@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-center mb-4">{{$transaksi->for_ps == '1' ? 'STRUK PESANAN' : 'STRUK PEMBELIAN'}}</h5>
                    <hr>
                    
                    <!-- No Invoice -->
                    <div class="row mb-2">
                        <div class="col-6">No Invoice</div>
                        <div class="col-6 text-right">{{ $transaksi->no_transaksi }}</div>
                    </div>
                    
                    <!-- Tanggal dan Waktu -->
                    <div class="row mb-2">
                        <div class="col-6">Tanggal & Waktu</div>
                        <div class="col-6 text-right">{{ $transaksi->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <!-- Nama Kasir -->
                    <div class="row mb-3">
                        <div class="col-6">Kasir</div>
                        <div class="col-6 text-right">{{ $transaksi->nama_kasir }}</div>
                    </div>
                    
                    <hr>
                    
                    <!-- Item Transaksi -->
                    @foreach($transaksi->items as $item)
                    <div class="row mb-2">
                        <div class="col-8">
                            <small>{{ $item->product_name }}</small><br>
                            <small class="text-muted">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                        </div>
                        <div class="col-4 text-right">
                            <small>Rp {{ number_format($item->total, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    @endforeach
                    
                    <hr>
                    
                    <!-- Subtotal -->
                    <div class="row mb-2">
                        <div class="col-6">Subtotal</div>
                        <div class="col-6 text-right">Rp {{ number_format($transaksi->total_transaksi, 0, ',', '.') }}</div>
                        @if($transaksi->for_ps == '1')
                        <div class="col-6">Add Fee</div>
                        <div class="col-6 text-right">Rp {{ number_format($transaksi->Preorder->add_fee, 0, ',', '.') }}</div>
                        <div class="col-6">Diskon</div>
                        <div class="col-6 text-right">Rp {{ number_format($transaksi->Preorder->discount, 0, ',', '.') }}</div>
                        @endif
                    </div>
                    
                    <!-- Grand Total -->
                    <div class="row mb-3">
                        <div class="col-6"><strong>Grand Total</strong></div>
                        <div class="col-6 text-right"><strong>Rp {{ number_format($transaksi->total_transaksi, 0, ',', '.') }}</strong></div>
                    </div>
                    
                    <hr>
                    
                    <!-- Pembayaran -->
                    <div class="row mb-2">
                        <div class="col-6">Pembayaran</div>
                        @if($transaksi->for_ps == '1')
                        <div class="col-6 text-right">(UANG MUKA) Rp {{ number_format($transaksi->Preorder->uang_muka, 0, ',', '.') }}</div>
                        <div class="col-6 text-right">(CASH) Rp {{ number_format($transaksi->Preorder->uang_dibayar, 0, ',', '.') }}</div>
                        @else
                        <div class="col-6 text-right">(CASH) Rp {{ number_format($transaksi->cash + ($transaksi->total_bayar - $transaksi->total_transaksi), 0, ',', '.') }}</div>
                        <div class="col-6 text-right">(TRANSFER) Rp {{ number_format($transaksi->transfer, 0, ',', '.') }}</div>
                        <div class="col-6 text-right">(QRIS) Rp {{ number_format($transaksi->qris, 0, ',', '.') }}</div>
                        @endif
                    </div>
                    
                    <!-- Uang Kembali -->
                    <div class="row mb-3">
                        <div class="col-6">Uang Kembali</div>
                        @if($transaksi->for_ps == '1')
                        <div class="col-6 text-right">Rp {{ number_format($transaksi->Preorder->uang_kembali, 0, ',', '.') }}</div>
                        @else
                        <div class="col-6 text-right">Rp {{ number_format($transaksi->total_bayar - $transaksi->total_transaksi, 0, ',', '.') }}</div>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="text-center">
                        <button class="btn btn-primary btn-sm" onclick="window.print()">Cetak</button>
                        <a href="{{ route('lap_reprint_struk') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection