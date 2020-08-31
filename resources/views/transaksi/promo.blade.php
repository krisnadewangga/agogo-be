@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Setup Promo', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Setup Promo','link' => '#')
                                                    	) 
                                  ])
    	<div class="card">
			<button class="btn btn-primary btn-flat" data-target="#modal_input" data-toggle="modal">Create</button><a href="{{route('list_promo_selesai')}}"><button class="btn bg-success text-green btn-flat ">Promo Yang Telah Selesai</button></a>
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
						<th>Waktu</th>
						<th>Judul</th>
						<th>Deskripsi</th>
						<th><center>Gambar</center></th>
						<th><center>Berlaku Sampai</center></th>
						<th><center>Status</center></th>
						<th style="width: 100px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($promo as $key)
							<tr>
								<td align="center" class="nowrap"></td>
								<td class="nowrap">{{ $key->created_at->format('d M Y H:i A') }}</td>
								<td class="nowrap">{{ $key->judul }}</td>
								<td class="nowrap">{!! $key->deskripsi !!}</td>
								<td class="nowrap" align="center"><a href="{{ $key->gambar }}" target="_blank" title="Lihat Gambar"><button class="btn-warning btn btn-sm" ><i class="fa fa-image"></i></button></a></td>
								<td class="nowrap" align="center">{{ $key->berlaku_sampai->format('d M Y') }}</td>
								<td class="nowrap" align="center">
									@php
										$tgl_skrang = strtotime(date('Y-m-d'));
										$batas_promo = strtotime($key->berlaku_sampai );
										$for_m = $key->berlaku_sampai->format('d/m/Y');
									@endphp
									
									@if($key->status == '1')
										@if($batas_promo < $tgl_skrang)
											<span class="label label-danger ">Expired</span>
										@else
											<span class="label label-success ">Aktif</span>
										@endif
									@else
										<span class="label label-danger ">T.Aktif</span>
									@endif
								</td>
								<td class="nowrap" align="center">
									<a href="javascript:edit('{{ $key->id }}','{{ $key->judul }}','{{$for_m}}','{{$key->deskripsi}}')"><button class='btn btn-sm btn-primary ' >
	       							<i class='fa fa-pencil'  ></i></button></a>

									<form method="post" action="{{ route('setup_promo.destroy', $key['id'] ) }}"  style="display: inline">
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
     
     @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Promo"])
		<form method="POST" action="{{ route('setup_promo.store') }}" enctype="multipart/form-data">
			@csrf
			
			<div class="form-group @error('judul') has-error @enderror ">
		        <label>Judul</label>
		        <input id="kategori" type="text" class="form-control " value="{{ old('judul') }}" name="judul" >
		        @error('judul')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	         <div class="form-group @error('deskripsi') has-error @enderror" >
	        	<label>Deskripsi</label>
	        	<textarea id="deskripsi_entri" class="form-control" name="deskripsi">
	        		
	        	</textarea>
	        	@error('deskripsi')
	        		 <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>  
	        	@enderror
	        </div>

	        <div class="form-group @error('berlaku_sampai') has-error @enderror ">
	        	<label>Berlaku Sampai</label>
	        	<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
			      <input id="berlaku_sampai" type="text" class="form-control " value="{{ old('berlaku_sampai') }}" name="berlaku_sampai" autocomplete="off" >
			    </div>
		        @error('berlaku_sampai')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>


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
	        	 <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Apakah Anda Yakin Membuat Promo ?')">Simpan</button>
	        </div>
		</form>
	 @endcomponent
	 
	 @component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Promo"])
		<form method="POST" action="setup_promo/edit" enctype="multipart/form-data">
			@csrf

			<input type="hidden" name="_method"  value="PUT">
			<input type="hidden" name="id" readonly id="promo_id" value="{{ old('id') }}">
			
			<div class="form-group {{ $errors->edit->has('judul') ? 'has-error' : '' }}">
		        <label >Judul</label>
		        <input id="judul_edit" type="text" class="form-control " value="{{ old('judul') }}" name="judul" >
		        @if($errors->edit->has('judul'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('judul') }} </strong>
                	</label>    
		        @endif 
	        </div>

	         <div class="form-group {{ $errors->edit->has('deskripsi') ? 'has-error' : '' }}" >
	        	<label>Deskripsi</label>
	        	<textarea id="deskripsi_edit" class="form-control" name="deskripsi">
	        		
	        	</textarea>
	        	@if($errors->edit->has('deskripsi'))
	        		 <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('deskripsi') }}</strong>
                	</label>  
	        	@endif
	        </div>

	        <div class="form-group {{ $errors->edit->has('berlaku_sampai') ? 'has-error' : '' }}">
	        	<label>Berlaku Sampai</label>
	        	<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
			      <input id="berlaku_sampai_edit" type="text" class="form-control " value="{{ old('berlaku_sampai') }}" name="berlaku_sampai" autocomplete="off" >
			    </div>
		        @error('berlaku_sampai')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        </div>

	        <div class="form-group {{ $errors->edit->has('gambar') ? 'has-error' : '' }} ">
		        <label>Gambar</label> &nbsp;<label class="label label-warning"><i class="fa fa-warning"></i> Kosongkan Jika Tidak Ingin Mengganti Gambar</label>
		        
		        <input id="gambar_edit" type="file" name="gambar" >
		        @if($errors->edit->has('gambar'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('gambar') }}</strong>
                	</label>    
		        @endif 
	        </div>


	        <div class="form-group">
	        	<label>Status Aktif</label>
	        	<select class="form-control" name="status" id="status">
	        		<option value="1">Aktif</option>
	        		<option value="0">T.Aktif</option>
	        	</select>
	        </div>

	        <div class="text-right">
	        	 <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
	        </div>
		</form>
	@endcomponent
	<script type="text/javascript">
	  	
 		$(document).ready(function(){
 			CKEDITOR.replace('deskripsi_entri');
 			CKEDITOR.replace('deskripsi_edit');

 			@if(Session::get('gagal') == 'update' )
 				$("#modal_edit").modal('show');
			@endif
			@if(Session::get('gagal') == 'simpan' )
				$("#modal_input").modal('show');
			@endif

			var date = new Date();
			date.setDate(date.getDate());

			$('#berlaku_sampai').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true,
		           startDate: date
		        });

			$('#berlaku_sampai_edit').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true
		        });


			
 		});
 	

     	function edit(id,judul,for_m,deskripsi){
     		// alert(id+" "+judul+" "+for_m);
     		$("#promo_id").val(id);
     		$("#judul_edit").val(judul);
     		$("#berlaku_sampai_edit").val(for_m);
     		CKEDITOR.instances.deskripsi_edit.setData(deskripsi);
		    $("#modal_edit").modal('show');

     	}
     </script>

@endsection