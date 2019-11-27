@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'List TopUp Saldo', 
								   'breadcumbs' => array(
                                                          array('judul' => 'TopUp Saldo','link' => '#'),
                                                          array('judul' => 'List','link' => '#')
                                                    	) 
                                  ])
    	   <div class="card">
				<a href="{{route('topup_saldo.index')}}"><button class="btn btn-flat btn-default" data-toggle="modal" data-target="#modal_input">Create</button></a><button class="btn btn-flat btn-primary">List Topup</button></a>
				<hr></hr>

				<div class="table-responsive" style="margin-top: 10px;">
					<table class="dataTables table  table-bordered">
						<thead style=" font-size:14px;">
							<tr>
							<th style="width: 5px;"><center>No</center></th>
							<th>Waktu</th>
							<th>Nama</th>
							<th>No Hp</th>
							<th>Alamat</th>
							<th ><center>Nominal</center></th>
							</tr>
						</thead>
						<tbody style=" font-size:14px;">
							@foreach($list_topup as $key)
								<tr>
									<td align="center"></td>
									<td>{{ $key->created_at->format('d M Y H:i A') }}</td>
									<td>{{ $key->User->name }}</td>
									<td>{{ $key->User->DetailKonsumen->no_hp }}</td>
									<td>{{ $key->User->DetailKonsumen->alamat }}</td>
									<td align="center">Rp {{ number_format($key->nominal,'0','','.') }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>        
    @endcomponent

@endsection