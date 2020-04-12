@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Kas', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Kas','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('cari_laporan_kas') }}">
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
	        			<a href="{{ route('lap_kas') }}"><label class="btn btn-warning" >Reset</label></a>
	        			<a href="javascript:export_pdf()"><label class="btn btn-success" >Export</label></a>
	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th style="width: 5px;">No</th>
						<th>Kasir</th>
						<th>Waktu</th>
						<th class="nowrap">Saldo Awal</th>
						<th class="nowrap">Total Transaksi</th>
						<th class="nowrap">Total Refund</th>
						<th class="nowrap">Saldo Akhir</th>
						
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($data as $key)
							<tr>
								<td align="center"></td>
								<td  class="nowrap">{{ $key->User->name }}</td>
								<td  class="nowrap">{{ $key->created_at->format('d/m/Y H:i:s') }}</td>
								<td  class="nowrap">Rp. {{ number_format($key->saldo_awal,'0','','.') }}</td>
								<td  class="nowrap">Rp. {{ number_format($key->transaksi,'0','','.') }}</td>
								<td  class="nowrap">Rp. {{ number_format($key->total_refund,'0','','.') }}</td>
								<td  class="nowrap">Rp. {{ number_format($key->saldo_akhir,'0','','.') }}</td>
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

        	function export_pdf()
        	{
        		var tanggal = $("#mt").val();
        		var pisah = tanggal.split('/');
        		var kt = pisah[2]+"-"+pisah[1]+"-"+pisah[0];
        		// document.location.href('export_kas');
        		window.open('export_kas?tanggal='+kt, '_blank');
        	}
        </script>
    @endcomponent
@endsection