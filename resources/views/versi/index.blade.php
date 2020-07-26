@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Versi App', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Versi App','link' => '#')
                                                    	) 
                                  ])
		<div class="card">
			@if(!isset($versi->id))
				<button class="btn btn-primary" data-toggle="modal" data-target="#modal_input">Create</button>
				<hr></hr>
			@endif

			@if (session('success'))
			 	@component("components.alert", ["type" => "success"])
					{{ session('success') }}
				@endcomponent
			@endif

			<div class="table-responsive" style="margin-top: 10px;">
				<table class=" table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
							<th>Versi</th>
							<th style="width: 200px; text-align: center;">Last Update</th>
							<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@if(isset($versi->id))
							<tr>
								<td>V. {{$versi->versi}}</td>
								<td align="center">{{ $versi->updated_at->format('d-m-Y h:i A')}}</td>
								<td align="center">
									<button onclick="edit('{{ $versi->id }}','{{ $versi->versi }}')" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
								</td>
							</tr>
						@else
							<tr>
								<td colspan="3" align="center">-- Silahkan Create Versi App --</td>
							</tr>
						@endif
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
 	

     	function edit(id,versi){
     		$("#versi_id").val(id);
     		$("#versi_edit").val(versi);
     		$("#modal_edit").modal('show');

     	}
     </script>

     @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Versi"])
		<form method="POST" action="{{ route('versi.store') }}" enctype="multipart/form-data">
			@csrf
			<div class="form-group @error('versi') has-error @enderror ">
		        <label>Versi</label>
		        <input id="versi" type="text" class="form-control " value="{{ old('versi') }}" name="versi" numeric >
		        @error('versi')
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

	@component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Versi"])
		<form method="POST" action="versi/edit" enctype="multipart/form-data">
			@csrf

			<input type="hidden" name="_method"  value="PUT">
			<input type="hidden" name="id" readonly id="versi_id" value="{{ old('id') }}">
			
			<div class="form-group {{ $errors->edit->has('versi') ? 'has-error' : '' }}">
		        <label >Versi</label>
		        <input id="versi_edit" type="text" class="form-control " value="{{ old('versi') }}" name="versi" >
		        @if($errors->edit->has('versi'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('versi') }} </strong>
                	</label>    
		        @endif 
	        </div>

	        <div class="text-right">
	        	 <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
	        </div>
		</form>
	@endcomponent
@endsection