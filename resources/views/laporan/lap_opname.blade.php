@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. Opname', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Penjualan Per Item','link' => '#')
                                                    	) 
                                  ])
        
		
        <div class="card">
        	<form method="POST" action="{{ route('cari_opname') }}">
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
                                    <option value="1" selected>Kode Barang</option>
                                    <option value="2">Nama Menu</option>
                                    <option value="3">Stok Masuk</option>
                                    <option value="4">Stok Akhir</option>
                                    <option value="5">Stok Toko</option>
                                @elseif($input['sort_by'] == '2')
                                    <option value="1" >Kode Barang</option>
                                    <option value="2" selected>Nama Menu</option>
                                    <option value="3">Stok Masuk</option>
                                    <option value="4">Stok Akhir</option>
                                    <option value="5">Stok Toko</option>
                                @elseif($input['sort_by'] == '3')
                                    <option value="1" >Kode Barang</option>
                                    <option value="2">Nama Menu</option>
                                    <option value="3" selected>Stok Masuk</option>
                                    <option value="4">Stok Akhir</option>
                                    <option value="5">Stok Toko</option>
                                @elseif($input['sort_by'] == '4')
                                    <option value="1" >Kode Barang</option>
                                    <option value="2">Nama Menu</option>
                                    <option value="3">Stok Masuk</option>
                                    <option value="4" selected>Stok Akhir</option>
                                    <option value="5">Stok Toko</option>
                                @elseif($input['sort_by'] == '5')
                                    <option value="1" >Kode Barang</option>
                                    <option value="2">Nama Menu</option>
                                    <option value="3">Stok Masuk</option>
                                    <option value="4">Stok Akhir</option>
                                    <option value="5" selected>Stok Toko</option>
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
	        		<a href="{{ route('opname') }}"><label class="btn btn-warning" >Reset</label></a>
	        		<a href="javascript:export_pdf()"><label class="btn btn-success" >Export PDF</label></a>
	        		<a href="{{ route('set_tanggal_produksi') }}" onclick="return confirm('Apakah Anda Yakin Untuk Set Tanggal ?') "><label class="btn btn-danger" >Set Tanggal</label></a>

	        		

	        	</div>
        	</form>
        </div>

        <div class="card" style="margin-top: 10px;">
        	
			@if (session('success'))
			 	@component("components.alert", ["type" => "success"])
					{{ session('success') }}
				@endcomponent
			@endif

			@if (session('error'))
			 	@component("components.alert_error", ["type" => "error"])
					{{ session('error') }}
				@endcomponent
			@endif
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class=" table  table-bordered" id="table_opname">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th>Kode Barang</th>
							<th>Nama Menu</th>
							<th ><center>Stok Masuk</center></th>
							<th ><center>Stok Akhir</center></th>
							<th ><center>Stok Toko</center></th>
							<th ><center>Aksi</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($item as $key)
							<tr>
								<td class="nowrap"></td>
								<td class="nowrap">{{ $key->code }}</td>
								<td class="nowrap">{{ $key->nama_item }}</td>
								<td class="nowrap" align="center">{{ $key->stock_masuk }}</td>
								<td class="nowrap" align="center">{{ $key->stock_akhir }}</td>
								<td class="nowrap" align="center">
										@if($key->stock_toko !== 'belum' )
											{{ $key->stock_toko }}
										@else
											<label class="label label-warning">Belum Diset</label>
										@endif
								</td>
								<td class="nowrap" align="center">
									<button onclick="edit('{{ $key->id }}','{{ $key->nama_item }}',
									'{{ $key->stock_masuk }}','{{$key->stock_akhir}}', '{{$key->stock_toko}}')" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
								</td>
							</tr>
						@endforeach
					</tbody>
					
				</table>
			</div>
			
        </div>

        <script type="text/javascript">
        	$(document).ready(function(){
	 			@if(Session::get('gagal') == 'update' )
	 				$("#modal_edit").modal('show');
				@endif
				@if(Session::get('gagal_modal') == 'simpan' )
					$("#modal_input").modal('show');
				@endif


				$('.datepicker').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true,
		           endDate: '+0d',
		        });

		        var table_opname = $("#table_opname").DataTable({
				    "ordering": false
				});
        		table_opname.on( 'order.dt search.dt', function () {
		            table_opname.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
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
        		window.open('export_opname?tanggal='+kt+'&sort_by='+sort_by+'&opsi_sort='+opsi_sort, '_blank');
        	}

	     	function edit(id,nama_item,stock_masuk,stock,stock_akhir){
	     		var tanggal = $("#mt").val();
        		var pisah = tanggal.split('/');
        		var kt = pisah[2]+"-"+pisah[1]+"-"+pisah[0];

        		$("#id").val(id);
        		$("#tanggal").val(kt);
        		$("#tampil_tanggal").val(tanggal);
        		$("#nama_item").val(nama_item);
        		$("#total_stock_masuk").val(stock_masuk);
        		$("#total_stock_akhir").val(stock);

        		if(stock_akhir == 'belum'){
        			$("#total_stock_toko").val('');
        		}else{
        			$("#total_stock_toko").val(stock_akhir);
        		}
        		
	     		$("#modal_input").modal('show');
	     	}

	     

        </script>
    @endcomponent

    @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Set Stock Akhir"])
		<form method="POST" action="{{ route('post_opname') }}" >
			@csrf
			<input type="text" name="id" id="id" hidden value="{{old('id')}}" readonly>
			<input type="text" name="tanggal" id="tanggal"  value="{{old('tanggal')}}" hidden readonly>
			
			<div class="form-group">
				<label>Tanggal</label>
				<input type="text" id="tampil_tanggal" name="tampil_tanggal" value="{{old('tampil_tanggal')}}" readonly class="form-control">
			</div>

			<div class="form-group">
				<label>Nama Item</label>
				<input type="text" id="nama_item" name="nama_item" value="{{old('nama_item')}}"  readonly class="form-control">
			</div>

			<div class="form-group">
				<label>Total Stock Masuk</label>
				<input type="text" id="total_stock_masuk" name="total_stock_masuk"  value="{{old('total_stock_masuk')}}" readonly class="form-control">
			</div>

			<div class="form-group">
				<label>Total Stock Akhir</label>
				<input type="text" id="total_stock_akhir" name="total_stock_akhir" value="{{old('total_stock_akhir')}}"  readonly class="form-control">
			</div>


			<div class="form-group @error('total_stock_toko') has-error @enderror ">
		        <label>Total Stock Toko</label>
		        <input id="total_stock_toko" type="text" class="form-control " value="{{ old('total_stock_toko') }}" name="total_stock_toko" >
		        @error('total_stock_toko')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	       
	        <div class="text-right">
	        	 <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
	        </div>
		</form>
	@endcomponent

@endsection