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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">



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

    
   <style type="text/css">
     body{
      margin:0;
     }
     .header-user-panel{
       background: #ededed;
       
     }


     .header-user-panel p {
        color: #a0a0a0;
        font-size: 12pt;
     }
     .header-user-panel hr{
       margin:0;
       border-color: #CCC;
     }
     .search-user{
      padding:10px 10px 1px 10px;

      background: #f8f8f8; 
     }

     .search-user input{
      border-radius: 20px;
     }

     .box-footer{
      background: white;
      border:0px;
     }

     .box-footer:hover{
       background: #ededed;
       cursor: pointer;
     }

     .list-kontak hr{
       margin:0 auto;
     }
     
     .img-sm, .box-comments .box-comment img, .user-block.user-block-sm img {
          width: 40px !important;
          height: 40px !important;
      }

    .box-comments .comment-text {
          margin-left: 50px;
          color: #555;
    }
   </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
  <div class="container-fluid" >
    <div class="row">
       <div class="col-md-4">
          <div class="header-user-panel">
              <div class="user-panel">
                <div class="pull-left image">
                  <img src="{{asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                  <p>Alexander Pierce</p>
                  <a href="#" style="color:green"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
              </div>
              <hr></hr>
              <div class="search-user" >
                  <div class="form-group">
                    <input type="text" name="search-user" placeholder="Cari User " class="form-control">
                  </div>
              </div>
          </div>
          <div class="list-kontak">
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user5-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user4-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user6-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user6-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user6-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user6-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user6-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
              <hr/>
              <div class="box-footer box-comments">
                  <div class="box-comment">
                    <!-- User image -->
                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('assets/dist/img/user6-128x128.jpg') }}" alt="User Image">

                    <div class="comment-text">
                          <span class="username">
                            Maria Gonzales
                            <span class="text-muted pull-right">8:03 PM Today</span>
                          </span><!-- /.username -->
                         It is a long established fact that a reader ...
                    </div>
                    <!-- /.comment-text -->
                  </div>
              </div>
          </div>
       </div>
       <div class="col-md-8">
          2
       </div>  
    </div>

  </div>
</body>
</html>


