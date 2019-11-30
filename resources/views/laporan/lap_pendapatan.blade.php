@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Pendapatan', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Pendapatan','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('filter_laporan') }}">
	        	@csrf
	        	<div class="row">
	        		<div class="col-md-5">
	        			<div class="form-group">
			                <label>Mulai Tanggal</label>

			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" class="form-control pull-right datepicker" name="mt" autocomplete="off" value="{{ $input['mt'] }}">
			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        		<div class="col-md-5">
	        			<div class="form-group">
			                <label>Sampai Tanggal</label>

			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" class="form-control pull-right datepicker" name="st" autocomplete="off" value="{{ $input['st'] }}" >
			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        		<div class="col-md-2" style="margin-top: 25px;">
	        			<button class="btn btn-primary">Filter</button>
	        			<a href="{{ route('lap_pendapatan') }}"><label class="btn btn-warning" >Reset</label></a>
	        		</div>
	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	<div class="text-center">
        		<h4><u><b>{{ $kop }}</b></u></h4>
        	</div>
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th style="width: 5px;">No</th>
						<th>Waktu</th>
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
								<td>{{ $key->tgl_bayar->format('d M Y H:i a') }}</td>
								<td>{{ $key->no_transaksi }}</td>
								<td>{{ $key->User->name }}</td>
								<td align="center">{{ $key->ItemTransaksi()->count() }} Pesanan</td>
								<td>Rp. {{ number_format($key->total_bayar,'0','','.') }}</td>
								<td align="center">
									@if($key['metode_pembayaran'] == 1)
		       							<span class="label label-warning ">TopUp</span>
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
			<h5 style="margin-bottom: 3px;"><u>Total Pendapatan</u></h5>
			<h1 style="margin-top: 0px;">Rp {{ number_format($total_pendapatan,'0','','.') }}</h1>
        </div>
    @endcomponent
@endsection