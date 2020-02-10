@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Konfirmasi Pembayaran', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Konfirmasi Pembayaran','link' => '#')
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
						<th>Waktu Pesanan</th>
						<th>No Transaksi</th>
						<th>Pemesan</th>
						<th ><center>Jumlah Pesanan</center></th>
						<th ><center>Jenis Transaksi</center></th>
						<th style="width: 50px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($transaksi as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->updated_at->format('d M Y H:i A') }}</td>
								<td>{{ $key->no_transaksi }}</td>
								<td>{{ $key->User->name }}</td>
								<td align="center">{{ $key->ItemTransaksi()->count() }} Pesanan</td>
								<td align="center">
									@if($key->metode_pembayaran == 1)
		       							<span class="label label-warning ">TopUp</span>
			       					@elseif($key->metode_pembayaran == 2)
			       						<span class="label label-info">Bank Transfer</span>
			       					@elseif($key->metode_pembayaran == 3)
			       						<span class="label label-success">Bayar Di Toko</span>
			       					@endif
								</td>
								<td align="center">
									<a href="{{ route('transaksi.show', $key->id ) }}">
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