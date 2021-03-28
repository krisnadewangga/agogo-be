@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Pesanan', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Pesanan','link' => '#')
                                                    	) 
                                  ])

    
		

        <div class="card" style="margin-bottom: 10px;" >
        	<form id="form-filter" method="POST" action="get_pesanan">
        		@csrf

    			<div class="row">
	    			<div class="col-md-6">
	    				<div class="form-group">
	    					<label>Jenis Transaksi</label>
	    					<select class="form-control" id="jenis_transaksi" name="jenis_transaksi" onchange="loadStatus()">
	    						<option value="0">Semua</option>
	    						<option value="1">Topup</option>
	    						<option value="2">Bank Transfer</option>
	    						<option value="3">Bayar Di Toko</option>
	    						<option value="4">COD</option>
	    					</select>
	    				</div>
	    			</div>
	    			<div class="col-md-6">
	    				<div class="form-group">
	    					<label>Status Transaksi</label>
	    					<select class="form-control" id="status_transaksi" name="status_transaksi">
	    						
	    					</select>
	    				</div>
	    			</div>
    			
    			</div>
    			<div class="row">
    				<div class="col-md-12">
    					<button class="btn btn-primary">Tampilkan</button>
    					<label class="btn btn-success" onclick="maps()">Maps</button>
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
				});

			})

			function loadStatus(){
				var jenis_transaksi = $("#jenis_transaksi").val();
				var html = `<option value="0">Aktif</option>`;
				if(jenis_transaksi == "0"){
					// <option value="7">Pesanan Expired</option>
					// <option value="8">Menunggu Pengambilan</option>
					html += `<option value="6">Menunggu Transfer</option>
							 <option value="1">Dikemas</option>
							 <option value="2">Dikirim</option>
							 <option value="5">Terima</option>
							 <option value="4">Pengajuan Pembatalan</option>
							 <option value="3">Dibatalkan</option>`;
				}else if(jenis_transaksi == "1"){
					// <option value="7">Pesanan Expired</option>
					html += `<option value="1">Dikemas</option>
							 <option value="2">Dikirim</option>
							 <option value="4">Dibatalkan</option>
							 
							 <option value="5">Terima</option>
							 <option value="3">Dibatalkan</option>`;
				}else if(jenis_transaksi == "2"){
					// <option value="7">Pesanan Expired</option>
					html += `<option value="6">Menunggu Transfer</option>
							 <option value="1">Dikemas</option>
							 <option value="2">Dikirim</option>
							
							 
							 <option value="5">Terima</option>
							 <option value="4">Pengajuan Pembatalan</option>
							 <option value="3">Dibatalkan</option>`;
				}else if(jenis_transaksi == "3"){
					 // <option value="7">Pesanan Expired</option>
					html += `<option value="1">Dikemas</option>
							 <option value="5">Terima</option>
							 <option value="4">Pengajuan Pembatalan</option>
							 <option value="3">Dibatalkan</option>`;
				}else if(jenis_transaksi == "4"){
					html += `<option value="1">Dikemas</option>
							 <option value="5">Terima</option>
							 <option value="4">Pengajuan Pembatalan</option>
							 <option value="3">Dibatalkan</option>`;
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
						console.log(msg);
						// console.log(data);
						var no = 1;
						$.each(msg,function(index,value){
							
							if(value.status == "3"){
						  		sambung_tombol = `<button  onclick="hapus_pesanan('`+value.id+`')" class=' btn btn-danger btn-sm btn_hapus_`+value.id+`'><i class='fa fa-trash'  ></i></button>`;
							}else{
								sambung_tombol = '';
							}

							isiTable += `<tr>
											<td align='center' class='nowrap'>`+no+`</td>
											<td  class='nowrap'>`+value.waktu_tampil+`</td>
											<td  class='nowrap'>`+value.tampil_waktu_kirim+`</td>
											<td  class='nowrap'>`+value.no_transaksi+`</td>
											<td  class='nowrap'>`+value.nama+`</td>
											<td  class='nowrap'>Rp. `+value.total_bayar+`</td>
											<td  class='nowrap' align='center'>`+value.tampil_jt+`</td>
											<td  class='nowrap' align='center'>`+value.tampil_status+`</td>
											<td  class='nowrap' align='center'>
												<a href="transaksi/`+value.id+`">
													<button class="btn btn-warning btn-sm"><i class="fa fa-search"></i>
													</button>
												</a>
												`+sambung_tombol+`
											</td>
									     </tr>`;
						 no++;
						});

						var bodiHtml = `<table class="dataTables table  table-bordered" id="listPesanan">
									<thead style=" font-size:14px;">
										<tr>
										<th style="width: 5px;">No</th>
										<th class='nowrap' ><center>Waktu Pemesanan</center></th>
										<th class='nowrap' ><center>Waktu Kirim / Ambil</center></th>
										<th class='nowrap'>No Transaksi</th>
										<th class='nowrap'>Pemesan</th>
										<th class='nowrap'><center>Total Bayar</center></th>
										<th class='nowrap'><center>Jenis Transaksi</center></th>
										<th class='nowrap'><center>Status</center></th>
										<th class='nowrap' style="width: 50px; text-align: center;">Aksi</th>
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

			function hapus_pesanan(id)
			{
				var konfir  = confirm('Apakah Anda Yakin ?');
				if(konfir){
					$.ajax({
						url : 'transaksi/'+id,
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						type : 'POST',
						data : '_method=delete',
						beforeSend:function(){
							$('.btn_hapus_'+id).html("<i class='fa fa-spinner fa-pulse fa-fw ' ></i>");
						},
						success:function(msg){
							if(msg.success == "1"){
								alert("Berhasil Hapus Pesanan");
								loadTable();
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

			function maps()
			{
                var jenis_transaksi = $("#jenis_transaksi").val();
                var status_transaksi = $("#status_transaksi").val();

                window.open('maps?jenis_transaksi='+jenis_transaksi+'&status_transaksi='+status_transaksi,'_blank');
            
			}
		</script>                      
	@endcomponent
	
@endsection