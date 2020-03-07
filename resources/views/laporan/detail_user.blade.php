@extends('layouts.app1')

@section('content')
        @component('components.card', ['title' => 'Detail User', 
								       'breadcumbs' => array(
                                                          array('judul' => 'Lap. User','link' => '../lap_user'),
                                                          array('judul' => $user->name,'link' => '#')

                                                    	) 
                                  ])
            
            @if (session('success'))
                @component("components.alert", ["type" => "success"])
                    {{ session('success') }}
                @endcomponent
            @endif
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-yellow">
                        <div class="widget-user-image">
                            @if(!empty($user->foto))
                                <img class="img-circle" src="{{ asset('upload/images-100/'.$user->foto) }}" alt="User Avatar">
                            @else
                                <img class="img-circle" src="{{ asset('assets/dist/img/user.png') }}" alt="User Avatar">

                            @endif
                           
                        </div>
                        <!-- /.widget-user-image -->
                        <h3 class="widget-user-username">{{ ucwords($user->name) }}</h3>
                        <h5 class="widget-user-desc">Konsumen</h5>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li>
                                    <a href="#" title="Jenis Kelamin">
                                        @if($user->DetailKonsumen->jenis_kelamin == '0')
                                            Laki-Laki
                                        @elseif($user->DetailKonsumen->jenis_kelamin == '1')
                                            Perempuan
                                        @else 
                                            <label class="label label-warning">Belum Diupdate</label>
                                        @endif
                                            
                                        <span class="pull-right  text-red" >
                                            <i class="fa  fa-intersex"></i>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" title="Tgl Lahir">
                                    
                                        
                                        @if(!empty($user->DetailKonsumen->tgl_lahir) )
                                            {{ $user->DetailKonsumen->tgl_lahir->format('d M Y') }}
                                        @else 
                                            <label class="label label-warning">Belum Diupdate</label>
                                        @endif
                                        <span class="pull-right  text-green" >
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" title="No HP" >{{ $user->no_hp }}
                                        <span class="pull-right  text-blue" >
                                            <i class='fa fa-phone text-blue'></i>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" title="Email">{{ $user->email }}
                                        <span class="pull-right  text-aqua" >
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                    </a>
                                </li>
                              
                            </ul>
                        </div>
                    </div>

                    <div class="row" >
                        <div class="col-md-12">
                            <div class="card">
                                <h5 style="margin:0">Saldo Saat Ini</h5>
                                <h1  style="margin:0">Rp {{ number_format($user->DetailKonsumen->saldo,'0','','.') }}</h1>
                                <hr style="margin-top:5px; margin-bottom:5px;"></hr>
                                <label class="label label-success">{{ $user->Transaksi->where('status','!=','3')->count() }} x</label><small> Belanja  </small> &nbsp; 
                                <label class="label label-danger">{{ $user->Transaksi->where('status','=','3')->count() }} x</label> <small>Batal Belanjaan </small>
                            </div>
                        </div>     
                    </div>
                    
                    <div class="row" style="margin-top:20px;">
                        <div class="col-md-12">
                            <div class="card">
                                <h5 style="margin:0">Status User Saat Ini</h5>
                                <div  class="@if($user->status_aktif == '1') bg-success text-green
                                             @else bg-danger text-red @endif" style="padding:10px;margin-top:5px; overflow:hidden; margin-bottom:5px;">
                                    <i class="fa @if($user->status_aktif == '1') fa-check 
                                                 @else fa-close @endif fa-3x"></i>
                                    <h1  style="margin:0" class="pull-right"> 
                                        @if($user->status_aktif == '1') Aktif
                                        @else Blokir @endif
                                    </h1>
                                    @if($user->status_aktif == '0')
                                        <hr style="margin-top:3px; margin-bottom:3px;" class="text-red"></hr>
                                        <small>Oleh : {{ $logBan->input_by }} </small> <br/> 
                                        <small>TGL : {{ $logBan->created_at->format('d M Y H:i A') }}
                                    @endif
                                </div>
                               
                                <a href="{{ route('blokir_user', $user->id ) }}" onclick="return confirm('Apakah Anda Yakin') ">
                                    <button class="btn  @if($user->status_aktif == '1') btn-danger 
                                                        @else btn-success @endif btn-flat " style="width:100%"> 
                                                        @if($user->status_aktif == '1') Blokir User
                                                        @else Buka Blokir @endif</button>
                                </a>
                            </div>
                        </div>     
                    </div>

                </div>
                <div class="col-md-9">
                   
                    <div class="card">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 style="margin:0; margin-bottom:5px;">Histori Belanja</h4>
                                
                            </div>
                            <div class="col-md-6 text-right">
                                <label class="label label-success">{{ $user->Transaksi->where('metode_pembayaran','=','1')->count() }} x</label> <small>TopUp</small> &nbsp;
                                <label class="label label-info">{{ $user->Transaksi->where('metode_pembayaran','=','2')->count() }} x</label> <small>Bank Transfer</small> &nbsp;
                                <label class="label label-warning">{{ $user->Transaksi->where('metode_pembayaran','=','3')->count() }} x</label> <small>Bayar Di Toko</small>
                            </div>
                        </div>
                        <hr style="margin-top:10px; margin-bottom:10px;"></hr>
                        <div class="table-responsive" style="margin-top: 30px; padding:0px 20px 0px 20px;">
                            <table class="dataTables table  table-bordered">
                                <thead style=" font-size:14px;">
                                    <tr>
                                        <th style="width: 5px;">No</th>
                                        <th  style="width: 110px;">Waktu Pesanan</th>
                                        <th style="width: 80px;">Total Bayar</th>
                                        <th style="width: 80px; text-align: center;"">Jml Pesanan</th>
                                        <th  style="width: 40px; text-align: center;"><center>Jenis <br/> Transaksi</center></th>
                                        <th style="width: 40px; text-align: center;"><center>Status</center></th>
                                        <th style="width: 40px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style=" font-size:14px;">
                                    @foreach($transaksi as $key)
                                        <tr>
                                            <td align="center"></td>
                                            <td>{{ $key->created_at->format('d M Y H:i A') }}</td>
                                         
                                            <td>Rp {{ number_format($key->total_bayar,'0','','.') }}</td>
                                            <td align="center">{{ $key->ItemTransaksi()->count() }} Pesanan</td>
                                            <td align="center">
                                                @if($key['metode_pembayaran'] == 1)
                                                    <span class="label label-success ">TopUp</span>
                                                @elseif($key['metode_pembayaran'] == 2)
                                                    <span class="label label-info">Bank Transfer</span>
                                                @elseif($key['metode_pembayaran'] == 3)
                                                    <span class="label label-warning">Bayar Di Toko</span>
                                                @endif
                                            </td>
                                            <td align="center">
                                                @if($key->status == '1')
                                                    @php
                                                        $waktu_skrang = strtotime(date('Y-m-d H:i:s'));
                                                        $batas_ambe = strtotime($key['waktu_kirim']);
                                                    @endphp
                                                    @if($waktu_skrang < $batas_ambe )
                                                        <label class="label label-info">Aktif</label>
                                                    @else
                                                        <label class="label label-danger">Expired</label>
                                                    @endif
                                                @elseif($key->status == '2')
                                                    <label class="label label-warning">Pengiriman</label>
                                                @elseif($key->status == '5')
                                                    <label class="label label-success">Selesai</label>
                                                @elseif($key->status=='3')
                                                    <label class="label label-danger">Dibatalkan</label>
                                                @endif
                                               
                                            </td>
                                            <td align="center">
                                                <a href="{{ route('transaksi.show', $key->id ) }}">
                                                    <button class="btn btn-warning btn-sm"><i class="fa fa-search"></i></button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent

@endsection