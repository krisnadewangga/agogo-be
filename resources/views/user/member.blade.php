@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Member', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Member','link' => '#')
                                                    	) 
                                  ])
        @if (session('success'))
		 	@component("components.alert", ["type" => "success"])
				{{ session('success') }}
			@endcomponent
		@endif

        <div class="card" style="margin-top: 0px;">

        	<div class="row " style="margin-left:-5px;">
        		<div class="col-md-3 col-sm-4 col-xs-4 col-lg-3" style="padding:5px; ">
        			<label class="btn btn-block btn-social btn-info">
		                <i class="fa fa-users"></i> {{ $total_user }} Total Konsumen 		             
		            </label>
        		</div>	
        		<div class="col-md-3 col-sm-4 col-xs-4 col-lg-3" style="padding:5px; ">
        			<label class="btn btn-block btn-social btn-success">
		                <i class="fa fa-users"></i> {{ $total_user_aktif }} Konsumen Aktif
		             </label>
        		</div>	
        		<div class="col-md-3 col-sm-4 col-xs-4 col-lg-3" style="padding:5px;">
        			<label class="btn btn-block btn-social btn-danger">
		                <i class="fa fa-users"></i> {{ $total_user_diblokir }} Konsumen Diblokir
		             </label>
        		</div>	
        	</div>
    		
    		
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th style="width:130px;" class="nowrap"> Waktu Regis</th>
							<th class="nowrap">Nama Member</th>
							<th style="width:90px;" class="nowrap"><center>Saldo</center></th>
							<th style="width:80px;" class="nowrap"><center>Total Belanja</center></th>
							<th style="width:80px;" class="nowrap"><center>Batal Belanja</center></th>
							<th style="width:50px;" class="nowrap"><center>Status</center></th>
							<th style="width:80px;" class="nowrap"><center>Aksi</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($user as $key)
							<tr>
								<td align="center"></td>
								<td class="nowrap">{{ $key->email_verified_at->format('d M Y H:i A') }}</td>
								<td class="nowrap">{{$key->name}}</td>
								<td align="right" class="nowrap">Rp {{ number_format($key->DetailKonsumen->saldo,'0','','.') }}</td>
								<td align="center" class="nowrap">{{ $key->total_belanja }}</td>
								<td align="center" class="nowrap">{{ $key->batal_belanja }}</td>
								<td align="center" class="nowrap">
									@if($key->status_aktif == '1')
										<label class="label label-success">Aktif</label>
									@else
										<label class="label label-danger">Diblokir</label>
									@endif
								</td>
								<td align="center" class="nowrap">
									<a href="{{ route('detail_user', ['id' => $key->id,'status_member' => $key->DetailKonsumen->status_member]) }}">
										<button class="btn btn-warning btn-sm"><i class="fa fa-search"></i></button>
									</a>
									<form method="post" action="{{ route('hapus_user', $key['id'] ) }}"  style="display: inline">
			       						{{ csrf_field() }}
			       						<input type="hidden" name="_method" value="delete" />
			       						<button onclick="return confirm('apa anda yakin ?')" class=' btn btn-danger btn-sm'><i class='fa fa-trash'  ></i></button>
			       					</form>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			
        </div>
     
     @endcomponent
@endsection