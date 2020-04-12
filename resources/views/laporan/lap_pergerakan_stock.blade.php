@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Pergerakan Stock', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Pergerakan Stock','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('cari_laporan_pergerakan_stock') }}">
	        	@csrf
	        	<div class="row">
	        		<div class="col-md-12">
	        			<div class="form-group  @error('tanggal') has-error @enderror">
			                <label>Pilih Tanggal</label>
			                   @error('tanggal')
						            <label class="control-label" for="inputError">
				                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
				                	</label>    
						      @enderror 
			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" id="mt" class="form-control pull-right datepicker" name="tanggal" autocomplete="off" value="{{ $input['tanggal'] }}">
			               
			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        	</div>
	        	<div style="margin-top: 5px;">
	        		<button class="btn btn-primary">Cari</button>
	        		<a href="{{ route('lap_pergerakan_stock') }}"><label class="btn btn-warning" >Reset</label></a>
	        		<a href="{{ route('lap_pendapatan') }}"><label class="btn btn-success" >Export</label></a>
	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th rowspan="2" style="width: 5px;">No</th>
						<th rowspan="2" class="nowrap">Kode Item</th>
						<th rowspan="2" class="nowrap">Nama Item</th>
						<th rowspan="2" class="nowrap">TGL Produksi</th>
						<th colspan='3'  class="text-center">Produksi</th>
						<th class="nowrap" rowspan="2">Total Produksi</th>
						<th class="nowrap" rowspan="2">Pesanan Diambil</th>
						<th class="nowrap" rowspan="2">Total Penjualan</th>
						<th class="nowrap" rowspan="2">Rusak</th>
						<th class="nowrap" rowspan="2">Lain-Lain</th>
						<th class="nowrap" rowspan="2">Sisa Stock</th>
						</tr>
						<tr>
							<th class="nowrap" class="text-align-center">1</th>
							<th class="nowrap" class="text-align-center">2</th>
							<th class="nowrap" class="text-align-center">3</th>
						</tr>
						
						
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($data as $key)
							<tr>
								<td align="center"></td>
								<td align="center">{{ $key->Item->code }}</td>
								<td >{{ $key->Item->nama_item }}</td>
								<td align="center">{{ $key->created_at->format('d/m/Y') }}</td>
								<td align="center">{{ number_format($key->produksi1,'0','','.') }}</td>
								<td align="center">{{ number_format($key->produksi2,'0','','.') }}</td>
								<td align="center">{{ number_format($key->produksi3,'0','','.') }}</td>
								<td align="center">{{ number_format($key->total_produksi,'0','','.') }}</td>
								<td align="center">{{ number_format($key->penjualan_pemesanan,'0','','.') }}</td>
								<td align="center">{{ number_format($key->penjualan_toko,'0','','.') }}</td>
								<td align="center">{{ number_format($key->ket_rusak,'0','','.') }}</td>
								<td align="center">{{ number_format($key->ket_lain,'0','','.') }}</td>
								<td align="center">{{ number_format($key->sisa_stock,'0','','.') }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			
		
			
        </div>

        <script type="text/javascript">
        	$(function(){
        		 $('.datepicker').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true
		        });
        	});
        </script>
    @endcomponent
@endsection