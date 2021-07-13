@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Tax', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Tax','link' => '#')
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
						
						<th>Nama</th>
						<th style="width: 100px; text-align: center;">Status</th>
						<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($tax as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->nama }}</td>
								
								<td align="center">
									@if($key['status_aktif'] == 1)
		       							<span class="label label-success ">Aktif</span>
			       					@else
			       						<span class="label label-danger">T.Aktif</span>
			       					@endif
								</td>
								<td align="center">
									<button onclick="edit('{{ $key->id }}','{{ $key->nama }}','{{ $key->status_aktif }}')" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
			       					
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


	@component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Tax"])
		<form method="POST" action="{{route('tax_update')}}" enctype="multipart/form-data">
			@csrf

			<input type="hidden" name="_method"  value="POST">
			<input type="hidden" name="id" readonly id="kategori_id" value="{{ old('id') }}">
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