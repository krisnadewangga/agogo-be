@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Master Set Tanggal', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Master Set Tanggal','link' => '#')
                                                    	) 
                                  ])
		<div class="card">
			

			<form method="post" action="{{ route('set_tanggal.store' ) }}"  style="display: inline">
			    {{ csrf_field() }}
				
				<button onclick="return confirm('Apakah Anda Yakin Untuk Set Tanggal ?')" class=' btn btn-primary '>Set Tanggal</button>
			</form>
			<hr></hr>

			@if (session('success'))
			 	@component("components.alert", ["type" => "success"])
					{{ session('success') }}
				@endcomponent
			@endif


			@if (session('error'))
			 	@component("components.alert_error", ["type" => "error"])
					{!! session('error') !!}
				@endcomponent
			@endif

			<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th>Tanggal</th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($tanggal as $key)
							<tr>
								<td align="center"></td>
								<td>{{ Carbon\Carbon::parse($key->tanggal)->format('d/m/Y') }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	@endcomponent
	
@endsection