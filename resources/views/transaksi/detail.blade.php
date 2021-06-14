@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Detail Pesanan', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Pesanan','link' => '../transaksi'),
                                                          array('judul' => $transaksi->no_transaksi,'link' => '#')

                                                    	) 
                                  ])
        @if (session('success'))
		 	@component("components.alert", ["type" => "success"])
				{{ session('success') }}
			@endcomponent
		@endif

		@if(session('error'))
			@component("components.alert_error", ["type" => "error"])
				{{ session('error') }}
			@endcomponent
		@endif

		@php
			if(isset($transaksi->AjukanBatalPesanan->id)){
			 	$cek_batal_ajukan = $transaksi->AjukanBatalPesanan->id;
			}
			
		@endphp
        <div class="row" style="margin-bottom: 20px; margin-top: 0px;">
			
			<div class="col-md-12">
				<div style="background-color: #FFFFFF; border:1px solid #CCC; border-top:none; border-left:none; border-right: none;">
					<div class="row">
						<div class="col-md-12">
							<div  style="padding:10px;">
								<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Daftar Item</b></h4>
								<div>Daftar Item Belanja Yang Masuk Dalam Pesanan</div>
							</div>
						</div>
						
					</div>
				</div>
				<div style="padding:15px 20px 20px 20px; background-color: #FFFFFF; margin-top: 0px; " >
						<div class="table-responsive">
							<table class="dataTables table  table-bordered">
								<thead style=" font-size:14px;">
									<tr>
										<th style="width: 5px;">No</th>
										<th>Item</th>
										<th style="width: 100px;"><center>Jumlah</center></th>
										<th style="width: 100px;"><center>Harga</center></th>
										<!-- <th><center>Diskon ?</center></th>
										<th><center>Harga Diskon</center></th> -->
										<th style="width: 100px;"><center>Total</center></th>
									</tr>
								</thead>
								<tbody style=" font-size:14px;">
									@foreach($transaksi->ItemTransaksi as $key)
										<tr>
											<td></td>
											<td>{{ $key->Item->nama_item}}</td>
											<td align="center">{{ $key->jumlah }} PCS</td>
											<td align="center">
												@if($key->diskon > 0)
													<label class="label label-default text-red" style="text-decoration: line-through;">Rp. {{ number_format($key->harga,'0','','.') }}</label>
												@else
													<label class="label label-success text-white" >Rp. {{ number_format($key->harga,'0','','.') }}</label> 
												@endif
											</td>
											<!-- <td align="center">
												@if($key->diskon > 0)
													<label class="label label-warning text-white" >
														{{ $key->diskon }} %
													</label>
												@else
													<label class="label label-default text-red" >
														{{ $key->diskon }}
													</label>
												@endif
											</td>
											<td align="center">
												@if($key->diskon > 0)
													<label class="label label-success text-white" >
														Rp. {{ number_format($key->harga_diskon,'0','','.') }} 
													</label>
												@else
													<label class="label label-default text-red" >
														Rp. {{ number_format($key->harga_diskon,'0','','.') }} 
													</label>
												@endif
												
											</td> -->
											<td align="center">Rp. {{ number_format($key->total,'0','','.') }} </td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						@if($transaksi->catatan_user != '-')
							<div  style="padding:10px; margin-top: 10px;" class="bg-warning text-orange  ">
								<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Catatan Pemesan</b></h4>
								<div>{{$transaksi->catatan}}</div>
							</div>
							
						@endif
				</div>
				
				@if($transaksi->metode_pembayaran != '3')
					<div class="bg-white" style="margin-top: 10px; background-color: #FFFFFF; padding:10px;">
						<div class="row">
							<div class="col-md-8">
								<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Pengiriman</b></h4>
								<div>
									Klik <u onclick="$('#bodi_pengiriman').toggle('slow')" style="cursor:pointer;">Lihat Detail</u> Untuk Melihat Deskripsi Pengiriman
								</div>
							</div>
							<div class="col-md-4 text-right">
								@php
									$waktu_skrang = strtotime(date('Y-m-d H:i:s'));
									$batas_ambe = strtotime($transaksi->waktu_kirim);
								@endphp

								@if($transaksi->status == '1')
									<label class="label label-info">Dikemas</label>
								@elseif($transaksi->status == '2')
									<label class='label bg-purple'>Dikirim</label>
								@elseif($transaksi->status == '3')
									<label class='label label-danger'>Pesanan Dibatalkan</label>
								@elseif($transaksi->status == '4')
									<label class='label label-warning'>Ajukan Pembatalan</label>
								@elseif($transaksi->status == '5')
									<label class='label label-success'>Terima</label>
								@elseif($transaksi->status == '6')
									<label class="label bg-yellow">Menunggu Transfer</label>
								@endif

								<h6 style="margin-bottom: 0px; margin-top: 5px; cursor:pointer;" onclick="$('#bodi_pengiriman').toggle('slow')"><u>Lihat Detail</u></h6>
								
							</div>
						</div>
						
						<div id="bodi_pengiriman" hidden>
							<div class="row" style="margin-top: 10px; margin-bottom: 10px; ">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-bordered">
											<thead>
												<th>Nama Pemesan</th>
												<th>No Hp</th>
												<th>Akan Dikirimkan</th>
												<th><center>Alamat</center></th>
												@if($transaksi->status >= '2' && $transaksi->status != '3'  && $transaksi->status != '6' )
													<th><center>Waktu Pengiriman</center></th>
												@endif

												@if($transaksi->status == '5')
													<th><center>Waktu Diterima</center></th>
												@endif

												
												@if($transaksi->status == '3' )
													@if(!isset($cek_batal_ajukan))
														<th><center>Dibatalkan Oleh</center></th>
														
													@else
														<th><center>Diajukan Pembatalan</center></th>
														<th><center>Disetujui Pembatalan</center></th>
													@endif
												@endif
												
												

											</thead>
											<tbody>
												<tr>
													<td>{{ $transaksi->User->name }}</td>
													<td>{{ $transaksi->User->no_hp }}</td>
													<td>
														@if($transaksi->metode_pembayaran == '2')
															{{ $transaksi->waktu_kirim->format('d M Y h:i A') }}
														@else
															{{ $transaksi->waktu_kirim->format('d M Y h:i A') }}
														@endif
													</td>
													<td align="center">
														{{ $transaksi->detail_alamat }} 
														<hr style="margin:3px;"></hr>
														<small>Jarak Tempuh : <label class="label  label-default text-yellow ">{{ $transaksi->jarak_tempuh }} KM </small>
													</td>
													
													@if( $transaksi->status >= '2' && $transaksi->status != '3' && $transaksi->status != '6' && $transaksi->status != '4' )
														<td align="center">
															{{$transaksi->Pengiriman->created_at->format('d M Y H:i A')}}
															<hr style="margin:3px;"></hr>
															<small class="label label-info" style="cursor: pointer;" data-toggle="modal" data-target="#modal_profil">Profil Kurir</small>
														</td>
													@endif


													@if($transaksi->status == '5')
														<td align="center">
															{{ $transaksi->Pengiriman->updated_at->format('d M Y H:i A') }}
															<hr style="margin:3px;"></hr>
															<label class="label label-success">{{ $transaksi->Pengiriman->diterima_oleh}}</label>
															
															
														</td>
													@endif

													@if($transaksi->status == '3')
														@if(!isset($cek_batal_ajukan))
															<td align="center">
																<label class="label label-danger">
																	{{ $transaksi->BatalPesanan->input_by}}
																</label>
																<hr style="margin:3px;"></hr>
																{{ $transaksi->BatalPesanan->created_at->format('d M Y H:i A') }}
															</td>
														
														@else
															<td align="center"	>
																<label class="label label-danger">
																	{{ $transaksi->AjukanBatalPesanan->diajukan_oleh }}
																</label>
																<hr style="margin:3px;"></hr>
																{{ $transaksi->AjukanBatalPesanan->created_at->format('d M Y H:i A') }}
															</td>
															<td align="center">
																<label class="label label-success">
																	{{ $transaksi->AjukanBatalPesanan->disetujui_oleh }}
																</label>
																<hr style="margin:3px;"></hr>
																{{ $transaksi->AjukanBatalPesanan->updated_at->format('d M Y H:i A') }}
															</td>
														@endif

														
													@endif
												</tr>
											</tbody>
										</table>
									</div>
									
								</div>
							</div>
						</div>
					</div>

					
				@else 
					<div class="bg-white" style="margin-top: 10px; background-color: #FFFFFF; padding:10px;">
						<div class="row">
							<div class="col-md-8">
								<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Pengambilan</b></h4>
								<div>
									Klik <u>Lihat Detail</u> Untuk Melihat Deskripsi Pengambilan
								</div>
							</div>
							<div class="col-md-4 text-right">
								@php
									$waktu_skrang = strtotime(date('Y-m-d H:i:s'));
									$batas_ambe = strtotime($transaksi->waktu_kirim);
								@endphp

								@if($transaksi->status == '1')
									<label class="label label-info">Dikemas</label>
							
								@elseif($transaksi->status == '3')
									<label class='label label-danger'>Dibatalkan</label>
								@elseif($transaksi->status == '4')
									<label class='label label-warning'>Pengajuan Pembatalan</label>
								@elseif($transaksi->status == '5')
									<label class='label label-success'>Terima</label>
								@endif

								
								<h6 style="margin-bottom: 0px; margin-top: 5px; cursor:pointer;" onclick="$('#bodi_pengambilan').toggle('slow')"><u>Lihat Detail</u></h6>
								
							</div>
						</div>
						
						<div id="bodi_pengambilan" hidden>
							<div class="row" style="margin-top: 10px; margin-bottom: 10px; ">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="table table-bordered">
											<thead>
												<th>Nama Pemesan</th>
												<th>No Hp</th>
												<th>Akan Diambil</th>
												<th><center>Alamat</center></th>

												@if($transaksi->status == '5')
													<th><center>Waktu Ambil</center></th>
												@endif

												@if($transaksi->status == '3' )
													@if(!isset($cek_batal_ajukan))
														<th><center>Dibatalkan Oleh</center></th>
													@else
														<th><center>Diajukan Pembatalan</center></th>
														<th><center>Disetujui Pembatalan</center></th>
													@endif
												@endif
												
											</thead>
											<tbody>
												<tr>
													<td>{{ $transaksi->User->name }}</td>
													<td>{{ $transaksi->User->DetailKonsumen->no_hp }}</td>
													<td>
														{{ $transaksi->waktu_kirim->format('d M Y H:i A') }}
													</td>
													<td align="center">
														{{ $transaksi->detail_alamat }} 
													</td>
													
													@if($transaksi->status == '5')
														<td align="center">
															{{ $transaksi->AmbilPesanan->created_at->format('d M Y H:i A') }}
															<hr style="margin:3px;"></hr>
															<small>Diambil Oleh : </small><label class="label label-success">{{ $transaksi->AmbilPesanan->diambil_oleh}}</label>
														</td>
													@endif

													@if($transaksi->status == '3' )
														@if(!isset($cek_batal_ajukan))
															<td align="center">
																<label class="label label-danger">{{ $transaksi->BatalPesanan->input_by}}</label>
																<hr style="margin:3px;"></hr>
																{{ $transaksi->BatalPesanan->created_at->format('d M Y H:i A') }}
															</td>
															
														@else
															<td align="center"	>
																<label class="label label-danger">
																	{{ $transaksi->AjukanBatalPesanan->diajukan_oleh }}
																</label>
																<hr style="margin:3px;"></hr>
																{{ $transaksi->AjukanBatalPesanan->created_at->format('d M Y H:i A') }}
															</td>
															<td align="center">
																<label class="label label-success">
																	{{ $transaksi->AjukanBatalPesanan->disetujui_oleh }}
																</label>
																<hr style="margin:3px;"></hr>
																{{ $transaksi->AjukanBatalPesanan->updated_at->format('d M Y H:i A') }}
															</td>
														@endif
													@endif
												</tr>
											</tbody>
										</table>
									</div>
									
								</div>
							</div>
						</div>

					</div>

					
					
				@endif
				

				<!-- PEMBAYARAN -->
				<div class="bg-white" style="margin-top: 10px; background-color: #FFFFFF; padding:10px;">
					<div class="row">
						<div class="col-md-8">
							<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Pembayaran</b></h4>
							<div>
								Metode Pembayaran Yang Digunakan Via <label class="label label-warning text-white">{{$transaksi->ket_metodepembayaran}}</label> 

								@if($transaksi->metode_pembayaran == '2')
									@php
										$waktu_skrang1 = strtotime(date('Y-m-d H:i:s'));
										$batas_bayar = strtotime($transaksi->waktu_kirim);
									@endphp

									@if( ($waktu_skrang > $batas_ambe) && ($transaksi->status == '6') )
										<label class="label label-danger">Pembayaran Expired</label>&nbsp;<label class="label label-default">BATAS TRANSFER : {{ $transaksi->waktu_kirim_tf->format('d/m/Y h:i A') }} </label>
									@elseif( ($waktu_skrang > $batas_ambe) && ($transaksi->status == '3') )
										<label class="label label-danger">Pesanan Dibatalkan</label>
									@elseif( ($waktu_skrang < $batas_ambe) && ($transaksi->status == '3') )
										<label class="label label-danger">Pesanan Dibatalkan</label>
									@else
										@if($transaksi->status == '6' )
											&nbsp  <label class="label label-default">BATAS TRANSFER : {{ $transaksi->waktu_kirim_tf->format('d/m/Y h:i A') }} </label>
										@elseif($transaksi->status == '4')
											&nbsp <label class="label label-default">BATAS TRANSFER : {{ $transaksi->waktu_kirim->format('d/m/Y h:i A') }} </label>
										@else
											&nbsp <label class="label label-success">Terbayar</label> &nbsp; <label class="label label-default">TGL BAYAR : {{ $transaksi->tgl_bayar->format('d/m/Y h:i A') }} </label>
										@endif

									@endif

								@endif
							</div>
						</div>
						<div class="col-md-4 text-right">
							
							@if($transaksi->metode_pembayaran != '3')
								<h3 style="margin:0px;"> Rp. {{number_format($transaksi->total_bayar,'0','','.') }} </h3>

								<h6 style="margin-bottom: 0px; margin-top: 5px; cursor:pointer;" >
									<u  data-toggle="modal" data-target="#modal_rincian">Lihat Rincian</u>
								</h6>
							@else
								<h3 style="margin:0px;"> Rp. {{number_format($transaksi->total_bayar,'0','','.') }} </h3>
								<h6 style="margin-bottom: 0px; margin-top: 5px; cursor:pointer;" ><u>Total Bayar</u></h6>
							@endif
						</div>
					</div>
				</div>

				@if(isset($transaksi->AjukanBatalPesanan->id) && $transaksi->AjukanBatalPesanan->status == '0' )
					<div class="bg-danger text-red" style="margin-top: 10px; padding:10px">
						<div><i class="fa fa-info-circle fa-2x"> <u>INFO</u></i>  </div>
						
						<p style="margin-top: 5px;">Pesanan Ini Telah Diajukan Oleh <b>{{ $transaksi->AjukanBatalPesanan->diajukan_oleh }}</b> Untuk Pembatalan Pesanan Pada Tanggal : <b>{{ $transaksi->AjukanBatalPesanan->created_at->format('d/m/Y h:i A') }}</b>  </p>
					</div>
				@endif

				<!-- Tombol -->
				@if($transaksi->status == '1' && $transaksi->metode_pembayaran != '3')
					<div style="margin-top: 10px;">
						
						<button class="btn btn-primary" data-target="#modal_input" data-toggle="modal">Mulai Pengiriman</button>

						@if($transaksi->status < '2' && $transaksi->status != '6')
							<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>
						@endif
					</div>
				<!-- Tombol Konfir Bayar -->
				@elseif($transaksi->status == '6' && $transaksi->metode_pembayaran == '2')
					<div style="margin-top: 10px;"> 
						@if($waktu_skrang < $batas_ambe)
							<a href="{{ route('konfir_pembayaran',$transaksi->id) }}" onclick="return confirm('Apakah Anda Yakin Mengkonfirmasi Pembayaran ?')">
								<button class="btn btn-success">Konfir Pembayaran</button>
							</a>
						@endif

						<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>
					</div>

				<!-- tombol bayar di toko -->
				@elseif($transaksi->status == '1' && $transaksi->metode_pembayaran == '3')
					<div style="margin-top: 10px;">
						@if($waktu_skrang < $batas_ambe)
							<button class="btn btn-success" data-target="#modal_ambil_pesanan" data-toggle="modal">Pesanan Diambil</button>
						@endif


						@if($waktu_skrang > $batas_ambe )
							<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>
						@endif
					</div>
				@elseif($transaksi->status == '4' && $transaksi->metode_pembayaran == '2')
					<div style="margin-top: 10px;"> 
						@if($waktu_skrang < $batas_ambe)
						<a href="{{ route('konfir_pembayaran',$transaksi->id) }}" onclick="return confirm('Apakah Anda Yakin Mengkonfirmasi Pembayaran ?')">
							<button class="btn btn-success">Konfir Pembayaran</button>
						</a>
						@endif

						<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>
					</div>
				@elseif($transaksi->status == '4' && $transaksi->metode_pembayaran == '1')
					<div style="margin-top: 10px;"> 
						<button class="btn btn-primary" data-target="#modal_input" data-toggle="modal">Mulai Pengiriman</button>

						<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>
					</div>
				@elseif($transaksi->status == '4' && $transaksi->metode_pembayaran == '3')
					<div style="margin-top: 10px;"> 
						@if($waktu_skrang < $batas_ambe)
							<button class="btn btn-success" data-target="#modal_ambil_pesanan" data-toggle="modal">Pesanan Diambil</button>
						@endif
						<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>
					</div>
				@endif

				@if($transaksi->status == '2' )
					<div style="margin-top: 10px;">
						<a href="{{ route('pesanan_diterima', $transaksi->id ) }}" onclick="return(confirm('Apakah Anda Yakin ?'))"><button class="btn btn-success">Pesanan Diterima</button></a>
						@if($transaksi->metode_pembayaran != '1')
							<!-- <a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a> -->
						@endif
					</div>
				@endif

				@if($transaksi->status == '3')
   					<div style="margin-top: 10px;"> 
   						<button onclick="hapus_pesanan('{{ $transaksi->id }}')" id="btn-hapus" class=' btn btn-danger '>Hapus Pesanan</button>
   					</div>
				@endif

				@if($transaksi->status == '4' && $transaksi->metode_pembayaran == '4')
   					<div style="margin-top: 10px;"> 
   					<a href="{{ route('batal_transaksi',['transaksi_id' => $transaksi->id]) }}" onclick="return confirm('Apakah Anda Yakin Membatalkan Pesanan ?') "><button class="btn btn-danger">Batalkan Pesanan</button></a>	
					</div>
				@endif
			</div>
		</div>	

		<script type="text/javascript">
	  
	 		$(document).ready(function(){
	 			@if(Session::get('gagal') == 'update' )
	 				$("#modal_edit").modal('show');
				@endif
				@if(Session::get('gagal') == 'simpan' )
					$("#modal_input").modal('show');
				@endif

				@if(Session::get('gagal') == 'simpan_ambil_pesanan')
					$("#modal_ambil_pesanan").modal('show');
				@endif

				@if(Session::get('gagal') == 'kurir')
					$("#modal_input").modal('show');
				@endif
	 		});
	 	

	     	function edit(id,level,status_aktif){
	     		$("#level_id").val(id);
	     		$("#level_edit").val(level);
	     		$("#status_aktif").val(status_aktif);
	     		$("#modal_edit").modal('show');

	     	}

	     	function hapus_pesanan(id)
			{
				var konfir  = confirm('Apakah Anda Yakin ?');
				if(konfir){
				
					$.ajax({
						url : '../transaksi/'+id,
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						type : 'POST',
						data : '_method=delete',
						beforeSend:function(){
							$('#btn-hapus').html("<i class='fa fa-spinner fa-pulse fa-fw ' ></i> Loading ....");
						},
						success:function(msg){
							if(msg.success == "1"){
								alert("Berhasil Hapus Pesanan");
								window.location.href = "{{route('transaksi.index')}}";
							}
						},
				        error: function(reason) {
	            			if(reason.status === 419 || reason.status === 401 || reason.status === 403){
					          // alert("Maaf! Sesi Anda Telah Habis, Silahkan Login Kembali");
					            window.location.href = "{{route('home')}}";
					        }
	        			}	
					});
				}
			}

     	</script>

     	@if( ($transaksi->metode_pembayaran == '1' && $transaksi->status > '1' && $transaksi->status != '3' && $transaksi->status != '4') 
     			|| ($transaksi->metode_pembayaran == '2' && $transaksi->status >= '2' && $transaksi->status != '3'  && $transaksi->status != '6'  && $transaksi->status != '6' && $transaksi->status != '4' ) )
	     	
	     	<div class="modal fade"  id="modal_profil">
				<!-- Add the bg color to the header using any of the bg-* classes -->
		        <div class="modal-dialog">
				    <div class="modal-content">
				    	
				    	<!-- Widget: user widget style 1 -->
				          <div class="box box-widget widget-user">
				            <!-- Add the bg color to the header using any of the bg-* classes -->
				           
				            <div class="widget-user-header bg-aqua-active">
				              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          				  <span aria-hidden="true">&times;</span></button>
				              
				              <h3 class="widget-user-username">{{ $transaksi->Pengiriman->Kurir->User->name }}</h3>
				              <h5 class="widget-user-desc">Kurir</h5>
				            </div>
				            <div class="widget-user-image ">
				              <img class="img-circle" src="{{ asset($transaksi->Pengiriman->Kurir->User->foto) }}" alt="User Avatar">
				            </div>
				            <div class="box-footer" style="padding-top:40px;">
				              <div class="row">
				                <div class="col-sm-4 border-right">
				                  <div class="description-block">
				                    <h5 class="description-header">{{ $transaksi->Pengiriman->Kurir->merek }}</h5>
				                    <span class="description-text">JENIS KENDARAAN</span>
				                  </div>
				                  <!-- /.description-block -->
				                </div>
				                <!-- /.col -->
				                <div class="col-sm-4 border-right">
				                  <div class="description-block">
				                    <h5 class="description-header">{{ $transaksi->Pengiriman->Kurir->no_polisi }}</h5>
				                    <span class="description-text">NO POLISI</span>
				                  </div>
				                  <!-- /.description-block -->
				                </div>
				                <!-- /.col -->
				                <div class="col-sm-4">
				                  <div class="description-block">
				                    <h5 class="description-header">{{ $transaksi->Pengiriman->Kurir->User->no_hp }}</h5>
				                    <span class="description-text">NO HP</span>
				                  </div>
				                  <!-- /.description-block -->
				                </div>
				                <!-- /.col -->
				              </div>
				              <!-- /.row -->
				            </div>
				          </div>
				          <!-- /.widget-user -->	
		    			
					</div>
				</div>
			</div>
		@endif
    
    @endcomponent

    @component("components.modal", ["id" => "modal_rincian" ,"kop_modal" => "Rincian Belanja"])
		<div class="row">
			<div class="col-md-6 col-xs-6">
				Total Harga Item
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{number_format($transaksi->total_transaksi,'0','','.')}}
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6 col-xs-6">
				Ongkos Kirim
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{number_format($transaksi->total_biaya_pengiriman,'0','','.')}}
			</div>
		</div>

		@if($transaksi->potongan > 0)
			<div class="row" style="margin-top: 10px;">
				<div class="col-md-6 col-xs-6">
					Voucher - <label class="label label-success">#{{$transaksi->kode_voucher}}</label>
				</div>
				<div class="col-md-6 text-right col-xs-6 text-red">
					- Rp {{number_format($transaksi->potongan,'0','','.')}}
				</div>
			</div>
		@endif

		<div  style="margin-top: 10px; border:1px solid #CCC; border-left: none; border-right: none; border-bottom:none; padding:10px;" class="bg-primary">
			<div class="row">
				<div class="col-md-6 col-xs-6">
					Total Pembayaran
				</div>
				<div class="col-md-6 text-right col-xs-6">
					Rp {{number_format($transaksi->total_bayar,'0','','.')}}
				</div>
			</div>
			
		</div>
	@endcomponent

	@component("components.modal", ["id" => "modal_input" ,"kop_modal" => "Form Input Kurir"])
		
		<form method="post" action="{{ route('pengiriman.store') }}"> 
			@csrf
			<input type="hidden" name="transaksi_id" value="{{$transaksi->id}}">
			<div class="form-group @error('kurir_id') has-error @enderror ">
				<label>Pilih Kurir</label>
	            <select id="kurir_id" class="form-control select2 " style="width: 100%;" name="kurir_id">
	              	<option value="">Pilih Kurir</option>
	            	@foreach($kurir as $key)
	            		<option value="{{ $key->id }}">{{ $key->nama }}</option>
	            	@endforeach
	            </select>
	            @error('kurir_id')
		            <label class="control-label" for="inputError">
                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                	</label>    
		        @enderror 
			</div>
			@if (session('error'))
			 	@component("components.alert_error", ["type" => "danger"])
					{{ session('error') }}
				@endcomponent
			@endif

			<button class="btn btn-primary">Simpan</button>
		</form>
	@endcomponent

	@component("components.modal", ["id" => "modal_ambil_pesanan" ,"kop_modal" => "Form Ambil Pesanan"])
		<form method="POST" action="{{ route('ambil_pesanan') }}">
			@csrf
			<input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">
			<div class="form-group @error('diambil_oleh') has-error @enderror ">
		        <label>Diambil Oleh ?</label>&nbsp; <label class="label label-warning">Silahkan Ganti Nama Pengambil Apabila Pesanan Diambil Oleh Orang Lain</label>
		        <input id="diambil_oleh" type="text" class="form-control" value="{{ $transaksi->User->name }}" name="diambil_oleh" >
		        @error('diambil_oleh')
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