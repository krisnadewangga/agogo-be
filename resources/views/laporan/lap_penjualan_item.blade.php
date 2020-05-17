@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Total Penjualan Per Item', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Penjualan Per Item','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('cari_laporan_penjualan_per_item') }}">
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
	        		<a href="{{ route('lap_penjualan_per_item') }}"><label class="btn btn-warning" >Reset</label></a>
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
						<th>Kode Menu</th>
						<th>Nama Menu</th>
						<th ><center>Quantitiy</center></th>
						<th ><center>Total Harga</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($data['data'] as $key)
							<tr>
								<td align="center"></td>
								<td class="nowrap">{{ $key['kode_menu'] }}</td>
								<td class="nowrap">{{ $key['nama_item'] }}</td>
								<td class="nowrap text-center" style="width: 100px;" >{{ $key['qty'] }}</td>
								<td class="nowrap text-left" style="width: 200px;">Rp. {{ $key['tampil_total'] }}</td>
							</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3"></td>
							<td class="text-right"><b>Grand Total :</b></td>
							<td><b>Rp. {{ $data['grandTotal'] }}</b></td>
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
        		var pisah = tanggal.split('/');
        		var kt = pisah[2]+"-"+pisah[1]+"-"+pisah[0];
        		// document.location.href('export_kas');
        		window.open('export_penjualan_per_item?tanggal='+kt, '_blank');
        	}
        </script>
    @endcomponent
@endsection