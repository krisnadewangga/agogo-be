@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Total Pendapatan Harian', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Pendapatan','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('cari_pendapatan_harian') }}">
	        	@csrf
	        	<div class="row">
	        		<div class="col-md-6">
	        			<div class="form-group">
			                <label>Mulai Tanggal</label>

			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" id="mt" class="form-control pull-right datepicker" name="mt" autocomplete="off" value="{{ $input['mt'] }}">
			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        		<div class="col-md-6">
	        			<div class="form-group">
			                <label>Sampai Tanggal</label>

			                <div class="input-group date">
			                  <div class="input-group-addon">
			                    <i class="fa fa-calendar"></i>
			                  </div>
			                  <input type="text" class="form-control pull-right datepicker" id="st" name="st" autocomplete="off" value="{{ $input['st'] }}" >
			                </div>
			                <!-- /.input group -->
	              		</div>
	        		</div>
	        	
	        	</div>
        		<div  style="margin-top: 5px;">
        			<button class="btn btn-primary">Cari</button>
        			<a href="{{ route('lap_pendapatan_harian') }}"><label class="btn btn-warning" >Reset</label></a>
        			<a href="javascript:export_pdf()"><label class="btn btn-success" >Export PDF</label></a>
        		</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th>Tanggal Transaksi</th>
							<th ><center>Transaksi</center></th>
							<th ><center>Diskon</center></th>
							<th ><center>Total Transaksi</center></th>
						</tr>
					</thead>
					<tbody style="font-size:14px;">
						@foreach($data['data'] as $key)
							<tr>
								<td align="center"></td>
								<td>{{ Carbon\Carbon::parse($key->tgl)->format('d/m/Y') }}</td>
								<td>Rp {{ number_format($key->transaksi) }}</td>
								<td>Rp {{ number_format($key->total_diskon) }}</td>
								<td>Rp {{ number_format($key->total_transaksi) }}</td>
							</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td class="text-right" colspan="2"><b>Grand Total : </b></td>
							<td><b>Rp {{ $data['grandTotalTransaksi'] }}</b></td>
							<td><b>Rp {{ $data['grandTotalDiskon'] }}</b></td>
							<td><b>Rp {{ $data['grandTotal'] }}</b></td>
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
        	});

        	function export_pdf()
        	{
        		var tanggal = $("#mt").val();
        		var tanggal1 = $("#st").val();
				window.open('export_pendapatan_harian?mt='+tanggal+'&st='+tanggal1, '_blank');
        	}
        </script>
    @endcomponent
@endsection