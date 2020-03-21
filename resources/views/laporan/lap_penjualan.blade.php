@extends('layouts.app1')

@section('content')
	     <div class="content">
             <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <h3 style="margin-top: 0px;">Lap. Penjualan</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div style="margin-top: 0px;">
                         <button class="btn btn-primary btn-flat" onclick="page(0)" id="btn-grafik" >Grafik</button><button class="btn btn-flat btn-default" id="btn-data" onclick="page(1)" >Table</button>
                     </div>
                 </div>
             </div>
            

             <div class="row">
                <div class="col-md-12">
                    <div class="card" style="padding-bottom: 5px;">
                        <input type="text" id="page" value="0" readonly hidden>

                        <div class="row" >
                            <div class="col-md-4">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label  class="col-sm-2 control-label">Tahun</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="top_ten_tahun" onchange="getTopTen(2)" style="width: 100%;">
                                                @for($a=$tahun->min_tahun; $a<=$tahun->max_tahun; $a++)
                                                    <option value="{{ $a }}"  @if($a == $tahunNow) selected @endif >{{ $a }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             
                            <div class="col-md-4">
                                <div class="form-horizontal">
                                    <div class="pull-left" style="margin-top: -20px; margin-left: 60px;" id="loading_bulan_topten" hidden>
                                        <label class='label label-warning'><i class='fa fa-spinner fa-pulse fa-fw' ></i> Loading...</label>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-sm-2 control-label">Bulan</label>

                                        <div class="col-sm-10">
                                            <select class="form-control" id="top_ten_bulan" onchange="getTopTen()">
                                                <option value="">All Bulan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-horizontal">
                                    
                                    <div class="form-group">
                                        <label  class="col-sm-2 control-label">Item</label>

                                        <div class="col-sm-10">
                                            <select class="form-control " id="top_ten_item" onchange="getTopTen()"  multiple="multiple">
                                                @foreach($item as $key)
                                                    <option value="{{ $key->id }}">{{ $key->nama_item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        
                        
                    </div>
                </div>
             </div>
            <div class="card" style="margin-top: 10px;">
                
                    <div id="chartid"  style="height: auto; min-width: 920px; max-width: 1200px; margin: 0 auto">
                        
                    </div> 
                    
                    <div id="forTable" >
                        
                    </div> 
            </div>
        </div>
        <script src="https://code.highcharts.com/stock/highstock.js"></script>
        <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>

        <script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.bootstrap.min.css"></style>
       
       <script type="text/javascript">
            $(function(){
                setBulan();
                getTopTen();
                $('#top_ten_item').select2({allowClear:true, placeholder: "All Item"});

            });
            function page(page){
                var pageS = $("#page").val();
                $("#page").val(page);

                if(page == 1 && pageS != 1){
                    $("#btn-grafik").prop("class","btn btn-default btn-flat");
                    $("#btn-data").prop("class","btn btn-primary btn-flat");
                    getTopTen();
                }else if(page == 0 && pageS != 0){
                    $("#btn-grafik").prop("class","btn btn-primary btn-flat");
                    $("#btn-data").prop("class","btn btn-default btn-flat");
                    getTopTen();
                }
                
            }

            function getTopTen(stat){
                var tahun = $("#top_ten_tahun").val();
                var bulan = $("#top_ten_bulan").val();
                var item = $("#top_ten_item").val();
                var page = $("#page").val();

                if(stat == "2"){
                    setBulan();
                    var bulan = "";
                }
                
                if(item == null){
                    item = "";
                }
                // else{
                //     // var jum = item.length;
                //     // if(jum > 3){
                //     //     alert("Maksiaml Pilih 3 Item");
                //     //     $("#top_ten_item").val(null).trigger('change');    
                //     //     getTopTen();               
                //     //  }
                // }  

                if(page == 0){
                    $('#forTable').html("");
                    $.ajax({
                        url : "set_grafik_penjualan",
                        data : "tahun="+tahun+"&bulan="+bulan+"&item="+item,
                        beforeSend: function(){
                            $('#chartid').html(`<div class="text-center bg-warning text-orange" style="padding:30px;">
                                          <i class='fa fa-4x fa-spinner fa-pulse fa-fw' ></i>
                                          <h5>Sedang Meload Grafik Pendapatan</h5>
                                      </div>`);
                        },success:function(msg){
                            console.log(msg);
                             
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
                               
                            }else if(msg.grafik == '2'){
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
                            }else if(msg.grafik == '3'){
                                console.log(msg.data);
                                $('#chartid').highcharts({
                                    chart: {
                                        type: 'bar',
                                        marginRight:80
                                    },
                                    title: {
                                        text: msg.title
                                    },
                                    subtitle: {
                                        text: 'Source: AgogoBakery.com'
                                    },
                                    xAxis: {
                                        type: 'category'
                                    },
                                    yAxis: {
                                        min: 0,
                                        title: {
                                            text: 'Quantity (Pcs)',
                                            align: 'high'
                                        },
                                        labels: {
                                            overflow: 'justify'
                                        }
                                    },
                                    tooltip: {
                                        valueSuffix: ' Pcs'
                                    },
                                    plotOptions: {
                                        bar: {
                                            dataLabels: {
                                                enabled: true
                                            }
                                        }
                                    },
                                    legend: {
                                        enabled:false,
                                        layout: 'vertical',
                                        align: 'right',
                                        verticalAlign: 'top',
                                        x: -40,
                                        y: 80,
                                        floating: true,
                                        borderWidth: 1,
                                        backgroundColor:
                                            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                                        shadow: true
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    series: msg.data
                                });
                            }
                            
                        }
                    }); 
                }else{
                    $('#chartid').html("");
                    $.ajax({
                        url : "set_data_penjualan",
                        data : "tahun="+tahun+"&bulan="+bulan+"&item="+item,
                        beforeSend: function(){
                            $('#forTable').html(`<div class="text-center bg-warning text-orange" style="padding:30px;">
                                          <i class='fa fa-4x fa-spinner fa-pulse fa-fw' ></i>
                                          <h5>Sedang Meload Data Penjualan</h5>
                                      </div>`);

                        },success:function(msg){
                            console.log(msg);
                            tableHeaders = "";
                            subHeaders = "";
                            dataTable = "";



                            // if(msg.table == "1"){
                                $.each(msg.columns, function(i, val){
                                        subHeaders += "<th>" + val + "</th>";
                                });
                                tableHeaders += `<tr>
                                                     <th style="width: 5px;" rowspan='2'>No</th>
                                                     <th rowspan='2' style='width:150px;'>Item</th>
                                                     <th colspan='`+msg.columns.length+`'><center>`+msg.kopHeader+`</center></th>
                                                     <th rowspan='2'><center>Total</center></th>
                                                </tr>
                                                <tr>`+subHeaders+`</tr>`;
                               
                                noD = 1;
                                $.each(msg.data, function(i,val){
                                    
                                    total = 0;
                                    dataTable += `<tr>
                                                    <td align='center'>`+noD+`</td>
                                                    <td>`+val.nama_item+`</td>`;
                                                    for(a=1; a<=msg.columns.length; a++){
                                                        if(val.jumlah[a-1] > 0){
                                                            varClass = "bg-green";
                                                        }else{
                                                            varClass = "";
                                                        }
                                                        dataTable +=`<td align='center' class='`+varClass+`'>`+val.jumlah[a-1]+`</td>`;
                                                        total += val.jumlah[a-1];
                                                    }
                                    dataTable += `<td align='center'>`+total+` Pcs</td></tr>`;
                                noD++;
                                });
                            // }
                               

                            html = `<div class="text-center">
                                        <h4><u><b>`+msg.kop+`</b></u></h4>
                                    </div>
                                        <table id="dataTables1" class='dataTables table table-bordered table-striped' style='width:100%;'>
                                            <thead>
                                                `+tableHeaders+`
                                            </thead>
                                            <tbody>
                                                `+dataTable+`
                                            </tbody>
                                        </table>
                                 `;


                            $("#forTable").empty();
                            $("#forTable").append(html);
                            
                            $("#dataTables1").dataTable({
                                                            scrollY:        "400px",
                                                            scrollX:        true,
                                                            scrollCollapse: true,
                                                            // paging:         false,
                                                            fixedColumns:   {
                                                                leftColumns: 2,
                                                                rightColumns: 1
                                                            }
                            });
                        }
                    }); 
                }
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
                    html = '<option value="">All Bulan </option>';
                    $.each(msg, function( key,value ){
                    html +=`<option value=`+key+`>`+value+`</option>`;
                    });
                    $("#top_ten_bulan").html(html);
                    $("#loading_bulan_topten").hide();
                    // console.log(html);
                }
                });
            }
        </script>            
  
@endsection