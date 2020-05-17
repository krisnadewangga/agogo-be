 <div class="tab-pane" id="tab_3">
				              	<table class="table-galeri"  style="margin-top: 10px;">
			              			<tr>
			              				<td rowspan="4">
			              					<img src="{{ asset('upload/images-400/'.$gambarUtama->gambar ) }}" height="100">
			              				</td>
			              			</tr>
			              			<tr>
			              				<td>Item</td>
			              				<td>:</td>
			              				<td>{{ $item->nama_item }}</td>
			              			</tr>
			              			<tr>
			              				<td>Kategori</td>
			              				<td>:</td>
			              				<td>{{ $item->Kategori->kategori }}</td>
			              			</tr>
			              		
			              			<tr>
			              				<td colspan="3">
			              					<!-- <button class="btn btn-primary btn-sm " data-target="#modal_tambah_stock" data-toggle="modal" style="width: 100%;">Tambah Stock</button> -->
			              					<div class="bg-warning text-orange" style="padding:10px;">
			              						 Untuk Penambahan Stock Sillahkan Gunakan Produksi Yang Di React Dekstop
			              					</div>
			              				</td>
			              			</tr>
			              		</table>

			              		<hr style="margin:20px 0 20px 0"></hr>
			               	 	<table class="dataTables table  table-bordered">
									<thead style=" font-size:14px;">
										<tr>
										<th style="width: 5px;">No</th>
										<th>Tanggal</th>
										<th>Jumlah</th>
										<th>InputBy</th>
										@if(Auth::User()->level_id == '1')
											<th style="width: 100px; text-align: center;">Aksi</th>
										@endif
										</tr>
									</thead>
									<tbody style=" font-size:14px;">
										@foreach($stocker as $keyStocker)
											<tr>
												<td align="center"></td>
												<td>{{ $keyStocker->created_at->format("d M Y H:i A") }}</td>
												<td>{{ $keyStocker->jumlah }} PCS</td>
												<td>{{ $keyStocker->input_by }} </td>
												@if(Auth::User()->level_id == '1')
													<td align="center">
														<form method="post" action="{{ route('hapus_stock', $keyStocker['id'] ) }}"  style="display: inline">
								       						{{ csrf_field() }}
								       						<input type="hidden" name="_method" value="delete" />
								       						<button onclick="return confirm('apa anda yakin ?')" class=' btn btn-danger btn-sm'><i class='fa fa-trash'  ></i></button>
								       					</form>
													</td>
												@endif
											</tr>
										@endforeach
									</tbody>
								</table>
			              </div>