@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Pengiriman', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Pengiriman','link' => '#')
                                                    	) 
                                  ])
        @if (session('success'))
		 	@component("components.alert", ["type" => "success"])
				{{ session('success') }}
			@endcomponent
		@endif
        <div class="card">
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th style="width: 5px;">No</th>
						<th>Waktu Kirim</th>
						<th>No Transaksi</th>
						<th>Pemesan</th>
						<th ><center>Jumlah Pesanan</center></th>
						<th ><center>Kurir</center></th>
						<th ><center>Jenis Transaksi</center></th>
						<th style="width: 50px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($pengiriman as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->created_at->format('d M Y H:i A') }}</td>
								<td>{{ $key->Transaksi->no_transaksi }}</td>
								<td>{{ $key->Transaksi->User->name }}</td>
								<td align="center">{{ $key->Transaksi->ItemTransaksi()->count() }} Pesanan</td>
								<td align="center">{{ $key->Kurir->nama}}</td>
								<td align="center">
									@if($key->Transaksi->metode_pembayaran == 1)
		       							<span class="label label-warning ">TopUp</span>
			       					@elseif($key->Transaksi->metode_pembayaran == 2)
			       						<span class="label label-info">COD</span>
			       					@elseif($key->Transaksi->metode_pembayaran == 3)
			       						<span class="label label-success">Bayar Di Toko</span>
			       					@endif
								</td>
								<td align="center">
									<a href="{{ route('transaksi.show', $key->Transaksi->id ) }}">
										<button class="btn btn-warning btn-sm"><i class="fa fa-search"></i></button>
									</a>
								</td>
								
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
        </div>
    @endcomponent
@endsection