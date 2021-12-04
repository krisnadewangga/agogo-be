@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Total Tax Harian Pesanan', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Tax','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('cari_tax_harian_pesanan') }}">
	        	@csrf
	        	<div class="row">
	        		<div class="col-md-3">
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
	        		<div class="col-md-3">
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
	        		<div class="col-md-3">
	        			<div class="form-group">
	        				<label>Sort By</label>
		        			<select name="sort_by" class="form-control" id="sort_by">
		        				@if($input['sort_by'] == '1')
		        					<option value="1" selected>Tanggal Transaksi</option>
		        					<option value="2">Transaksi</option>
			        				<option value="3">Tax</option>
			        				<option value="4">Total Transaksi</option>
			        			@elseif($input['sort_by'] == '2')
		        					<option value="1" >Tanggal Transaksi</option>
		        					<option value="2" selected>Transaksi</option>
			        				<option value="3">Tax</option>
			        				<option value="4">Total Transaksi</option>
		        				@elseif($input['sort_by'] == '3')
		        					<option value="1" >Tanggal Transaksi</option>
		        					<option value="2" >Transaksi</option>
			        				<option value="3" selected>Tax</option>
			        				<option value="4">Total Transaksi</option>
		        				@elseif($input['sort_by'] == '4')
		        					<option value="1" >Tanggal Transaksi</option>
		        					<option value="2" >Transaksi</option>
			        				<option value="3" >Tax</option>
			        				<option value="4" selected>Total Transaksi</option>
		        				@endif
		        			</select>
	        			</div>
	        		</div>
	        		<div class="col-md-3">
	        			 <label>Opsi Sort</label>
	        			 <select name="opsi_sort" class="form-control" id="opsi_sort">
	        			 	@if($input['opsi_sort'] == '1')
	        			 		<option value="1" selected>Kecil Ke Besar</option>
	        					<option value="2" >Besar Ke Kecil</option>
	        			 	@elseif($input['opsi_sort'] == '2')
	        			 		<option value="1" >Kecil Ke Besar</option>
	        					<option value="2" selected >Besar Ke Kecil</option>
	        			 	@endif
	        			 </select>
	        		</div>
	        	</div>
        		<div  style="margin-top: 5px;">
        			<button class="btn btn-primary">Cari</button>
        			<a href="{{ route('lap_tax_harian_pesanan') }}"><label class="btn btn-warning" >Reset</label></a>
        			<a href="javascript:export_pdf()"><label class="btn btn-success" >Export PDF</label></a>
        		</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="table  table-bordered" id="table_ph">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th>Tanggal Transaksi</th>
							
							<th ><center>Transaksi</center></th>
							<th ><center>Tax</center></th>
						</tr>
					</thead>
					<tbody style="font-size:14px;">
						@foreach($data['data'] as $key)
							<tr>
								<td align="center"></td>
								<td>{{ Carbon\Carbon::parse($key->tgl)->format('d/m/Y') }}</td>
								<td>Rp {{ number_format($key->total_transaksi) }}</td>
								<td>Rp {{ number_format($key->total_tax) }}</td>
								
							</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td class="text-right" colspan="2"><b>Grand Total : </b></td>
							<td><b>Rp {{ $data['grandTotal'] }}</b></td>
							<td><b>Rp {{ $data['grandTotalTax'] }}</b></td>
							
						</tr>
					</tfoot>
				</table>
			</div>
		
		
			
        </div>

        <script type="text/javascript">
        	$(function(){
        		 $('.datepicker').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true,
		           endDate: '+0d'
		        });

        		var table_ph = $("#table_ph").DataTable({
				    "ordering": false
				});
        		table_ph.on( 'order.dt search.dt', function () {
		            table_ph.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
		                cell.innerHTML = i+1;
		            } );
		        } ).draw();
        	});

        	function export_pdf()
        	{
        		var tanggal = $("#mt").val();
        		var tanggal1 = $("#st").val();
        		var sort_by = $("#sort_by").val();
                var opsi_sort = $("#opsi_sort").val();
				
				window.open('export_tax_harian_pesanan?mt='+tanggal+'&st='+tanggal1+'&sort_by='+sort_by+'&opsi_sort='+opsi_sort, '_blank');
        	}
        </script>
    @endcomponent
@endsection