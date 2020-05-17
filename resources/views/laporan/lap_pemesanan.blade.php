@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Pemesanan', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Pemesanan','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('filter_laporan_pemesanan') }}">
	        	@csrf
	        	<div class="row">
	        		<div class="col-md-6">
	        			<div class="form-group  @error('mulai_tanggal') has-error @enderror">
			                <label>Mulai Tanggal</label>
			                   @error('mulai_tanggal')
						            <label class="control-label" for="inputError">
				                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
				                	</label>    
						      @enderror 
			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" id="mt" class="form-control pull-right datepicker" name="mulai_tanggal" autocomplete="off" value="{{ $input['mulai_tanggal'] }}">
			               
			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        		<div class="col-md-6">
	        			<div class="form-group  @error('sampai_tanggal') has-error @enderror">
			                <label>Sampai Tanggal</label>
			                 @error('sampai_tanggal')
						            <label class="control-label" for="inputError">
				                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
				                	</label>    
						     @enderror 
			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" class="form-control pull-right datepicker" id="st" name="sampai_tanggal" autocomplete="off" value="{{ $input['sampai_tanggal'] }}" >

			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        		
	        	</div>
	        	<div style="margin-top: 5px;">
	        		<button class="btn btn-primary">Cari</button>
        			<a href="{{ route('lap_pemesanan') }}"><label class="btn btn-warning" >Reset</label></a>
        			<a href="javascript:export_pdf()"><label class="btn btn-success" >Export PDF</label></a>
	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	<div class="text-center">
        	
        	</div>
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="table  table-bordered" id="dataTables-preorder">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th class="nowrap">Preoreder ID</th>
							<th class="nowrap">Nama Pelanggan</th>
							<th class="nowrap">Tanggal Pesan</th>
							<th class="nowrap">Tanggal Selesai</th>
							<th class="nowrap">Jam Selesai</th>
							<th class="nowrap">Status Order</th>
							<th class="nowrap">Metode Pembayaran</th>
							<th class="nowrap">Pencatat</th>
							<th class="nowrap">Total Harga</th>
							<th class="nowrap">DP</th>
							<th class="nowrap">Sisa</th>
						
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@php $no=1; @endphp
						@forelse($result['data'] as $key)
							<tr>
								<td align="center">{{ $no++ }}</td>
								<td class="nowrap">{{$key->no_transaksi}}</td>
								<td class="nowrap">{{ $key->nama }}</td>
								<td class="nowrap">{{ $key->tgl_pesan }}</td>
								<td class="nowrap">{{ $key->tgl_selesai}}</td>
								<td class="nowrap">{{ $key->jam}}</td>
								<td class="nowrap">
									
									@if($key->status == "5")
										@if($key->metode_pembayaran == "1" || $key->metode_pembayaran == "2")
											Sudah Diterima
										@else
											Sudah Diambil
										@endif
									@else
									    @if($key->metode_pembayaran == "1" || $key->metode_pembayaran == "2")
											Belum Diterima
										@else
											Belum Diambil
										@endif
									@endif
									
								</td>
								<td class="nowrap">
									@if($key->jenis == "2")
										Pemesanan
									@else
										@if($key->metode_pembayaran == "1")
											TopUp
										@elseif($key->metode_pembayaran == "2")
											Bank Transfer
										@else
											Bayar Ditoko
										@endif
									@endif
								</td>
								<td class="nowrap">
									{{ $key->pencatat }}
								</td>
								<td class="nowrap">Rp. {{ number_format($key->total,'0','','.') }}</td>
								<td class="nowrap">Rp. {{ number_format($key->uang_muka,'0','','.') }}</td>
								<td class="nowrap">Rp. {{ number_format($key->sisa_bayar,'0','','.') }}</td>
							</tr>
						@empty
							<tr>
                                <td class="text-center" colspan="11">Tidak ada data transaksi Hari ini</td>
                            </tr>
						@endforelse
					</tbody>
					<tfoot>
						 <tr>
                            <th colspan="8" style="text-align:right"></th>                
                            <th class="nowrap" style="text-align:right">Grand Total :</th>
                            <th class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->grand_total_th }} </th>
                            <th class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->grand_total_dp }} </th>
                            <th  class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->grand_total_sisa }} </th>

                        </tr>
                        <tr>
                                <th colspan="8" style="text-align:right"></th>
                                <th  class="nowrap" style="text-align:right">Pembatalan Transaksi : </th>
                                <th  class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->pembatalan_transaksi_th }}</th>
                                <th  class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->pembatalan_transaksi_dp }}</th>
                                <th class="nowrap" style="text-align:right"></th>
                            </tr>
                            <tr>
                                    <th colspan="8" style="text-align:right"></th>
                                    <th class="nowrap" style="text-align:right">Total Transaksi : </th>
                                    <th class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->total_transaksi_th }}</th>
                                    <th class="nowrap" style="text-align:left">Rp. {{ $result['tfoot']->total_transaksi_dp }}</th>
                                    <th class="nowrap" style="text-align:right"></th>
                                </tr>
					</tfoot>
				</table>
			</div>
		
		
			
        </div>

        <script type="text/javascript">
        	$(function(){
        		 $('.datepicker').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true
		        });

        		 $("#dataTables-preorder").DataTable({
        		 	 "paging":   false,
			        "ordering": false,
			        "info":     false
        		 });
        	});

        	function export_pdf()
        	{
        		var tanggal = $("#mt").val();
        		var tanggal1 = $("#st").val();

        		if(tanggal != "" || tanggal1 != ""){
        			var pisah = tanggal.split('/');
	        		var pisah1 = tanggal1.split('/');

	        		var mt = pisah[2]+"-"+pisah[1]+"-"+pisah[0];
	        		var st = pisah1[2]+"-"+pisah1[1]+"-"+pisah1[0];
	        		// document.location.href('export_kas');
	        		window.open('export_pemesanan?mulai_tanggal='+mt+'&sampai_tanggal='+st, '_blank');
        		}else{
        			alert("Maaf! Pastikan Mulai Tanggal & Sampat Tanggal Tidak Kosong");
        		}
        		
        	}
        </script>
    @endcomponent
@endsection