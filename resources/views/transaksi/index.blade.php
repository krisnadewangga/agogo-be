@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Transaksi', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Transaksi','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
			<!-- <button class="btn btn-primary" data-toggle="modal" data-target="#modal_input">Create</button>
			<button class="btn btn-warning" data-toggle="modal" data-target="#modal_input">SetOngkir</button>
			<hr></hr> -->

			@if (session('success'))
			 	@component("components.alert", ["type" => "success"])
					{{ session('success') }}
				@endcomponent
			@endif

			<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th style="width: 5px;">No</th>
						<th>Waktu Pesanan</th>
						<th>No Transaksi</th>
						<th>Pemesan</th>
						<th ><center>Jumlah Pesanan</center></th>
						<th ><center>Total Bayar</center></th>
						<th ><center>Jenis Transaksi</center></th>
						<th style="width: 50px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($transaksi as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->created_at->format('d M Y H:i a') }}</td>
								<td>{{ $key->no_transaksi }}</td>
								<td>{{ $key->User->name }}</td>
								<td align="center">{{ $key->ItemTransaksi()->count() }} Pesanan</td>
								<td>Rp. {{ number_format($key->total_bayar,'0','','.') }}</td>
								<td align="center">
									@if($key['metode_pembayaran'] == 1)
		       							<span class="label label-warning ">Bukpay</span>
			       					@elseif($key['metode_pembayaran'] == 2)
			       						<span class="label label-info">COD</span>
			       					@elseif($key['metode_pembayaran'] == 3)
			       						<span class="label label-success">Bayar Ditempat</span>
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