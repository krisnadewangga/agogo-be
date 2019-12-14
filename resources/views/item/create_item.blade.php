@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Create Item', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Item','link' => '../item'),
                                                          array('judul' => 'Create','link' => '#')
                                                    	) 
                                  ])
        <div class="row" style="margin-bottom: 30px;" >
			<div class="col-md-12">
				<form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-md-12">
							
								<div style="background-color:#ffffff; padding:10px;">
								<h4><b>Data Item</b></h4>
								<div style="padding:20px;">
									<div class="form-group @error('nama_item') has-error @enderror">
										<label>Nama Item</label>
										<input type="text" name="nama_item" value="{{ old('nama_item') }}" class="form-control" placeholder="Masukan Nama Item">
										@error('nama_item')
								            <label class="control-label" for="inputError">
						                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
						                	</label>    
								        @enderror 
									</div>

									<div class="form-group @error('kategori') has-error @enderror">
										<label>Kategori</label>
						                <select id="select2Kat" class="form-control " style="width: 100%;" name="kategori" >
						                  <option></option>
						                  @foreach($kategori as $key)
						                  	<option value="{{ $key->id }}">{{ $key->kategori }}</option>
						                  @endforeach
						                </select>
						                @error('kategori')
								            <label class="control-label" for="inputError">
						                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
						                	</label>    
								        @enderror 
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group @error('harga') has-error @enderror">
												<label>Harga</label>
												<input type="text" name="harga" value="{{ old('harga') }}" class="form-control" placeholder="Masukan Harga Jual">
												<input type="hidden" name="margin" value="0" class="form-control" placeholder="Masukan Margin Keuntunga Dari Harga Jual" readonly >

												@error('harga')
										            <label class="control-label" for="inputError">
								                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
								                	</label>    
										        @enderror 
											</div>
										</div>
										<!-- <div class="col-md-6">
											<div class="form-group @error('margin') has-error @enderror">
												<label>Margin</label>
												<input type="text" name="margin" value="{{ old('margin') }}" class="form-control" placeholder="Masukan Margin Keuntunga Dari Harga Jual">
												@error('margin')
										            <label class="control-label" for="inputError">
								                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
								                	</label>    
										        @enderror 
											</div>
										</div> -->
									</div>

									<label>Stock Awal</label>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group @error('stock') has-error @enderror">
												
												<input type="text" name="stock" value="{{ old('stock') }}" class="form-control" placeholder="Masukan Stock Awal">
												@error('stock')
										            <label class="control-label" for="inputError">
								                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
								                	</label>    
										        @enderror 
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="text" name="" value="PCS" disabled class="form-control">
											</div>
										</div>
									</div>
									<div class="form-group @error('gambar') has-error @enderror ">
								        <label>Gambar Utama</label>
								        <input id="gambar" type="file"  name="gambar" >
								        @error('gambar')
								            <label class="control-label" for="inputError">
						                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
						                	</label>    
								        @enderror 
							        </div>
									
									
							
								</div>
								</div>	
							
						</div>
					</div>
					
					<div class="row" style="margin-top: 20px;">
						<div class="col-md-12">
							<div style="background-color:#ffffff; padding:10px;">
								<h4><b>Deskripsi Item</b></h4>
								<div style="padding:20px;">
									
								<!-- 	<div style="border:1px solid #CCC; padding:30px 20px 10px 20px; margin-bottom:10px;" >
										<div class="row">
											<div class="col-md-12">
												<div>
													<label>Varian Item</label>
												</div>
												<div style="margin-top: -5px;"> 
													Tambahkan Varian Rasa Apabila Item Memiliki Lebih Dari Satu Rasa	
												</div>
											
											</div>
											
										</div>
										
										<div id="form-varian" style="margin-top: 5px;">
											<table class="table" style="margin-bottom: 0px;">
												<tr>
													<td>Varian Rasa</td>
													<td>
														<div class="form-group @error('v_rasa') has-error @enderror">
															<textarea class="form-control" name="v_rasa">
																{{old('rasa')}}
															</textarea>
															*) Gunakan Koma Untuk Pemisah Setiap Rasa, Kosongkan Apabila Tidak Ingin Memasukan Varian Rasa
															@error('v_rasa')
													            <label class="control-label" for="inputError">
											                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
											                	</label>    
													        @enderror 
														</div>
													</td>
												</tr>
												
											</table>
										</div>
									</div> -->


									<div class="form-group @error('deskripsi') has-error @enderror" >
										<label>Masukan Deskripsi Yang Menggambarkan Item</label>
										<textarea name="deskripsi" class="form-control" style="height:75px;">
											{{ old('deskripsi') }}
										</textarea>
										@error('deskripsi')
								            <label class="control-label" for="inputError">
						                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
						                	</label>    
								        @enderror 
									</div>

								</div>
							</div>	
						</div>
					</div>

					<div class="row" style="margin-top: 20px;">
						<div class="col-md-12">
							<button class="btn btn-primary">Simpan</button><a href="#" class="btn btn-danger">Batal</a>
						</div>
					</div>
				</form>
			</div>
		</div>

		<script type="text/javascript">
			$(function (){
				CKEDITOR.replace('deskripsi');
				$('#select2Kat').select2({allowClear:true, placeholder: "Pilih Kategori"});

			});
			
		</script>
    @endcomponent
@endsection