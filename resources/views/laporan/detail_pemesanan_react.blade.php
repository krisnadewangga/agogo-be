@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Detail Transaksi', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Pesanan','link' => '../transaksi'),
                                                          array('judul' => $transaksi->no_transaksi,'link' => '#')

                                                    	) 
                                  ])
      
        <div class="row" style="margin-bottom: 20px; margin-top: 0px;">
			
			<div class="col-md-12">
				<div style="background-color: #FFFFFF; border:1px solid #CCC; border-top:none; border-left:none; border-right: none;">
					<div class="row">
						<div class="col-md-12">
							<div  style="padding:10px;">
								<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Daftar Item</b></h4>
								<div>Daftar Item Yang Dibeli </div>
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
					
				</div>
			
				<!-- PEMBAYARAN -->
				<div class="bg-white" style="margin-top: 10px; background-color: #FFFFFF; padding:10px;">
					<div class="row">
						<div class="col-md-8">
							<h4 style="margin-top: 5px; margin-bottom: 5px;"><b>Total Transaksi</b></h4>
							<div>
								Kasir Yang Bertugas : {{$transaksi->User->name }} | TGL : {{ $transaksi->created_at->format('d/m/Y h:i A') }}

								
							</div>
						</div>
						<div class="col-md-4 text-right">
							
								<h3 style="margin:0px;"> Rp. {{number_format($transaksi->total_bayar,'0','','.') }} </h3>

								<h6 style="margin-bottom: 0px; margin-top: 5px; cursor:pointer;" >
									<u  data-toggle="modal" data-target="#modal_rincian">Lihat Rincian</u>
								</h6>
							
						</div>
					</div>
				</div>
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
     	</script>

    
    @endcomponent

    @component("components.modal", ["id" => "modal_rincian" ,"kop_modal" => "Rincian Belanja"])
		<div class="row">
			<div class="col-md-6 col-xs-6">
				Total Transaksi
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{number_format($transaksi->total_transaksi,'0','','.')}}
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6 col-xs-6">
				Uang Muka
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{ number_format($transaksi->Preorder->uang_muka,'0','','.') }}
			</div>
		</div>

		<hr></hr>
		<h4><u>Pelunasan</u></h4>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6 col-xs-6">
				Sisa Bayar
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{ number_format($transaksi->Preorder->sisa_bayar,'0','','.') }}
			</div>
		</div>

		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6 col-xs-6">
				Uang Dibayar
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{ number_format($transaksi->Preorder->uang_dibayar,'0','','.') }}
			</div>
		</div>

		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6 col-xs-6">
				Uang Kembali
			</div>
			<div class="col-md-6 text-right col-xs-6">
				Rp {{ number_format($transaksi->Preorder->uang_kembali,'0','','.') }}
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

		
	@endcomponent

      
@endsection