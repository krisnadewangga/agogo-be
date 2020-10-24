@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Kurir', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Kurir','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
			<button class="btn btn-primary" data-toggle="modal" data-target="#modal_input">Create</button>
			<button class="btn btn-warning" data-toggle="modal" data-target="#modal_input_ongkir">SetOngkir</button>
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
						<th>No Hp</th>
						<th>Email</th>
						<th>Jenis Kendaraan</th>
						<th>Merek Kendaraan</th>
						<th>No Polisi</th>
						<th style="width: 50px; text-align: center;">Foto</th>
						<th style="width: 50px; text-align: center;">Status</th>
						<th style="width: 50px; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($kurir as $key)
							<tr>
								<td align="center"></td>
								<td class="nowrap">{{ $key->User->name }}</td>
								<td class="nowrap">{{ $key->User->no_hp }}</td>
								<td class="nowrap">{{ $key->User->email }}</td>
								<td class="nowrap">{{ $key->jenis_kendaraan }}</td>
								<td class="nowrap">{{ $key->merek }}</td>
								<td class="nowrap">{{ $key->no_polisi }}</td>
								<td class="nowrap" align="center">
									<a href="upload/{{ $key->User->foto }}" target="_blank" title="Lihat Gambar"><button class="btn-warning btn btn-sm" ><i class="fa fa-image"></i></button></a>
								</td>
								<td class="nowrap" align="center">
									@if($key->User->status_aktif == 1)
		       							<span class="label label-success ">Aktif</span>
			       					@else
			       						<span class="label label-danger">T.Aktif</span>
			       					@endif
			       				</td>
								<td class="nowrap" align="center">
									<button onclick="edit('{{ $key->User->id }}',
														  '{{ $key->User->name }}',
														  '{{ $key->User->no_hp }}',
														  '{{ $key->User->email }}',
														  '{{ $key->jenis_kendaraan }}',
														  '{{ $key->merek }}',
														  '{{ $key->no_polisi }}',
														  '{{ $key->User->status_aktif }}'
														  )" class='btn btn-sm btn-primary ' >
		       							<i class='fa fa-pencil'  ></i></button>
			       					
			       					<form method="post" action="{{ route('kurir.destroy', $key->User->id ) }}"  style="display: inline">
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

			@if(Session::get('gagal') == 'simpan_ongkir')
				$("#modal_input_ongkir").modal('show');
			@endif
 		});
 	

     	function edit(id,nama,no_hp,email,jenis_kendaraan,merek,no_polisi,status_aktif){
     		$("#kurir_id").val(id);
     		$("#nama_edit").val(nama);
     		$("#no_hp_edit").val(no_hp);
     		$("#jenis_kendaraan_edit").val(jenis_kendaraan);
     		$("#merek_edit").val(merek);
     		$("#no_polisi_edit").val(no_polisi);
     		$("#status_aktif").val(status_aktif);
     		$("#email_edit").val(email);
     		$("#modal_edit").modal('show');
     	}
     </script>

    @component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Kurir"])
		<form method="POST" action="{{ route('kurir.store') }}" enctype="multipart/form-data">
			@csrf

			<div class="form-group @error('name') has-error @enderror ">
		        <label>Nama</label>
		        <input id="name" type="text" class="form-control " value="{{ old('name') }}" name="name" autocomplete>
		        @error('name')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('no_hp') has-error @enderror ">
		        <label>No Hp</label>
		        <input id="no_hp" type="text" class="form-control " value="{{ old('no_hp') }}" name="no_hp" >
		        
		        @error('no_hp')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('email') has-error @enderror ">
		        <label>Email</label>
		        <input id="email" type="text" class="form-control " value="{{ old('email') }}" name="email" >
		        
		        @error('email')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('password') has-error @enderror ">
		        <label>Password</label>
		        <input id="password" type="text" class="form-control " value="{{ old('password') }}" name="password" >
		        
		        @error('password')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('jenis_kendaraan') has-error @enderror ">
		        <label>Jenis Kendaraan</label>
		        <select class="form-control" name="jenis_kendaraan">
		        	<option value="Motor">Motor</option>
		        	<option value="Mobil">Mobil</option>
		        </select>
		      
		        @error('jenis_kendaraan')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('merek') has-error @enderror ">
		        <label>Merek Kendaraan</label>
		        <input id="merek" type="text" class="form-control " value="{{ old('merek') }}" name="merek" >
		        
		        @error('merek')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('no_polisi') has-error @enderror ">
		        <label>No Polisi</label>
		        <input id="no_polisi" type="text" class="form-control " value="{{ old('no_polisi') }}" name="no_polisi" >
		        
		        @error('no_polisi')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>

	        <div class="form-group @error('foto') has-error @enderror ">
		        <label>Foto</label>
		        
		        <input id="foto" type="file"  name="foto" >
		        @error('foto')
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

	@component("components.modal", ["id" => "modal_input_ongkir" ,"kop_modal" => "Form Set Ongkir"])
		<form method="POST" action="{{ route('set_ongkir') }}" >
			@csrf

			<div class="form-group @error('biaya_ongkir') has-error @enderror ">
		        <label>Biaya Ongkir</label>
		        <input id="biaya_ongkir" type="text" class="form-control " value="{{ is_null($ongkir) ? '0' : $ongkir->biaya_ongkir }}" name="biaya_ongkir" autocomplete>
		        @error('biaya_ongkir')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
	        	
	        </div>
	        
	    	@if( is_null($ongkir) )
	        	<div style="padding:10px; margin-bottom: 5px; margin-top: -10px;" class="bg-warning text-yellow">
	        		Silahkan Atur Biaya Untuk Ongkir
	        	</div>
	        @else
	        	<hr style="margin:5px 0px 5px 0px;"></hr>
	        	<h5>Dibuat Oleh : {{ $ongkir->dibuat_oleh }}</h5>
	        	<h5>{{ $ongkir->updated_at->format('d M Y H:i A') }}</h5>
	        @endif


	        <div class="text-right">
	        	 <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
	        </div>
		</form>
	@endcomponent

	@component("components.modal", ["id" => "modal_edit" ,"kop_modal" => "Form Edit Kurir"])
		<form method="POST" action="kurir/edit" enctype="multipart/form-data">
			@csrf

			<input type="hidden" name="_method"  value="PUT">
			<input type="hidden" name="id" readonly id="kurir_id" value="{{ old('id') }}">


			<div class="form-group  {{ $errors->edit->has('name') ? 'has-error' : '' }} ">
		        <label>Nama</label>
		        <input id="nama_edit" type="text" class="form-control " value="{{ old('name') }}" name="name" autocomplete>
		        @if($errors->edit->has('name'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong> {{ $errors->edit->first('name') }} </strong>
                	</label>    
		        @endif 
	        </div>

	        <div class="form-group {{ $errors->edit->has('no_hp') ? 'has-error' : '' }} ">
		        <label>No Hp</label>
		        <input id="no_hp_edit" type="text" class="form-control " value="{{ old('no_hp') }}" name="no_hp" >
		        
		        @if($errors->edit->has('no_hp'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('no_hp') }}</strong>
                	</label>    
		        @endif 
	        </div>

	        <div class="form-group {{ $errors->edit->has('email') ? 'has-error' : '' }} ">
		        <label>Email</label>
		        <input id="email_edit" type="text" class="form-control " value="{{ old('email') }}" name="email" >
		        
		        @if($errors->edit->has('email'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('email') }}</strong>
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

	        <div class="form-group {{ $errors->edit->has('jenis_kendaraan') ? 'has-error' : '' }}  ">
		        <label>Jenis Kendaraan</label>
		        <select id="jenis_kendaraan_edit" class="form-control" name="jenis_kendaraan">
		        	<option value="Motor">Motor</option>
		        	<option value="Mobil">Mobil</option>
		        </select>
		      
		        @if($errors->edit->has('jenis_kendaraan'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('jenis_kendaraan') }}</strong>
                	</label>    
		        @endif 
	        	
	        </div>

	        <div class="form-group {{ $errors->edit->has('merek') ? 'has-error' : '' }} ">
		        <label>Merek Kendaraan</label>
		        <input id="merek_edit" type="text" class="form-control " value="{{ old('merek') }}" name="merek" >
		        
		         @if($errors->edit->has('merek'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('merek') }}</strong>
                	</label>    
		        @endif 
	        	
	        </div>

	        <div class="form-group{{ $errors->edit->has('no_polisi') ? 'has-error' : '' }}  ">
		        <label>No Polisi</label>
		        <input id="no_polisi_edit" type="text" class="form-control " value="{{ old('no_polisi') }}" name="no_polisi" >
		        
		        @if($errors->edit->has('no_polisi'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('no_polisi') }}</strong>
                	</label>    
		        @endif 
	        	
	        </div>


	        <div class="form-group {{ $errors->edit->has('foto') ? 'has-error' : '' }} ">
		        <label>Foto</label> &nbsp;<label class="label label-warning"><i class="fa fa-warning"></i> Kosongkan Jika Tidak Ingin Mengganti Foto</label>
		        
		        <input id="foto_edit" type="file" name="foto" >
		        @if($errors->edit->has('foto'))
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $errors->edit->first('foto') }}</strong>
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