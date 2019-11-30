@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'TopUp Saldo', 
								   'breadcumbs' => array(
                                                          array('judul' => 'TopUp Saldo','link' => '#'),
                                                          array('judul' => 'Create','link' => '#')
                                                    	) 
                                  ])
    	   <div class="card">
				<button class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modal_input">Create</button><a href="{{ route('list_topup_saldo') }}"><button class="btn btn-flat btn-default" data-toggle="modal" data-target="#modal_input">List Topup</button></a>
				<hr></hr>

				@if (session('success'))
				 	@component("components.alert", ["type" => "success"])
						{{ session('success') }}
					@endcomponent
				@endif
		
				<div class="row">
					<div class="col-md-12">
						<div class="input-group ">
			                <input type="text" class="form-control" placeholder="Masukan Nama / Nohp User" name="param">
			                <span class="input-group-btn">
			                   <button type="button" class="btn btn-info btn-flat" id="btn_action" onclick="cari_nama()">Cari</button>
			                </span>
			            </div>
					</div>
					
				</div>
				
				<div class="table-responsive" style="margin-top: 10px;">
					<table class="table table-bordered">
						<thead>
							<th>No</th>
							<th>Nama</th>
							<th>Jenis Kelamin</th>
							<th>TGL Lahir</th>
							<th>No Hp</th>
							<th>Alamat</th>
							<th><center>Status User</center></th>
							<th><center>Aksi</center></th>
						</thead>
						<tbody id="bodi_tbl_search">
							<tr>
								<td colspan="8" align="center">-- Tidak Ada Data User Yang Ditampilkan --</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
	  

		<script type="text/javascript">

			$(document).ready(function(){
	 			@if(Session::get('gagal') == 'update' )
	 				$("#modal_edit").modal('show');
				@endif
				@if(Session::get('gagal') == 'simpan' )
					$("#modal_topup").modal('show');
				@endif
	 		});

			function cari_nama(){
				var param = $("input[name='param']").val();
				$.ajax({
					url : "cari_user",
					data : "param="+param,
					beforeSend:function(){
						$("#bodi_tbl_search").html(`
													<tr>
														<td colspan='8' align='center'>
															<label class='label label-warning'><i class='fa fa-spinner fa-pulse fa-fw' ></i> Sedang Mempersiapkan Data</label>
														</td>
													</tr>
												  `);
					},
					success:function(msg){
						console.log(msg);

						if(msg.jumlah == '0'){
							$("#bodi_tbl_search").html(`
													<tr>
														<td colspan='8' align='center'>
															<label class='label label-danger'><i class='fa fa-warning' ></i>&nbsp; Data User TIdak Ditemukan</label>
														</td>
													</tr>
												  `);
						}else{
							var html = "";
							var no=1;
							$.each(msg.msg, function( index, value ) {
								html += `
										  <tr>
										  	 <td align='center'>`+no+`</td>
										  	 <td>`+value.name+`</td>
										  	 <td>`; 
										  	 	if(value.jenis_kelamin == '0'){
										  	 		html += 'Laki-Laki';
										  	 	}else if(value.jenis_kelamin == '1'){
										  	 		html += 'Perempuan';
										  	 	}else{
										  	 		html += '<label class="label label-warning">Belum Ditentukan</label>';
										  	 	}
									html += `</td>
										  	 <td>`+value.ket_tgl_lahir+`</td>
										  	 <td>`+value.no_hp+`</td>
										  	 <td>`+value.alamat+`</td>
										  	 <td align='center'><label class='label label-info'>`; 
										  	 	
										  	 	if(value.status_member == '0'){
										  	 		html += 'Not Member';
										  	 	}else if(value.status_member == '1'){
										  	 		html += 'Member';
										  	 	}

								    html += `</label></td>
										  	 <td align='center'>
										  	 	<button class="btn btn-warning btn-sm" onclick="topUp('`+value.user_id+`','`+value.status_member+`','`+value.name+`')"><i class="fa  fa-credit-card"></i></button>
										  	 </td>
										  </tr>
										`;
							no++;
							});
							$("#bodi_tbl_search").html(html);
						}
					}
				});
			}

			function topUp(user_id,status_member,nama){
				$("#user_id").val(user_id);
				$("#status_member").val(status_member);
				$("#nama").val(nama);
				$("#modal_topup").modal('show');
			}


		</script>     
    @endcomponent

    @component("components.modal", ["id" => "modal_topup" ,"kop_modal" => "Form Topup Saldo"])
		
		<form method="POST" action="{{ route('topup_saldo.store') }}" >
			@csrf
			<input type="text" id="user_id" name="user_id" hidden>
			<input type="text" id="status_member" name="status_member" hidden>
			
			<div class="form-group @error('kategori') has-error @enderror ">
		        <label>Nama</label>
		        <input id="nama" type="text" class="form-control " value="{{ old('nama') }}"  name="nama" readonly >
	        </div>

			<div class="form-group @error('saldo') has-error @enderror ">
		        <label>Nominal Saldo</label>
		        <input id="saldo" type="text" class="form-control " value="{{ old('saldo') }}" name="saldo" >
		        @error('saldo')
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