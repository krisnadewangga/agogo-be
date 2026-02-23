@extends('layouts.app1')

@section('content')
    @component('components.card', ['title' => 'Lap. Reprint Struk', 
                                   'breadcumbs' => array(
                                                          array('judul' => 'Lap. Reprint Struk','link' => '#')
                                                        ) 
                                  ])
        <div class="card">
            <form method="POST" action="{{ route('cari_laporan_reprint_struk') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group @error('tanggal') has-error @enderror">
                            <label>Tanggal</label>
                            @error('tanggal')
                            <label class="control-label"><i class="fa fa-times-circle-o"></i> {{ $message }}</label>    
                            @enderror 
                            <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right datepicker" name="tanggal" autocomplete="off" value="{{ $input['tanggal'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Cari</button>
                            <a href="{{ route('lap_reprint_struk') }}" class="btn btn-warning">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top: 10px;">
            <div class="table-responsive">
                <table class="table table-bordered" id="table_struk">
                    <thead style="font-size:14px;">
                        <tr>
                        <th style="width: 5px;">No</th>
                        <th>No. Struk</th>
                        <th>Kasir</th>
                        <th>Tanggal Transaksi</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px;">
                        @foreach($data as $key)
                            <tr>
                                <td align="center"></td>
                                <td>{{ $key->no_transaksi }}</td>
                                <td>{{ $key->User->name }}</td>
                                <td>{{ $key->created_at->format('d/m/Y') }}</td>
                                <td>{{ $key->created_at->format('H:i:s') }}</td>
                                <td>
                                    <a href="javascript:reprint_struk({{ $key->id }})" class="btn btn-xs btn-info">
                                        <i class="fa fa-print"></i> Cetak
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script type="text/javascript">
            $(function(){
                $('.datepicker').datepicker({
                   format: 'dd/mm/yyyy',
                   autoclose: true,
                   endDate: '+0d'
                });

                var table_struk = $("#table_struk").DataTable({
                    "ordering": false
                });
                table_struk.on( 'order.dt search.dt', function () {
                    table_struk.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
            });

            function reprint_struk(id) {
                window.open('reprint_struk/' + id, '_blank');
            }
        </script>
    @endcomponent
@endsection