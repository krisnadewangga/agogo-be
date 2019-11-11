@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Admin', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Admin','link' => '#')
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
						<th>Name</th>
						<th>Email</th>
						<th style="width: 100px; text-align: center;">Status</th>
						<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($administrator as $key)
							<tr>
								<td></td>
								<td>{{ $key->name }}</td>
								<td>{{ $key->email }}</td>
								<td align="center">
									@if($key['status_aktif'] == 1)
		       							<span class="label label-success ">Aktif</span>
			       					@else
			       						<span class="label label-danger">T.Aktif</span>
			       					@endif
								</td>
								<td align="center">
									<button onclick="edit('{{ $key->id }}','{{ $key->name }}','{{ $key->email }}', '{{ $key->status_aktif }}')" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
			       					<form method="post" action="{{ route('administrator.destroy', $key['id'] ) }}"  style="display: inline">
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
 	

     	function edit(id,name,email,status_aktif){
     		$("#user_id").val(id);
     		$("#nama_edit").val(name);
     		$("#email_edit").val(email);
     		$("#status_aktif").val(status_aktif);
     		$("#modal_edit").modal('show');

     	}
     </script>

     @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Administrator"])
		<form method="POST" action="{{ route('administrator.store') }}">
			@csrf
			
			<div class="form-group @error('name') has-error @enderror ">
		        <label>Nama</label>
		        <input id="level" type="text" class="form-control " value="{{ old('name') }}" name="name" autofocus >
		        @error('name')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	        <div class="form-group @error('email') has-error @enderror ">
		        <label>Email</label>
		        <input id="email" type="email" class="form-control " value="{{ old('email') }}" name="email" >
		        @error('email')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	         <div class="form-group @error('password') has-error @enderror ">
		        <label>Password</label>
		        <input id="password" type="password" class="form-control " value="{{ old('password') }}" name="password" >
		        @error('password')
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

  	@component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Administrator"])
		<form method="POST" action="administrator/edit">
			@csrf

			<input type="hidden" name="_method"  value="PUT">
			<input type="hidden" name="id" readonly id="user_id" value="{{ old('id') }}">
			
			<div class="form-group {{ $errors->edit->has('name') ? 'has-error' : '' }}">
		        <label >Nama</label>
		        <input id="nama_edit" type="text" class="form-control " value="{{ old('name') }}" name="name" autocomplete >
		        @if($errors->edit->has('name'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('name') }} </strong>
                	</label>    
		        @endif 
	        </div>

	        <div class="form-group {{ $errors->edit->has('email') ? 'has-error' : '' }}">
		        <label >Email</label>
		        <input id="email_edit" type="email" class="form-control " value="{{ old('email') }}" name="email" >
		        @if($errors->edit->has('email'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('email') }} </strong>
                	</label>    
		        @endif 
	        </div>

	        <div class="form-group {{ $errors->edit->has('password') ? 'has-error' : '' }}">
		        <label >Password</label>&nbsp;<label class="label label-warning"><i class="fa fa-warning"></i> Kosongkan Jika Tidak Ingin Mengganti Password</label>
		        <input id="password_edit" type="password" class="form-control " value="{{ old('password') }}" name="password" >
		        @if($errors->edit->has('password'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('password') }} </strong>
                	</label>    
		        @endif 
	        </div>

	        <div class="form-group">
	        	<label >Status Aktif</label>
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