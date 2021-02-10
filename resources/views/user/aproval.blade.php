@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Aproval', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Aproval','link' => '#')
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
							<th style="text-align: center">Rule</th>
							<th>Name</th>
							<th style="width: 50px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($aproval as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->rule_name }}</td>
								<td>{{ $key->User->name }}</td>
								<td align="center">
									<form method="post" action="{{ route('aproval.destroy', $key['id'] ) }}"  style="display: inline">
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
 	

     	function edit(id,name,email,status_aktif, roles){
     		$("#user_id").val(id);
     		$("#nama_edit").val(name);
     		$("#email_edit").val(email);
     		$("#status_aktif").val(status_aktif);
     		console.log(roles);
     		$("#roles_edit").val(roles).trigger('change');

     		$("#modal_edit").modal('show');

     	}
     </script>

     @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input User"])
		<form method="POST" action="{{ route('aproval.store') }}" enctype="multipart/form-data">
			@csrf 

			<div class="form-group @error('rule') has-error @enderror">
                <label  class="control-label">Rule </label> 
                <!-- multiple="multiple" -->
                <select class="form-control"  id="rule" name="rule"  style="width: 100%" >	
                	
                   	@foreach($list_aproval as $key_aproval)
                   	 	<option value="{{ $key_aproval['id'] }}" 
                   	 		{{ (old('rule') == $key_aproval['id'] ) ? "selected": ""}}
                   	 	 > {{ $key_aproval['text'] }} </option>
                   	 	
                   	@endforeach
                </select>
               	
                 @error('rule')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
            </div>

	        <div class="form-group @error('user') has-error @enderror">
                <label  class="control-label">User </label> 
                <select class="form-control"  id="user" name="user[]" multiple="multiple" style="width: 100%" data-placeholder="Pilih User">	
                
                   	@foreach($users as $key_user)
                   	 	<option value="{{ $key_user->id }}" 
                   	 		{{ in_array($key_user->id, old("user") ?: []) ? "selected": ""}}
                   	 	 > {{ $key_user->name }} </option>
                   	 	
                   	@endforeach
                </select>
                 @error('user')
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

 
	<script type="text/javascript">
		$(document).ready(function(){
			$('#user').select2({allowClear:true});
			$('#rule').select2();
			$('#roles_edit').select2({allowClear:true, placeholder: "Pilih Roles"});
		});
	</script>
@endsection