@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Pesanan', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Pesanan','link' => '#')
                                                    	) 
                                  ])
        <div class="card" style="margin-bottom: 10px;">
        	<form id="form-filter" method="POST" action="get_pesanan">
        		@csrf

    			<div class="row">
    			<div class="col-md-5">
    				<div class="form-group">
    					<label>Jenis Transaksi</label>
    					<select class="form-control" id="jenis_transaksi" name="jenis_transaksi" onchange="loadStatus()">
    						<option value="0">Semua</option>
    						<option value="1">Topup</option>
    						<option value="2">Bank Transfer</option>
    						<option value="3">Bayar Di Toko</option>
    					</select>
    				</div>
    			</div>
    			<div class="col-md-5">
    				<div class="form-group">
    					<label>Status Transaksi</label>
    					<select class="form-control" id="status_transaksi" name="status_transaksi">
    						
    					</select>
    				</div>
    			</div>
    			<div class="col-md-2">
    				<button class="btn btn-primary" style="width: 100%; margin-top: 25px;">Tampilkan</button>
    			</div>
    			</div>
    		</form>
        </div>
        <div class="card">
			<!-- <button class="btn btn-primary" data-toggle="modal" data-target="#modal_input">Create</button>
			<button class="btn btn-warning" data-toggle="modal" data-target="#modal_input">SetOngkir</button>
			<hr></hr> -->
			
			<div class="table-responsive" style="margin-top: 10px;" id="divTable">
				<div class="text-center bg-warning text-orange" style="padding:30px;">
                                          <i class='fa fa-4x fa-spinner fa-pulse fa-fw' ></i>
                                          <h5>Sedang Mempersiapkan Data</h5>
                                      </div>
			</div>
		</div>    
		<script type="text/javascript">
			$(document).ready(function(){
				loadStatus();
				loadTable();
				$("#form-filter").submit(function(e){
					e.preventDefault();
					loadTable();
				})

			})

			function loadStatus(){
				var jenis_transaksi = $("#jenis_transaksi").val();
				var html = `<option value="0">Aktif</option>`;
				if(jenis_transaksi == "0"){
					// <option value="7">Pesanan Expired</option>
					html += `<option value="6">Menunggu Transfer</option>
							 <option value="8">Menunggu Pengambilan</option>
							 <option value="1">Menunggu Pengiriman</option>
							 <option value="2">Sementara Pengiriman</option>
							 <option value="4">Pengajuan Pembatalan Pesanan</option>
							 
							 <option value="5">Pesanan Diterima</option>
							 <option value="3">Pesanan Yang Dibatalkan</option>`;
				}else if(jenis_transaksi == "1"){
					// <option value="7">Pesanan Expired</option>
					html += `<option value="1">Menunggu Pengiriman</option>
							 <option value="2">Sementara Pengiriman</option>
							 <option value="4">Pengajuan Pembatalan Pesanan</option>
							 
							 <option value="5">Pesanan Diterima</option>
							 <option value="3">Pesanan Yang Dibatalkan</option>`;
				}else if(jenis_transaksi == "2"){
					// <option value="7">Pesanan Expired</option>
					html += `<option value="6">Menunggu Transfer</option>
							 <option value="1">Menunggu Pengiriman</option>
							 <option value="2">Sementara Pengiriman</option>
							 <option value="4">Pengajuan Pembatalan Pesanan</option>
							 
							 <option value="5">Pesanan Diterima</option>
							 <option value="3">Pesanan Yang Dibatalkan</option>`;
				}else if(jenis_transaksi == "3"){
					 // <option value="7">Pesanan Expired</option>
					html += `<option value="1">Menunggu Pengambilan</option>
							 <option value="4">Pengajuan Pembatalan Pesanan</option>
							
							 <option value="5">Pesanan Diterima</option>
							 <option value="3">Pesanan Yang Dibatalkan</option>`;
				}

				$("#status_transaksi").html(html);
			}

			function loadTable(){
				var data = $("#form-filter").serialize();
				var isiTable = "";
				$.ajax({
					url : 'get_pesanan',
					type : 'POST',
					data : data,
					beforeSend:function(){
						 $('#divTable').html(`<div class="text-center bg-warning text-orange" style="padding:30px;">
                                          <i class='fa fa-4x fa-spinner fa-pulse fa-fw' ></i>
                                          <h5>Sedang Mempersiapkan Data</h5>
                                      </div>`);
					},success:function(msg){
						// console.log(msg);
						// console.log(data);
						var no = 1;
						$.each(msg,function(index,value){
							isiTable += `<tr>
											<td align='center'>`+no+`</td>
											<td>`+value.waktu_tampil+`</td>
											<td>`+value.no_transaksi+`</td>
											<td>`+value.nama+`</td>
											<td>`+value.jum_pesanan+` Pesanan</td>
											<td>Rp. `+value.total_bayar+`</td>
											<td align='center'>`+value.tampil_jt+`</td>
											<td align='center'>`+value.tampil_status+`</td>
											<td align='center'>
												<a href="transaksi/`+value.id+`">
													<button class="btn btn-warning btn-sm"><i class="fa fa-search"></i>
													</button>
												</a>
											</td>
									     </tr>`;
						 no++;
						})

						var bodiHtml = `<table class="dataTables table  table-bordered" id="listPesanan">
									<thead style=" font-size:14px;">
										<tr>
										<th style="width: 5px;">No</th>
										<th>Waktu </th>
										<th>No Transaksi</th>
										<th>Pemesan</th>
										<th ><center>Jumlah Pesanan</center></th>
										<th ><center>Total Bayar</center></th>
										<th ><center>Jenis Transaksi</center></th>
										<th ><center>Status</center></th>
										<th style="width: 50px; text-align: center;">Aksi</th>
										</tr>
									</thead>
									<tbody>
										`+isiTable+`
									
									</tbody>
								</table>`;
						$("#divTable").html(bodiHtml);
						$("#listPesanan").dataTable();
						
					}

				})
			}
		</script>                      
	@endcomponent
	
@endsection