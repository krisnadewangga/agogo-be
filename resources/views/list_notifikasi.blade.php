@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'List Notifikasi', 
								   'breadcumbs' => array(
                                                          array('judul' => 'List Notifikasi','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
						<th style="width: 5px;">No</th>
						<th style="width: 100px;">Waktu</th>
						<th style="width: 200px;">Dari</th>
						<th>Pesan</th>
						<th style="width: 50px;"><center>Aksi</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($notifikasi as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->created_at }}</td>
								<td>{{ $key->name }}</td>
								<td>{{ $key->isi }}</td>
								<td align="center">
									<a href="{{ route('transaksi.show', $key->judul_id) }}"><button class="btn btn-sm btn-info "><i class="fa fa-location-arrow"></i></button>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
        </div>

    @endcomponent
@endsection