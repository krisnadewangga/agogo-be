@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'List Promo', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Setup Promo','link' => 'setup_promo'),
                                                          array('judul' => 'List Promo Selesai','link' => '#')
                                                    	) 
                                  ])
    	<div class="card">
			<a href="{{ route('setup_promo.index') }}"><button class="btn bg-success text-green btn-flat "><< Kembali Ke Setup Promo</button></a>
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
						<th><center>Gambar</center></th>
						<th><center>Berlaku Sampai</center></th>
						<th><center>Input By</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($promo as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->updated_at->format('d M Y H:i A') }}</td>
								<td>{{ $key->judul }}</td>
								<td align="center"><a href="upload/images-700/{{ $key->gambar }}" target="_blank" title="Lihat Gambar"><button class="btn-warning btn btn-sm" ><i class="fa fa-image"></i></button></a></td>
								<td align="center">{{ $key->berlaku_sampai->format('d M Y') }}</td>
								<td align="center">
									{{ $key->dibuat_oleh }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>                           
    @endcomponent
    

    

	
@endsection