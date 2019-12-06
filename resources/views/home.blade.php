@extends('layouts.app1')

@section('content')
    @component('components.card', ['title' => 'Dashboard', 'breadcumbs' => array() 
                                  ])
            
     
     
        <!-- /.row -->
     
      <div class="row">
        <div class="col-md-8">
          <div class="row">
            <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3>{{ $dashboard_k['pesanan'] }}</h3>

                      <p>Pesanan</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-bag"></i>
                    </div>
                    
                  </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box bg-yellow">
                  <div class="inner">
                    <h3>{{ $dashboard_k['pengiriman'] }}</h3>

                    <p>Pengiriman</p>
                  </div>
                  <div class="icon">
                    <i class="ion ion-jet"></i>
                  </div>
                 
                </div>
              </div>
              <!-- ./col -->
              <div class="col-lg-4 col-xs-4">
                <!-- small box -->
                <div class="small-box  bg-green">
                  <div class="inner">
                    <h3>{{ $dashboard_k['total_p'] }}</h3>

                    <p>Terselesaikan</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-check-square-o"></i>
                  </div>
                
                </div>
              </div>
         
          </div>

          <div class="row">
              <div class="col-md-12">
                   <div class="card table-responsive" >
                        <div class="row" >
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                  <div class="form-group">
                                    <label  class="col-sm-2 control-label">Tahun</label>

                                    <div class="col-sm-10">
                                      <select class="form-control" id="tahun_grafik" onchange="setGrafik()">
                                        @for($a=$tahun->min_tahun; $a<=$tahun->max_tahun; $a++)
                                          <option value="{{ $a }}"  @if($a == $tahunNow) selected @endif >{{ $a }}</option>
                                        @endfor
                                      </select>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    
                        
                        <div id="chartid"  style="min-width: 310px; height: auto; margin: 0;"></div>
                   </div>
              </div>
          </div>
         
        </div>

        <div class="col-md-4">
          <div class="card">
            <h4 style="margin:0">10 Item Terlaris</h4>

            
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control" id="top_ten_tahun" onchange="getTopTen()">
                        @for($i=$tahun->min_tahun; $i<=$tahun->max_tahun; $i++)
                          <option value="{{ $i }}"  @if($i == $tahunNow) selected @endif >{{ $i }}</option>
                        @endfor
                    </select>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="pull-left" style="margin-top: -20px;" id="loading_bulan_topten" hidden>
                    <label class='label label-warning'><i class='fa fa-spinner fa-pulse fa-fw' ></i> Loading...</label>
                  </div>
                  <div class="form-group">

                    <select class="form-control" id="top_ten_bulan" onchange="getTopTen()">
                      <option>All Bulan</option>
                    </select>

                  </div>
              </div>
            </div>
           
            
            <div class="table-responsive" style="margin-top: 0px;">
              <table class="table table-bordered">
                <thead>
                   <th style="width: 10px;">No</th>
                   <th>Item</th>
                   <th style="width: 20px;">Terjual</th>
                </thead>
                <tbody id="tbl_top_ten">
                  @php $no=1 @endphp
                  @foreach($top_ten as $key)
                    <tr>
                      <td align="center">{{ $no++ }}</td>
                      <td>{{ $key->nama_item }}</td>
                      <td>
                         @if(!empty($key->total_belanja) )
                            {{ $key->total_belanja }} PCS
                         @else
                            0 PCS
                         @endif
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript">
      $(function(){
          
          setBulan();
          setGrafik();

      });

      function setGrafik(){
         var tahun_grafik = $("#tahun_grafik").val();
         $.ajax({
            url : "set_grafik",
            data : "tahun="+tahun_grafik,
            beforeSend:function(){
               $('#chartid').html(`<div class="text-center bg-warning text-orange" style="padding:30px;">
                                      <i class='fa fa-4x fa-spinner fa-pulse fa-fw' ></i>
                                      <h5>Sedang Meload Grafik Pendapatan</h5>
                                  </div>`);
            },
            success:function(msg){
              $('#chartid').highcharts({
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Grafik Pendapatan Selama Tahun '+tahun_grafik
                  },
                  subtitle: {
                      text: 'Sumber: AgogoBakery.com'
                  },
                  xAxis: {
                      categories: [
                          'Jan',
                          'Feb',
                          'Mar',
                          'Apr',
                          'May',
                          'Jun',
                          'Jul',
                          'Aug',
                          'Sep',
                          'Oct',
                          'Nov',
                          'Dec'
                      ],
                      crosshair: true
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: 'Penghasilan'
                      }
                  },
                  tooltip: {
                      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                      pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                          '<td style="padding:0"><b>Rp {point.y}</b></td></tr>',
                      footerFormat: '</table>',
                      shared: true,
                      useHTML: true
                  },
                  plotOptions: {
                      column: {
                          pointPadding: 0.2,
                          borderWidth: 0
                      }
                  },
                  series:msg
              });
            }
         });
         
      }
      function setBulan(){
        var tahun_select = $("#top_ten_tahun").val();
        $.ajax({
          url : "get_bulan",
          data : "tahun="+tahun_select,
          beforeSend:function(){
            $("#loading_bulan_topten").show();
          },
          success :function(msg){
            html = '<option value="">All Bulan  </option>';
            $.each(msg, function( key,value ){
              html +=`<option value=`+key+`>`+value+`</option>`;
            });
            $("#top_ten_bulan").html(html);
            $("#loading_bulan_topten").hide();
            // console.log(html);
          }
        });
      }

      function getTopTen(){
        var bulan = $("#top_ten_bulan").val();
        var tahun = $("#top_ten_tahun").val();
        $.ajax({
          url : "get_top_ten",
          data : "tahun="+tahun+"&bulan="+bulan,
          beforeSend:function(){
            $("#tbl_top_ten").html(`
                          <tr>
                            <td colspan='3' align='center'>
                              <label class='label label-warning'><i class='fa fa-spinner fa-pulse fa-fw' ></i>&nbsp; Loading...</label>
                            </td>
                          </tr>
                          `);
          },
          success :function(msg){
            console.log(msg);
            no = 1;
            html = "";
            $.each(msg, function( key,value ){
              if(value.total_belanja == null){
                tb = "0 PCS";
              }else{
                tb = value.total_belanja+" PCS";
              }
              html += `
                      <tr>
                        <td>`+no+`</td>
                        <td>`+value.nama_item+`</td>
                        <td>`+tb+`</td>
                      </tr>
                      `;
              no++;
            });
            $("#tbl_top_ten").html(html);
          }
        });
      }
    </script>
@endsection
