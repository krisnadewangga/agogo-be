@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Lap. User', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Lap. User','link' => '#')
                                                    	) 
                                  ])
  

      	<!-- Small boxes (Stat box) -->
	    <div class="row">
	        <div class="col-lg-4 col-xs-12">
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
	        <div class="col-lg-4 col-xs-12">
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
	        <div class="col-lg-4 col-xs-12">
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
	    </div>
      	<!-- /.row -->

        <div class="card" style="margin-top: 0px;">
        	
        	<div class="table-responsive" style="margin-top: 10px;">
				<table class="dataTables table  table-bordered">
					<thead style=" font-size:14px;">
						<tr>
							<th style="width: 5px;">No</th>
							<th>Waktu Regis</th>
							<th>Nama User</th>
							<th>Jenis Kelamain</th>
							<th>TGL Lahir</th>
							<th>No Hp</th>
							<th>Alamat</th>
							<th><center>Jenis User</center></th>
						</tr>
					</thead>
					<tbody style=" font-size:14px;">
						@foreach($user as $key)
							<tr>
								<td align="center"></td>
								<td>{{ $key->email_verified_at->format('d M Y H:i A') }}</td>
								<td>{{$key->name}}</td>
								<td>
									@if($key->DetailKonsumen->jenis_kelamin == '0')
										Laki-Laki
									@elseif($key->DetailKonsumen->jenis_kelamin == '1')
										Perempuan
									@else
										<label class="label label-warning">Belum Ditentukan</label>
									@endif
								</td>
								<td>
									@if(!empty($key->DetailKonsumen->tgl_lahir))
									{{ $key->DetailKonsumen->tgl_lahir->format('d M Y') }}
									@else
										<label class="label label-warning">Belum Ditentukan</label>
									@endif
								</td>
								<td>{{ $key->DetailKonsumen->no_hp }}</td>
								<td>{{ $key->DetailKonsumen->alamat }}</td>
								<td align="center">
									@if($key->DetailKonsumen->status_member == '1')
										<label class="label label-success">Member</label>
									@else
										<label class="label label-warning">Not Member</label>
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			
        </div>
    @endcomponent
@endsection