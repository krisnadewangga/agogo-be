@extends('layouts.app1')

@section('content')
    @component('components.card', ['title' => 'Dashboard', 'breadcumbs' => array() 
                                  ])
            
     
     
        <!-- /.row -->
     
      <div class="row">
        <div class="col-md-12">
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

  
         
        </div>

        </div>
        
      </div>
    @endcomponent
    <!-- <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    <script type="text/javascript">
      var bulan_sekarang = parseInt("<?= date('m') ?>");
      var tahun_sekarang = parseInt("<?= date('Y') ?>")
      $(function(){

          setBulan("1");
          setBulan1("1");
          setGrafik("1");
      });

      function setBulan1(set){
        var tahun_select = $("#tahun_grafik").val();
        if(tahun_select == tahun_sekarang){
          set = "1";
        }

        $.ajax({
          url : "get_bulan",
          data : "tahun="+tahun_select,
          beforeSend:function(){
            $("#loading_bulan_grafik").prop("class","label label-warning")
                                      .html(`<i class='fa fa-spinner fa-pulse fa-fw' ></i> Loading...`);
          },
          success :function(msg){
            html = '<option value="">All Bulan  </option>';
            $.each(msg, function( key,value ){
              if( set=="1"){
                if(key == bulan_sekarang){
                  selected = "selected";
                }else{
                  selected = "";
                }
              }else{
                 selected = "";
              }
              html +=`<option value=`+key+` `+selected+`>`+value+`</option>`;
            });
            $("#bulan_grafik").html(html);
            $("#loading_bulan_grafik").prop('class','').html("");
            // console.log(html);
          }
        });
      }


      function setGrafik(set){
         var tahun = $("#tahun_grafik").val();
        
        
         if(set == "1"){
          bulan = bulan_sekarang;
         }else if(set == "2"){
            setBulan1('');
            var bulan = "";
         }else{
            var bulan = $("#bulan_grafik").val();
         }

         if(bulan == null){
            bulan = "";
         }

         $.ajax({
              url : "set_grafik_penjualan",
              data : "tahun="+tahun+"&bulan="+bulan+"&item=",
              beforeSend: function(){
                  $('#chartid').html(`<div class="text-center bg-warning text-orange" style="padding:30px;">
                                <i class='fa fa-4x fa-spinner fa-pulse fa-fw' ></i>
                                <h5>Sedang Meload Grafik Pendapatan</h5>
                            </div>`);
              },success:function(msg){
                  // console.log(msg);
                   
                  if(msg.grafik == '1'){
                     $('#chartid').highcharts({
                          chart: {
                              type: 'line',
                              marginLeft: 80,
                              marginRight:70
                          },
                          title: {
                              text: msg.title,
                          },
                          subtitle: {
                              text: 'Source: AgogoBakery.com'
                          },
                          xAxis: {
                              // type: 'category',
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
                              title: {
                                  text: null
                              }
                              // ,
                              // min: 0,
                              // max: 4,
                              // scrollbar: {
                              //     enabled: true
                              // },
                              // tickLength: 250
                          },
                          yAxis: {
                            
                              title: {
                                  text: 'Quantity (Pcs)',
                                  align: 'high'
                              }
                          },
                          plotOptions: {
                              bar: {
                                  dataLabels: {
                                      enabled: true
                                  }
                              }
                          },
                          legend: {
                              enabled: true
                          },
                          credits: {
                              enabled: false
                          },
                          series:msg.data
                                  
                     });
                  } else if(msg.grafik == '2'){
                      $('#chartid').highcharts({
                          chart: {
                              type: 'line',
                              marginLeft: 80,
                              marginRight:70
                          },
                          title: {
                              text: msg.title,
                          },
                          subtitle: {
                              text: 'Source: AgogoBakery.com'
                          },
                          xAxis: {
                              type: 'category'
                              // ,
                              // min: 0,
                              // max: 4,
                              // scrollbar: {
                              //     enabled: true
                              // },
                              // tickLength: 250
                          },
                          yAxis: {
                            
                              title: {
                                  text: 'Quantity (Pcs)',
                                  align: 'high'
                              }
                          },
                          plotOptions: {
                              bar: {
                                  dataLabels: {
                                      enabled: true
                                  }
                              }
                          },
                          legend: {
                              enabled: true
                          },
                          credits: {
                              enabled: false
                          },
                          series:msg.data
                        
                      });
                  }  
                  
              }
         }); 
         
      }


      
      function setBulan(set){
        var tahun_select = $("#top_ten_tahun").val();
        if(tahun_select == tahun_sekarang){
          set = "1";
        }
        $.ajax({
          url : "get_bulan",
          data : "tahun="+tahun_select,
          beforeSend:function(){
            $("#loading_bulan_topten").show();
          },
          success :function(msg){
            html = '<option value="">All Bulan  </option>';
            $.each(msg, function( key,value ){
              if( set=="1"){
                if(key == bulan_sekarang){
                  selected = "selected";
                }else{
                  selected = "";
                }
              }else{
                 selected = "";
              }
              
              html +=`<option value=`+key+` `+selected+`>`+value+`</option>`;
            });

            $("#top_ten_bulan").html(html);
            $("#loading_bulan_topten").hide();
            // console.log(html);
          }
        });
      }

      function getTopTen(set){

        var tahun = $("#top_ten_tahun").val();
        if(set == "1"){
          bulan = bulan_sekarang;
        }else if(set == "2"){
          setBulan('');
          var bulan = $("#top_ten_bulan").val();
        }else{
          var bulan = $("#top_ten_bulan").val();
        }
       

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
            // console.log(msg);
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
    </script> -->
@endsection
