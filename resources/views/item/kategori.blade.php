@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Kategori', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Kategori','link' => '#')
                                                    	) 
                                  ])
		<div class="card">
			<button class="btn btn-primary" data-toggle="modal" data-target="#modal_input">Create</button>
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
						<th style="width: 100px; text-align: center;">Gambar</th>
						<th style="width: 100px; text-align: center;">Status</th>
						<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($kategori as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->kategori }}</td>
								<td align="center">
									<a href="upload/images-700/{{ $key->gambar }}" target="_blank" title="Lihat Gambar"><button class="btn-warning btn btn-sm" ><i class="fa fa-image"></i></button></a>
								</td>
								<td align="center">
									@if($key['status_aktif'] == 1)
		       							<span class="label label-success ">Aktif</span>
			       					@else
			       						<span class="label label-danger">T.Aktif</span>
			       					@endif
								</td>
								<td align="center">
									<button onclick="edit('{{ $key->id }}','{{ $key->kategori }}','{{ $key->status_aktif }}')" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
			       					<form method="post" action="{{ route('kategori.destroy', $key['id'] ) }}"  style="display: inline">
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

	 <script type="text/javascript">
	  
 		$(document).ready(function(){
 			@if(Session::get('gagal') == 'update' )
 				$("#modal_edit").modal('show');
			@endif
			@if(Session::get('gagal') == 'simpan' )
				$("#modal_input").modal('show');
			@endif
 		});
 	

     	function edit(id,kategori,status_aktif){
     		$("#kategori_id").val(id);
     		$("#kategori_edit").val(kategori);
     		$("#status_aktif").val(status_aktif);
     		$("#modal_edit").modal('show');

     	}
     </script>

     @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Kategori"])
		<form method="POST" action="{{ route('kategori.store') }}" enctype="multipart/form-data">
			@csrf
			<div class="form-group @error('kategori') has-error @enderror ">
		        <label>Kategori</label>
		        <input id="kategori" type="text" class="form-control " value="{{ old('kategori') }}" name="kategori" >
		        @error('kategori')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	        <div class="form-group @error('gambar') has-error @enderror ">
		        <label>Gambar / Icon</label>
		        
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

	@component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Kategori"])
		<form method="POST" action="kategori/edit" enctype="multipart/form-data">
			@csrf

			<input type="hidden" name="_method"  value="PUT">
			<input type="hidden" name="id" readonly id="kategori_id" value="{{ old('id') }}">
			
			<div class="form-group {{ $errors->edit->has('kategori') ? 'has-error' : '' }}">
		        <label >Kategori</label>
		        <input id="kategori_edit" type="text" class="form-control " value="{{ old('kategori') }}" name="kategori" >
		        @if($errors->edit->has('kategori'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('kategori') }} </strong>
                	</label>    
		        @endif 

	        </div>

	        <div class="form-group {{ $errors->edit->has('gambar') ? 'has-error' : '' }} ">
		        <label>Gambar / Icon</label> &nbsp;<label class="label label-warning"><i class="fa fa-warning"></i> Kosongkan Jika Tidak Ingin Mengganti Gambar Icon</label>
		        
		        <input id="gambar_edit" type="file" name="gambar" >
		        @if($errors->edit->has('gambar'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('gambar') }}</strong>
                	</label>    
		        @endif 
	        </div>


	        <div class="form-group">
	        	<label>Status Aktif</label>
	        	<select class="form-control" name="status_aktif" id="status_aktif">
	        		<option value="1">Aktif</option>
	        		<option value="0">T.Aktif</option>
	        	</select>
	        </div>
	        <div class="text-right">
	        	 <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
	        </div>
		</form>
	@endcomponent
@endsection