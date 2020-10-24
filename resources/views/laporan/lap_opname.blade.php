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
			<form method="POST" action="{{ route('post_opname') }}" >
				@csrf
				<input type="text" name="tanggal" value="{{ $tanggal_form }}" hidden>
	        	<div class="table-responsive" style="margin-top: 10px;">
					<table class=" table  table-bordered" >
						<thead style=" font-size:14px;">
							<tr>
								<th style="width: 5px;">No</th>
								<th>Kode Barang</th>
								<th>Nama Menu</th>
								<th ><center>Stok Masuk</center></th>
								<th ><center>Stok Akhir</center></th>
								<th ><center>Stok Toko</center></th>
							</tr>
						</thead>
						<tbody style=" font-size:14px;">
							@php $no=1; @endphp
							@foreach($item as $key)
								<tr>
									<td class="nowrap" align="center">{{ $no++ }}</td>
									<td class="nowrap">{{ $key->code }}</td>
									<td class="nowrap">{{ $key->nama_item }}</td>
									<td class="nowrap" align="center">
										{{ $key->stock_masuk }}
										<input type="hidden" name="total_stock_masuk_{{$key->id}}" value="{{ $key->stock_masuk }}" >
									</td>
									<td class="nowrap" align="center">
										{{ $key->stock_akhir }}
										<input type="hidden" name="total_stock_akhir_{{$key->id}}" value="{{ $key->stock_akhir }}" >
									</td>
									<td class="nowrap" align="center">	
										
										<input type="numeric" name="total_stock_toko_{{$key->id}}" class="form-control" value="@if(session('error_auth')) {{ old('total_stock_toko_'.$key->id) }} @else {{$key->stock_toko}} @endif" style="width: 80px;">
										
									</td>
									
								</tr>
							@endforeach
						</tbody>
						
					</table>
					<label class="btn btn-success" onclick="aproval()">Simpan</label>

					@component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Aproval Simpan Opname"])
						@if (session('error_auth'))
						 	@component("components.alert_error", ["type" => "error"])
								{{ session('error_auth') }}
							@endcomponent
						@endif
						<div class="form-group">
							<label>Username</label>
							<input type="text" id="username" name="username"   class="form-control">
						</div>

						<div class="form-group">
							<label>Password</label>
							<input type="password" id="username" name="password"   class="form-control">
						</div>
				       
				        <div class="text-right">
				        	 <button type="submit" class="btn btn-success btn-sm">Proses</button>
				        </div>
					@endcomponent

				</form>
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

        	function aproval()
        	{
        		$("#modal_input").modal('show');
        	}


        </script>
    @endcomponent

  

@endsection