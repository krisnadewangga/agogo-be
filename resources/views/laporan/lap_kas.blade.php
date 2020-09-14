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
	        		<div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sort By</label>
                            <select name="sort_by" class="form-control" id="sort_by">
                                @if($input['sort_by'] == '1')
                                    <option value="1" selected>Kasir</option>
                                    <option value="2">Waktu</option>
                                    <option value="3">Saldo Awal</option>
                                    <option value="4">Total Transaksi</option>
                                    <option value="5">Total Refund</option>
                                    <option value="6">Saldo Akhir</option>
                                @elseif($input['sort_by'] == '2')
                                    <option value="1" >Kasir</option>
                                    <option value="2" selected>Waktu</option>
                                    <option value="3">Saldo Awal</option>
                                    <option value="4">Total Transaksi</option>
                                    <option value="5">Total Refund</option>
                                    <option value="6">Saldo Akhir</option>
                                @elseif($input['sort_by'] == '3')
                                    <option value="1" >Kasir</option>
                                    <option value="2" >Waktu</option>
                                    <option value="3" selected>Saldo Awal</option>
                                    <option value="4">Total Transaksi</option>
                                    <option value="5">Total Refund</option>
                                    <option value="6">Saldo Akhir</option>
                                @elseif($input['sort_by'] == '4')
                                    <option value="1" >Kasir</option>
                                    <option value="2" >Waktu</option>
                                    <option value="3" >Saldo Awal</option>
                                    <option value="4" selected>Total Transaksi</option>
                                    <option value="5">Total Refund</option>
                                    <option value="6">Saldo Akhir</option>
                                @elseif($input['sort_by'] == '5')
                                    <option value="1" >Kasir</option>
                                    <option value="2" >Waktu</option>
                                    <option value="3" >Saldo Awal</option>
                                    <option value="4" >Total Transaksi</option>
                                    <option value="5" selected>Total Refund</option>
                                    <option value="6">Saldo Akhir</option>
                                @elseif($input['sort_by'] == '6')
                                    <option value="1" >Kasir</option>
                                    <option value="2" >Waktu</option>
                                    <option value="3" >Saldo Awal</option>
                                    <option value="4" >Total Transaksi</option>
                                    <option value="5" >Total Refund</option>
                                    <option value="6" selected>Saldo Akhir</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
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
	        	<div style="margin-top: 5px;">
	        			<button class="btn btn-primary">Cari</button>
	        			<a href="{{ route('lap_kas') }}"><label class="btn btn-warning" >Reset</label></a>
	        			<a href="javascript:export_pdf()"><label class="btn btn-success" >Export PDF</label></a>
	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="table  table-bordered" id="table_kas">
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

        <div class="card" >
        	<div class="row">
        		<div class="col-md-4">
        			<table id="example2" class="table table-bordered table-hover dataTable" >
                <thead>
                    <tr>
                        <th>Penerimaan Uang</th>
                        <th style="text-align: Center;">Jumlah</th>
                        
                    </tr>
                </thead>
                <tbody>
                   
                    <tr>
                        <td>100.000</td>      
                        <td style="text-align: right;"></td>                                                                                        
                    </tr>
                    <tr>
                        <td>50.000</td>      
                        <td style="text-align: right;"></td>                                                                                                                                                                                                                                                                                                                                                                                                                                     
                    </tr>
                    <tr>
                        <td>20.000</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>10.000</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>5000</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>2000</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>1000</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>500</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>200</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>100</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>10</td>      
                        <td style="text-align: right;"></td>                                                                                            
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Kas Selisih </th>      
                        <th style="text-align: right;"></th>                                                                                                                                                                                                                                                                                                                                             
                    </tr>
                    
                </tfoot>
            		</table>
            	</div>
        	</div>
        </div>

        <script type="text/javascript">
        	$(function(){
        		 $('.datepicker').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true,
                   endDate: '+0d'
		        });

                var table_kas = $("#table_kas").DataTable({
                    "ordering": false
                });
                table_kas.on( 'order.dt search.dt', function () {
                    table_kas.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();

        	});

        	function export_pdf()
        	{
        		var tanggal = $("#mt").val();
        		var pisah = tanggal.split('/');
        		var kt = pisah[2]+"-"+pisah[1]+"-"+pisah[0];
                var sort_by = $("#sort_by").val();
                var opsi_sort = $("#opsi_sort").val();

        		// document.location.href('export_kas');
        		window.open('export_kas?tanggal='+kt+'&sort_by='+sort_by+'&opsi_sort='+opsi_sort, '_blank');
        	}
        </script>
    @endcomponent
@endsection