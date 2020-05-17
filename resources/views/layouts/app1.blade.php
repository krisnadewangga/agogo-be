<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AgogoBakery.com</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">
     <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }} ">

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/skins/_all-skins.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/dist/css/style_message.css') }}"
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

   
    
    <style type="text/css">
      .card{
        background: #FFFFFF;
        padding:20px;
      }

       .fileUpload {
              position: relative;
              overflow: hidden;
              margin: 10px;
          }
          
          .fileUpload input.upload {
              position: absolute;
              top: 0;
              right: 0;
              margin: 0;
              padding: 0;
              font-size: 20px;
              cursor: pointer;
              opacity: 0;
              filter: alpha(opacity=0);
          }
          
          .table-galeri td{
            height: 35px;
            padding:5px;
          }

          .list-gambar td{
            
              padding:5px;
           
              border : 1px solid #CCC;
          }

           .nowrap { white-space: nowrap } 
    
    </style>

    <!-- jQuery 3 -->
    <script src="{{ asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('assets/dist/js/demo.js') }}"></script>
    <!-- CKEDITOR -->
    <script src="{{ asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    
   

    <!-- Pusher -->
    <script src="https://js.pusher.com/3.1/pusher.min.js"></script>

    <script type="text/javascript"></script>
    <script type="text/javascript"> 
       @if (Auth::check()) 
          var user_id =  {{ Auth::user()->id }}  
          var level_id = {{ Auth::user()->level_id }}
      @else var user_id = '' @endif 
      var menu_active = "{{ $menu_active }}";
    </script>

    <!-- NOTIFIKASI -->
    <script src="{{ asset('assets/dist/js/notifikasi.js') }}"></script>
    <!-- page script -->
    
   <!--  <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script> -->
   



    <script>
      
      $(function () {
         getJumPesan();
         loadNotifikasi();
         activeMenu();
         getJumPesanan();
         getJumPengiriman();
         getJumAP();
         getJumKP();
         
         var t = $('.dataTables').DataTable();
         t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
         } ).draw();

         $('.select2').select2();
         
         $('textarea').each(function(){
            $(this).val($(this).val().trim());
         });

          //Date picker
       
        
        var pusher = new Pusher('{{ env('MIX_PUSHER_APP_KEY') }}', {
                              cluster: '{{ env('MIX_PUSHER_APP_CLUSTER') }}',
                              encrypted: true
                            });


        @if (Auth::check())
         
          if(level_id == "2" || level_id == "7"){
            var channelPesan = pusher.subscribe('agogoPesan');
            channelPesan.bind('App\\Events\\PusherEvent', function(data) {
              
              // console.log(data);
              var cek_buka_pesan_us = $("input[name='user_id_k']").val();
              if(data.type == "2"){
                  if ( $('#kontak'+data.message.user_id).length ){
                      $("#kontak"+data.message.user_id).remove();
                      // console.log("hilang");
                      setDashboardPesan(data.message);
                  }else{
                    setDashboardPesan(data.message);
                  }

                  if( (cek_buka_pesan_us != "") && (cek_buka_pesan_us == data.message.user_id) ){
                    setPesan(data.message);
                    bacaPesan(data.message.user_id);
                    $("#jumPesanDash"+data.message.user_id).html("");
                  }

                  getJumPesan();
              }else if(data.type == "3"){
                  getJumPesan();
                  $("#jumPesanDash"+data.message.user_id).html("");
              }else if(data.type == "4"){
                  if ( $('#kontak'+data.message.user_id).length ){
                      $("#kontak"+data.message.user_id).remove();
                      // console.log("hilang");
                      setDashboardPesan(data.message);
                  }else{
                    setDashboardPesan(data.message);
                  }

                  if( (cek_buka_pesan_us != "") && (cek_buka_pesan_us == data.message.user_id) && (user_id != data.message.pengirim_id) ){
                    setKirimPesan(data.message);
                  }
              }else if(data.type == "5"){
                  if(data.message.jenisNotif == "1"){
                    getJumPesanan();
                  }else if(data.message.jenisNotif == "2"){
                    getJumPengiriman();
                  }else if(data.message.jenisNotif == "3"){
                    getJumAP();
                  }else if(data.message.jenisNotif == "4"){
                    getJumKP();
                  }
              }
            });
          }
          

          var channel = pusher.subscribe('agogo.{{ Auth::user()->id }}');
          channel.bind('App\\Events\\PusherEvent', function(data) {
              // this is called when the event notification is received...
              loadNotifikasi();
              // alert(JSON.stringify(data));
          });
          
        @else
          var channel = pusher.subscribe('agogo');
            channel.bind('App\\Events\\PusherEvent', function(data) {
                // this is called when the event notification is received...
                console.log('oke');
            });
        @endif

      })

      function activeMenu(){
        var pisah = menu_active.split('|');
        $(".treeview").prop('class','treeview');
        $(".submenu").prop('class','');

        $("#m_"+pisah[0]).prop('class','treeview active menu-open');
        $("#sm_"+pisah[1]).prop('class','active');
      }
    </script>
   
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
  <div class="wrapper">

    @include('layouts.header')
    @include('layouts.aside')
       
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <main>
        @yield('content')
      </main>
      
      
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
     
      <strong>copyright © 2019 <a href='http://suaratech.com/'>Cv. Azkha Indo Pratama</a>.  All rights reserved.
      reserved.</strong>
    </footer>
    
    @if(Auth::user()->level_id == "2")
      @include('layouts.asideM') 
    @endif
  </div>
  <script type="text/javascript">
    
      $(document).ready(function(){
        
        @if(Session::get('gagal') == 'gambar_fp' )
          $("#modal_ganti_fp").modal('show');
        @endif
      });
      

  </script>
   @component("components.modal", ["id" => "modal_ganti_fp" ,"kop_modal" => "Ganti Foto Profil"])

      <form method="POST" action="{{ route('submit_ganti_fp') }}" enctype="multipart/form-data">
        @csrf
            <div class="form-group @error('gambar') has-error @enderror ">
              <label>Pilih Foto</label>
              <input id="gambar" type="file"  name="gambar" >
              @error('gambar')
                  <label class="control-label" for="inputError">
                        <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                    </label>    
              @enderror 
            </div>

            <div class="text-right">
               <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>

      </form>
    @endcomponent

</body>
</html>


