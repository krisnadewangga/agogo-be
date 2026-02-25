@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Produksi', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Produksi','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<form method="POST" action="{{ route('cari_laporan_produksi') }}">
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
                                 	<option value="1" selected>Kode Item</option>
                                    <option value="2" >Nama Item</option>
                                    <option value="6" >Total Produksi</option>
                                    <option value="7" >Catatan</option>
                                @elseif($input['sort_by'] == '2')
                                 	<option value="1" >Kode Item</option>
                                    <option value="2" selected>Nama Item</option>
                                    <option value="6" >Total Produksi</option>
                                    <option value="7" >Catatan</option>
                                @elseif($input['sort_by'] == '6')
                                 	<option value="1" >Kode Item</option>
                                    <option value="2">Nama Item</option>
                                    <option value="6" selected>Total Produksi</option>
                                    <option value="7">Catatan</option>
								@elseif($input['sort_by'] == '7')
                                 	<option value="1" >Kode Item</option>
                                    <option value="2">Nama Item</option>
                                    <option value="6" >Total Produksi</option>
                                    <option value="7" selected>Catatan</option>
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
	        		<a href="{{ route('lap_pergerakan_stock') }}"><label class="btn btn-warning" >Reset</label></a>
	        		<a href="javascript:export_pdf()"><label class="btn btn-success" >Export PDF</label></a>
	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class=" table  table-bordered" id="table_pergerakan_stock">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th class="nowrap">Kode Item</th>
							<th class="nowrap">Nama Item</th>
							<th class="text-center">Total Produksi</th>
							<th class="nowrap" >Catatan</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($data as $key)
							<tr>
								<td align="center"></td>
								<td align="center">{{ $key->Item->code }}</td>
								<td >{{ $key->Item->nama_item }}</td>
								<td align="center">{{ number_format($key->produksi1 + $key->produksi2 + $key->produksi3,'0','','.') }}</td>
								<td >{{ $key->catatan }}</td>
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
		           autoclose: true,
		           endDate: '+0d'
		        });

        		var table_pergerakan_stock = $("#table_pergerakan_stock").DataTable({
				    "ordering": false
				});
        		table_pergerakan_stock.on( 'order.dt search.dt', function () {
		            table_pergerakan_stock.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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
        		window.open('export_produksi?tanggal='+kt+'&sort_by='+sort_by+'&opsi_sort='+opsi_sort, '_blank');
        	}
        </script>
    @endcomponent
@endsection