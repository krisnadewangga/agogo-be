@extends('layouts.app1')

@section('content')
    @component('components.card', ['title' => 'Laporan Selisih Stok Barang', 
                                   'breadcumbs' => array(
                                                        array('judul' => 'Lap. Selisih Stok','link' => '#')
                                                      ) 
                                 ])
        <div class="card">
            <form method="POST" action="{{ route('cari_selisih_stock') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group @error('mt') has-error @enderror">
                            <label>Mulai Tanggal</label>
                            @error('mt')
                                <label class="control-label" for="inputError">
                                    <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                                </label>    
                            @enderror 
                            <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" id="mt" class="form-control pull-right datepicker" name="mt" autocomplete="off" value="{{ $input['mt'] }}" placeholder="Pilih Mulai Tanggal">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group @error('st') has-error @enderror">
                            <label>Sampai Tanggal</label>
                            @error('st')
                                <label class="control-label" for="inputError">
                                    <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                                </label>    
                            @enderror 
                            <div class="input-group date">
                              <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right datepicker" id="st" name="st" autocomplete="off" value="{{ $input['st'] }}" placeholder="Pilih Sampai Tanggal">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Jenis Selisih Tampilan</label>
                            <select name="jenis_selisih" class="form-control" id="jenis_selisih">
                                <option value="1" {{ ($input['jenis_selisih'] ?? '1') == '1' ? 'selected' : '' }}>Keduanya (Pagi & Malam)</option>
                                <option value="2" {{ ($input['jenis_selisih'] ?? '') == '2' ? 'selected' : '' }}>Selisih Pagi Saja</option>
                                <option value="3" {{ ($input['jenis_selisih'] ?? '') == '3' ? 'selected' : '' }}>Selisih Malam Saja</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Sort By</label>
                            <select name="sort_by" class="form-control" id="sort_by">
                                <option value="1" {{ ($input['sort_by'] ?? '1') == '1' ? 'selected' : '' }}>Kode Menu</option>
                                <option value="2" {{ ($input['sort_by'] ?? '') == '2' ? 'selected' : '' }}>Nama Item</option>
                                <option value="3" {{ ($input['sort_by'] ?? '') == '3' ? 'selected' : '' }}>Total Selisih Pagi</option>
                                <option value="4" {{ ($input['sort_by'] ?? '') == '4' ? 'selected' : '' }}>Total Selisih Malam</option>
                                <option value="5" {{ ($input['sort_by'] ?? '') == '5' ? 'selected' : '' }}>Total Harga</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                         <label>Opsi Sort</label>
                         <select name="opsi_sort" class="form-control" id="opsi_sort">
                            <option value="1" {{ ($input['opsi_sort'] ?? '1') == '1' ? 'selected' : '' }}>Kecil Ke Besar</option>
                            <option value="2" {{ ($input['opsi_sort'] ?? '') == '2' ? 'selected' : '' }}>Besar Ke Kecil</option>
                         </select>
                    </div>
                </div>
                <div style="margin-top: 10px;">
                    <button class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                    <a href="{{ route('lap_selisih_stock') }}" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</a>
                    <a href="javascript:export_pdf()" class="btn btn-success"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top: 10px;">
            <div class="table-responsive" style="margin-top: 10px;">
                <table class="table table-bordered table-striped" id="table_selisih_stock" width="100%">
                    <thead style="font-size:14px;">
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>Nama Item</th>
                            <th style="text-align: right;">Harga Item</th>
                            @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '2')
                                <th style="text-align: center;">Total Selisih Pagi</th>
                                <th style="text-align: right;">Total Rp. Pagi</th>
                            @endif
                            @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '3')
                                <th style="text-align: center;">Total Selisih Malam</th>
                                <th style="text-align: right;">Total Rp. Malam</th>
                            @endif
                            <th style="text-align: right;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px;">
                        @if(!empty($data['data']))
                            @foreach($data['data'] as $key => $row)
                                <tr>
                                    <td align="center"></td>
                                    <td class="nowrap">{{ $row->nama_item }}</td>
                                    <td class="nowrap text-right">{{ $row->harga }}</td>
                                    @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '2')
                                        <td class="nowrap text-center">
                                            <span class="badge {{ $row->total_selisih_pagi != 0 ? 'bg-yellow' : 'bg-green' }}">
                                                {{ $row->total_selisih_pagi }}
                                            </span>
                                        </td>
                                        <td class="nowrap text-right">{{ $row->total_nominal_pagi }}</td>
                                    @endif
                                    @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '3')
                                        <td class="nowrap text-center">
                                            <span class="badge {{ $row->total_selisih_malam != 0 ? 'bg-yellow' : 'bg-green' }}">
                                                {{ $row->total_selisih_malam }}
                                            </span>
                                        </td>
                                        <td class="nowrap text-right">{{ $row->total_nominal_malam }}</td>
                                    @endif
                                    <td class="nowrap text-right"><b>{{ $row->tampil_total_nominal }}</b></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    @if(!empty($data['data']))
                    <tfoot>
                        <tr style="font-size:14px; background-color: #f5f5f5;">
                            @php
                                $colSpan = 3;
                                if(($data['jenis_selisih'] ?? '1') == '1') $colSpan = 7;
                                elseif(($data['jenis_selisih'] ?? '1') == '2') $colSpan = 5;
                                elseif(($data['jenis_selisih'] ?? '1') == '3') $colSpan = 5;
                            @endphp
                            <td colspan="{{ $colSpan - 1 }}" class="text-right"><b>Grand Total :</b></td>
                            <td class="text-right"><b>Rp. {{ $data['grandTotalNominal'] }}</b></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <script type="text/javascript">
            $(function(){
                 $('.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    endDate: '+0d',
                 });

                 var table = $("#table_selisih_stock").DataTable({
                     "ordering": false
                 });

                 table.on('order.dt search.dt', function () {
                     table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                         cell.innerHTML = i + 1;
                     });
                 }).draw();
            });

            function export_pdf() {
                var tanggal = $("#mt").val();
                var tanggal1 = $("#st").val();
                var sort_by = $("#sort_by").val();
                var opsi_sort = $("#opsi_sort").val();
                var jenis_selisih = $("#jenis_selisih").val();

                if (tanggal != "" && tanggal1 != "") {
                    var pisah = tanggal.split('/');
                    var pisah1 = tanggal1.split('/');

                    var mt = pisah[2] + "-" + pisah[1] + "-" + pisah[0];
                    var st = pisah1[2] + "-" + pisah1[1] + "-" + pisah1[0];

                    window.open('{{ route("export_selisih_stock") }}?mt=' + mt + '&st=' + st + '&sort_by=' + sort_by + '&opsi_sort=' + opsi_sort + '&jenis_selisih=' + jenis_selisih, '_blank');
                } else {
                    alert("Maaf! Pastikan Mulai Tanggal & Sampai Tanggal Tidak Kosong");
                }
            }
        </script>
    @endcomponent
@endsection