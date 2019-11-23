@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Detail Transaksi', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Transaksi','link' => '../transaksi'),
                                                          array('judul' => $transaksi->no_transaksi,'link' => '#')

                                                    	) 
                                  ])
        @if (session('success'))
		 	@component("components.alert", ["type" => "success"])
				{{ session('success') }}
			@endcomponent
		@endif

        <div class="row" style="margin-bottom: 20px; margin-top: 0px;">
			
			<div class="col-md-12">
				<div style="background-color: #FFFFFF; border:1px solid #CCC; border-top:none; border-left:none; border-right: none;">
					<div class="row">
						<div class="col-md-12">
							<div  style="padding:10px;">
								<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Daftar Pesanan</b></h4>
								<div>Daftar Item Belanja Yang Masuk Dalam Pesanan </div>
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
										<th><center>Jumlah</center></th>
										<th><center>Harga</center></th>
										<th><center>Diskon ?</center></th>
										<th><center>Harga Diskon</center></th>
										<th><center>Total</center></th>
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
											<td align="center">
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
												
											</td>
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
				
				<div class="bg-white" style="margin-top: 10px; background-color: #FFFFFF; padding:10px;">
				
					<div class="row">
						<div class="col-md-12">
							<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Pengiriman</b></h4>
							<div class="table-responsive">
								<table class="table">
									<tr>
										<td >
											<i class="fa fa-user bg-success text-green" style="padding:5px;"></i> &nbsp;{{ $transaksi->User->name }}
										</td>
										<td>
											<i class="fa fa-phone bg-warning text-orange" style="padding:5px;"></i> &nbsp;{{ $transaksi->User->DetailKonsumen->no_hp }} 
										</td>
											
										<td>
											<i class="fa fa-clock-o bg-danger text-red" style="padding:5px;"></i> &nbsp; {{ $transaksi->waktu_kirim}}
										</td>
										<td >
											<i class="fa fa-map-marker bg-info text-blue" style="padding:6px;"></i> &nbsp;{{ $transaksi->detail_alamat }} - <label class="label  label-default text-yellow ">{{ $transaksi->jarak_tempuh }} KM</label>
										</td>


										@if($transaksi->status > '1')
											<td align="center">
												@if($transaksi->status == '2')
													<label class="label label-warning">Pengiriman</label> 
												@else if($transaksi->status == '3')
													<label class="label label-warning">Pesanan Diterima</label> 
												@endif
												<small style="cursor: pointer;" data-toggle="modal" data-target="#modal_profil"><u>Profil Kurir</u></small>
											</td>
										@endif
									</tr>
									
								</table>	
							</div>
						</div>
					</div>
				</div>

				<div class="bg-white" style="margin-top: 10px; background-color: #FFFFFF; padding:10px;">
					<div class="row">
						<div class="col-md-8">
							<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Pembayaran</b></h4>
							<div>
								Metode Pembayaran Yang Digunakan Via <label class="label label-warning text-white">{{$transaksi->ket_metodepembayaran}}</label> 
							</div>
						</div>
						<div class="col-md-4 text-right">
							<h3 style="margin:0px;"> Rp. {{number_format($transaksi->total_bayar,'0','','.') }} </h3>
							<h6 style="margin-bottom: 0px; margin-top: 5px; cursor:pointer;" data-toggle="modal" data-target="#modal_rincian" ><u>Lihat Rincian</u></h6>
							
						</div>
					</div>
				</div>

				@if($transaksi->status == '1')
					<div style="margin-top: 10px;">
						<button class="btn btn-primary" data-target="#modal_input" data-toggle="modal">Mulai Pengiriman</button>
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
	 		});
	 	

	     	function edit(id,level,status_aktif){
	     		$("#level_id").val(id);
	     		$("#level_edit").val(level);
	     		$("#status_aktif").val(status_aktif);
	     		$("#modal_edit").modal('show');

	     	}
     	</script>

     	<!-- modal profil kurir -->
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
			              
			              <h3 class="widget-user-username">{{ $transaksi->Pengiriman->Kurir->nama }}</h3>
			              <h5 class="widget-user-desc">Kurir</h5>
			            </div>
			            <div class="widget-user-image ">
			              <img class="img-circle" src="{{ asset('upload/images-100/'.$transaksi->Pengiriman->Kurir->foto) }}" alt="User Avatar">
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
			                    <h5 class="description-header">{{ $transaksi->Pengiriman->Kurir->no_hp }}</h5>
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


			<button class="btn btn-primary">Simpan</button>
		</form>
	@endcomponent



	
	

      
@endsection