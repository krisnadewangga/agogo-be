@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Item', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Item','link' => '#')
                                                    	) 
                                  ])
    	<div class="card">
			<!-- <button class="btn btn-primary" data-target="#modal_input" data-toggle="modal">Create</button> -->
			<a href="{{ route('item.create') }}"><button class="btn btn-primary" >Create</button></a>
			<hr></hr>
			@if (session('success'))
			 	@component("components.alert", ["type" => "success"])
					{{ session('success') }}
				@endcomponent
			@endif
		
			<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th style="width: 5px;">No</th>
						<th>Kategori</th>
						<th>Item</th>
						<th>Deskripsi</th>
						<th>Harga Jual</th>
						<th>stock</th>
						<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($item as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->Kategori->kategori }}</td>
								<td>{{ $key->nama_item }}</td>
								<td>{!! $key->deskripsi !!}</td>
								<td>Rp. {{ number_format($key->harga,'0','','.') }}</td>
								<td>{{ $key->stock }}</td>
								<td align="center">
									<a href="item/{{$key->id}}"><button  class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button></a>
			       					<form method="post" action="{{ route('item.destroy', $key['id'] ) }}"  style="display: inline">
			       						{{ csrf_field() }}
			       						<input type="hidden" name="_method" value="delete" />
			       						<button onclick="return confirm('apa anda yakin ?')" class=' btn btn-danger btn-sm'><i class='fa fa-trash'  ></i></button>
			       					</form>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>                           
    @endcomponent

    @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Item"])
		<form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data">
			@csrf
			<div class="form-group @error('kategori') has-error @enderror ">
		        <label>Nama</label>
		        <input id="nama" type="text" class="form-control" value="{{ old('nama') }}" name="nama" >
		        @error('nama')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	        <div class="form-group @error('kategori') has-error @enderror ">
		        <label>Kategori</label>
		        <select class="form-control" id="kategori" name="kategori">
		        	<option value="">-- Pilih Kategori --</option>
		        </select>

		        @error('kategori')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>


	        <div class="form-group @error('gambar') has-error @enderror ">
		        <label>Gambar Utama</label>
		        
		        <input id="gambar" type="file"  name="gambar" >
		        @error('gambar')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>


	        <div style="border:1px solid #CCC; padding:10px; margin-bottom:10px;" >
									<div class="row">
										<div class="col-md-12">
											<div>
												<label>Varian Item</label>
											</div>
											<div style="margin-top: -5px;"> 
												Tambahkan Varian Item Untuk Ukuran Dan Warna	
											</div>
										
										</div>
										<!-- <div class="col-md-3" style="text-align: center; padding-top:5px;">
											<button class="btn btn-sm btn-primary" style="width: 100%;">Tambahkan Varian</button>
										</div> -->
									</div>
									
									<div id="form-varian" style="margin-top: 5px;">
										<table class="table">
											<tr>
												<td>Ukuran</td>
												<td>
													<div class="form-group">
														<textarea class="form-control" name="ukuran">
														</textarea>
														*) Gunakan Koma Untuk Pemisah Setiap Ukuran, Kosongkan Apabila Tidak Ingin Memasukan Ukuran
													</div>
												</td>

											</tr>
											<tr>
												<td>Pilihan Warna</td>
												<td>
													<div class="form-group">
														<textarea class="form-control" name="warna">
														</textarea>
														*) Gunakan Koma Untuk Pemisah Setiap Warna, Kosongkan Apabila Tidak Ingin Memasukan Warna
													</div>
												</td>

											</tr>
										</table>
									</div>
								</div>

	        <div class="text-right">
	        	 <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
	        </div>
		</form>
	@endcomponent
@endsection