@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Level', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Level','link' => 'google.com')
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
						<th>Level</th>
						<th style="width: 100px; text-align: center;">Status</th>
						<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($level as $key)
						<tr>
							<td style="text-align: center;"></td>
							<td>{{ $key->level }}</td>
							<td align='center'>
		       					@if($key['status_aktif'] == 1)
		       						<span class="label label-success ">Aktif</span>
		       					@else
		       						<span class="label label-danger">T.Aktif</span>
		       					@endif
		       				</td>
							<td align='center'>
		       					<button onclick="edit('{{ $key->id }}','{{ $key->level }}','{{ $key->status_aktif }}')" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
		       					<form method="post" action="{{ route('level.destroy', $key['id'] ) }}"  style="display: inline">
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
 	

     	function edit(id,level,status_aktif){
     		$("#level_id").val(id);
     		$("#level_edit").val(level);
     		$("#status_aktif").val(status_aktif);
     		$("#modal_edit").modal('show');

     	}
     </script>

     @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Level"])
		<form method="POST" action="{{ route('level.store') }}">
			@csrf
			<div class="form-group @error('level') has-error @enderror ">
		        <label>Level</label>
		        <input id="level" type="text" class="form-control " value="{{ old('level') }}" name="level" >
		        @error('level')
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

	@component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Level"])
		<form method="POST" action="level/1">
			@csrf

			<input type="hidden" name="_method"  value="PUT">
			<input type="hidden" name="id" readonly id="level_id" value="{{ old('id') }}">
			
			<div class="form-group {{ $errors->edit->has('level') ? 'has-error' : '' }}">
		        <label >Level</label>
		        <input id="level_edit" type="text" class="form-control " value="{{ old('level') }}" name="level" >
		        @if($errors->edit->has('level'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('level') }} </strong>
                	</label>    
		        @endif 

	        </div>

	        <div class="form-group">
	        	<label class="label">Status Aktif</label>
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