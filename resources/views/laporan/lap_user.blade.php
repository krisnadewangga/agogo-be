@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. User', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. User','link' => '#')
                                                    	) 
                                  ])
  

      	<!-- Small boxes (Stat box) -->
	    <div class="row">
	        <div class="col-lg-3 col-xs-12">
	          <!-- small box -->
	          <div class="small-box bg-aqua" style="padding:10px;">
	            <div class="inner">
	              <h3>{{ $total_user }}</h3>

	              <p>Total User</p>
	            </div>
	            <div class="icon">
	              <i class="fa fa-users"></i>
	            </div>
	          
	          </div>
	        </div>
	        <!-- ./col -->
	        <div class="col-lg-3 col-xs-12">
	          <!-- small box -->
	          <div class="small-box bg-green" style="padding:10px;">
	            <div class="inner">
	              <h3>{{ $total_member }}</h3>

	              <p>Member</p>
	            </div>
	            <div class="icon">
	              <i class="fa fa-user"></i>
	            </div>
	          
	          </div>
	        </div>
	        <!-- ./col -->
	        <div class="col-lg-3 col-xs-12">
	          <!-- small box -->
	          <div class="small-box bg-yellow" style="padding:10px;">
	            <div class="inner">
	              <h3>{{ $total_not_member }}</h3>
	              <p>Not Member</p>
	            </div>
	            <div class="icon">
	              <i class="fa fa-user"></i>
	            </div>
	           
	          </div>
	        </div>

			 <!-- ./col -->
			 <div class="col-lg-3 col-xs-12">
	          <!-- small box -->
	          <div class="small-box bg-red" style="padding:10px;">
	            <div class="inner">
	              <h3>{{ $total_blokir }}</h3>
	              <p>Di blokir</p>
	            </div>
	            <div class="icon">
	              <i class="fa fa-user"></i>
	            </div>
	           
	          </div>
			 </div>
	    </div>
      	<!-- /.row -->

        <div class="card" style="margin-top: 0px;">
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th style="width:130px;"> Waktu Regis</th>
							<th>Nama User</th>
							<th style="width:90px;"><center>Saldo</center></th>
							<th style="width:80px;"><center>Total <br/> Belanja</center></th>
							<th style="width:80px;"><center>Batal <br/> Belanja</center></th>
							<th style="width:80px;"><center>Jenis <br/> User</center></th>
							<th style="width:50px;"><center>Status</center></th>
							<th style="width:80px;"><center>Aksi</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($user as $key)
							<tr>
								<td align="center"></td>
								<td >{{ $key->email_verified_at->format('d M Y H:i A') }}</td>
								<td>{{$key->name}}</td>
								<td align="right">Rp {{ number_format($key->DetailKonsumen->saldo,'0','','.') }}</td>
								<td align="center">{{ $key->total_belanja }}</td>
								<td align="center">{{ $key->batal_belanja }}</td>
								<td align="center">
									@if($key->DetailKonsumen->status_member == '1')
										<label class="label label-success">Member</label>
									@else
										<label class="label label-warning">Not Member</label>
									@endif
								</td>
								<td align="center">
									@if($key->status_aktif == '1')
										<label class="label label-success">Aktif</label>
									@else
										<label class="label label-danger">Diblokir</label>
									@endif
								</td>
								<td align="center">
									<a href="{{ route('detail_user', $key->id) }}">
										<button class="btn btn-warning btn-sm"><i class="fa fa-search"></i></button>
									</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			
        </div>
    @endcomponent
@endsection