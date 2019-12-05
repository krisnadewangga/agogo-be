@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Detail Item', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Item','link' => '../item'),
                                                          array('judul' => $item->nama_item ,'link' => '#')
                                                    	) 
                                  ])
        
        @if(session('success_al_gambar'))
      		@component("components.alert", ["type" => "success"])
				{{ session('success_al_gambar') }}
			@endcomponent
		@endif

		@if(session('success_gu_gambar'))
      		@component("components.alert", ["type" => "success"])
				{{ session('success_gu_gambar') }}
			@endcomponent
		@endif
		

		@if(session('success_del_gambar'))
      		@component("components.alert", ["type" => "success"])
				{{ session('success_del_gambar') }}
			@endcomponent
		@endif


		@if(session('success_detail'))
      		@component("components.alert", ["type" => "success"])
				{{ session('success_detail') }}
			@endcomponent
		@endif

		@if(session('success_del_stock'))
      		@component("components.alert", ["type" => "success"])
				{{ session('success_del_stock') }}
			@endcomponent
		@endif

		@if(session('success_up_stock'))
      		@component("components.alert", ["type" => "success"])
				{{ session('success_up_stock') }}
			@endcomponent
		@endif
		
		
    	<div class="card">
	    	<div class="row" >
	    		
	    		<div class="col-md-12">
					<div class="nav-tabs-custom">
			            <ul class="nav nav-tabs">
			              <li id="tab1" class="tab active"><a href="#tab_1" data-toggle="tab">Tentang</a></li>
			              <li id="tab2" class="tab"><a href="#tab_2" data-toggle="tab">Galeri</a></li>
			              <li id="tab3" class="tab"><a href="#tab_3" data-toggle="tab">Stock&nbsp; <sup class="label label-success">{{ $item->stock }}</sup></a></li>
			            
			            </ul>
			            <div class="tab-content">
			              <div class="tab-pane active" id="tab_1">
			                
			                <div style="padding:0 20px 0 20px;">
			                	<form method="POST" action="{{ route('item.update', $item->id ) }}">
			                		@csrf
			                		<input type="hidden" name="_method" value="PUT">
			                		
				                	
				                	<div class="form-group">
										<label>Kategori</label>
										<input type="text" value="{{$item->Kategori->kategori}}" class="form-control" disabled>
									</div>

					                <div class="form-group @error('nama_item') has-error @enderror">
										<label>Nama Item</label>
										<input type="text" name="nama_item" value="{{ $item->nama_item }}" class="form-control" placeholder="Masukan Nama Item">
										@error('nama_item')
								            <label class="control-label" for="inputError">
						                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
						                	</label>    
								        @enderror 
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group @error('harga') has-error @enderror">
												<label>Harga</label>
												<input type="text" name="harga" value="{{ $item->harga }}" class="form-control" placeholder="Masukan Harga Jual">
												@error('harga')
										            <label class="control-label" for="inputError">
								                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
								                	</label>    
										        @enderror 
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group @error('margin') has-error @enderror">
												<label>Margin</label>
												<input type="text" name="margin" value="{{ $item->margin }}" class="form-control" placeholder="Masukan Margin Keuntunga Dari Harga Jual">
												@error('margin')
										            <label class="control-label" for="inputError">
								                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
								                	</label>    
										        @enderror 
											</div>
										</div>
									</div>
<!-- 
									<div style="border:1px solid #CCC; padding:20px 20px 10px 20px; margin-bottom:10px; margin-top: 10px;" >
										<div class="row">
											<div class="col-md-12">
												<div>
													<label>Varian Item</label>
												</div>
												<div style="margin-top: -5px;"> 
													Tambahkan Varian Rasa Apabila Item Memiliki Lebih Dari Satu Rasa	
												</div>
											
											</div>
											
										</div>
										
										<div id="form-varian" style="margin-top: 5px;">
											<table class="table" style="margin-bottom: 0px;">
												<tr>
													<td>Varian Rasa</td>
													<td>
														<div class="form-group @error('v_rasa') has-error @enderror">
															<textarea class="form-control" name="v_rasa">
																{{ $item->v_rasa }}
															</textarea>
															*) Gunakan Koma Untuk Pemisah Setiap Rasa, Kosongkan Apabila Tidak Ingin Memasukan Varian Rasa
															@error('v_rasa')
													            <label class="control-label" for="inputError">
											                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
											                	</label>    
													        @enderror 
														</div>
													</td>
												</tr>
												
											</table>
										</div>
									</div> -->
									
									<div class="form-group @error('deskripsi') has-error @enderror" >
										<label>Deskripsi Item</label>
										<textarea name="deskripsi" class="form-control" style="height:75px;">
											{{ $item->deskripsi }}
										</textarea>
										@error('deskripsi')
								            <label class="control-label" for="inputError">
						                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
						                	</label>    
								        @enderror 
									</div>

									<button class="btn btn-primary">Simpan</button>
			                	</form>
			                	
							</div>
			             
			              </div>
			              <!-- /.tab-pane -->

			              <div class="tab-pane" id="tab_2">
			              		<table class="table-galeri" style="margin-top: 10px;">
			              			<tr>
			              				<td rowspan="4">
			              					<img src="{{ asset('upload/images-400/'.$gambarUtama->gambar ) }}" height="100">
			              				</td>
			              			</tr>
			              			<tr>
			              				<td>Item</td>
			              				<td>:</td>
			              				<td>{{ $item->nama_item }}</td>
			              			</tr>
			              			<tr>
			              				<td>Kategori</td>
			              				<td>:</td>
			              				<td>{{ $item->Kategori->kategori }}</td>
			              			</tr>
			              			
			              			<tr>
			              				<td colspan="3">
			              					<button class="btn btn-primary btn-sm " data-target="#modal_input_gambar" data-toggle="modal" style="width: 100%;">Tambah Gambar</button>
			              				</td>
			              			</tr>
			              		</table>

			              		<hr style="margin:20px 0 20px 0"></hr>
			              		
			              		<div class="table-responsive"> 
			              			
			              			<table class="list-gambar" style="margin-top: 0px; margin-bottom: 15px;" > 
			              				<tr>
			              					@foreach($listGambarItem as $keyGambar)
			              						<td>
			              							<div>
				                						<img src="{{ asset('upload/images-400/'.$keyGambar->gambar ) }}" height="150">
				                					</div>
				                					<div style="margin-top: 5px;">
					                					<div class="row">
					                						<div class="col-md-6 col-xs-6" style="padding-right: 0px;">

					                							<a href="{{ $keyGambar->utama == '1' ? '#' : '../ganti_gambar_utama/'.$keyGambar->id }}">
					                								<button class="btn btn-sm btn-flat 
					                										 	   {{ $keyGambar->utama == '1' ? 'btn-primary' : 'bg-info' }}
					                											   "
					                									    style="width: 100%" ><i class='fa fa-star'></i></button>
					                							</a>

					                						</div>
					                						<div class="col-md-6 col-xs-6" style="padding-left: 0px;">
					                							<form method="post" action="{{ route('hapus_gambar_item', $keyGambar['id'] ) }}" style="display: inline">
										       						{{ csrf_field() }}
										       						<input type="hidden" name="_method" value="delete" />
										       						<button onclick="return confirm('apa anda yakin ?')" class=' btn btn-danger btn-sm btn-flat' style="width: 100%" ><i class='fa fa-trash'></i></button>
										       					</form>
					                							
					                						</div>
					                					</div>
					                				</div>
			              						</td>
			              					@endforeach
				                			

				                		</tr>
				                	</table>
			              		
			              		</div>
			                	

			              </div>
			              <!-- /.tab-pane -->
			              <div class="tab-pane" id="tab_3">
				              	<table class="table-galeri"  style="margin-top: 10px;">
			              			<tr>
			              				<td rowspan="4">
			              					<img src="{{ asset('upload/images-400/'.$gambarUtama->gambar ) }}" height="100">
			              				</td>
			              			</tr>
			              			<tr>
			              				<td>Item</td>
			              				<td>:</td>
			              				<td>{{ $item->nama_item }}</td>
			              			</tr>
			              			<tr>
			              				<td>Kategori</td>
			              				<td>:</td>
			              				<td>{{ $item->Kategori->kategori }}</td>
			              			</tr>
			              		
			              			<tr>
			              				<td colspan="3">
			              					<button class="btn btn-primary btn-sm " data-target="#modal_tambah_stock" data-toggle="modal" style="width: 100%;">Tambah Stock</button>
			              				</td>
			              			</tr>
			              		</table>

			              		<hr style="margin:20px 0 20px 0"></hr>
			               	 	<table class="dataTables table  table-bordered">
									<thead style=" font-size:14px;">
										<tr>
										<th style="width: 5px;">No</th>
										<th>Tanggal</th>
										<th>Jumlah</th>
										<th>InputBy</th>
										@if(Auth::User()->level_id == '1')
											<th style="width: 100px; text-align: center;">Aksi</th>
										@endif
										</tr>
									</thead>
									<tbody style=" font-size:14px;">
										@foreach($stocker as $keyStocker)
											<tr>
												<td align="center"></td>
												<td>{{ $keyStocker->created_at->format("d M Y H:i A") }}</td>
												<td>{{ $keyStocker->jumlah }} PCS</td>
												<td>{{ $keyStocker->input_by }} </td>
												@if(Auth::User()->level_id == '1')
													<td align="center">
														<form method="post" action="{{ route('hapus_stock', $keyStocker['id'] ) }}"  style="display: inline">
								       						{{ csrf_field() }}
								       						<input type="hidden" name="_method" value="delete" />
								       						<button onclick="return confirm('apa anda yakin ?')" class=' btn btn-danger btn-sm'><i class='fa fa-trash'  ></i></button>
								       					</form>
													</td>
												@endif
											</tr>
										@endforeach
									</tbody>
								</table>
			              </div>
			              <!-- /.tab-pane -->
			            </div>
			            <!-- /.tab-content -->
			        </div>
			        <!-- nav-tabs-custom -->
	    			
	    		</div>
	    	</div>
    	</div>


    	<script type="text/javascript">
			$(function (){
				CKEDITOR.replace('deskripsi');

				@if(Session::get('gagal') == 'gambar' )
 					$("#modal_input_gambar").modal('show');
 					$(".tab").prop('class','tab');
 					$(".tab-pane").prop('class','tab-pane');

 					$("#tab2").prop('class','tab active');
 					$("#tab_2").prop('class','tab-pane active');
				@endif
				

				@if(Session::get('success_tab') == 'gambar' )
 					$(".tab").prop('class','tab');
 					$(".tab-pane").prop('class','tab-pane');

 					$("#tab2").prop('class','tab active');
 					$("#tab_2").prop('class','tab-pane active');
				@endif


				@if(Session::get('gagal') == 'stock' )
 					$("#modal_tambah_stock").modal('show');
 					$(".tab").prop('class','tab');
 					$(".tab-pane").prop('class','tab-pane');

 					$("#tab3").prop('class','tab active');
 					$("#tab_3").prop('class','tab-pane active');
				@endif

				@if(Session::get('success_tab') == 'stock' )
 					$(".tab").prop('class','tab');
 					$(".tab-pane").prop('class','tab-pane');

 					$("#tab3").prop('class','tab active');
 					$("#tab_3").prop('class','tab-pane active');
				@endif
			});	
			
		</script>
    @endcomponent

    @component("components.modal", ["id" => "modal_input_gambar" ,"kop_modal" => "Form Input Gambar"])

		<form method="POST" action="{{ route('store_gambarItem') }}" enctype="multipart/form-data">
			@csrf
			<input type="hidden" name="item_id" value="{{ $item->id }}">
	        <div class="form-group @error('gambar') has-error @enderror ">
		        <label>Gambar</label>
		        <input id="gambar" type="file"  name="gambar" >
		        @error('gambar')
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

	@component("components.modal", ["id" => "modal_tambah_stock" ,"kop_modal" => "Form Tambah Stock"])
		<form method="POST" action="{{ route('input_stock', $item->id) }}" >
			@csrf
	        <div class="form-group @error('jumlah') has-error @enderror ">
		        <label>Jumlah</label>
		        <input id="jumlah" type="text"  name="jumlah" class="form-control" placeholder="Masukan Jumlah Stock Yang Ingin Anda Tambahkan" >
		        @error('jumlah')
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