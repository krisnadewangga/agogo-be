<header class="main-header">
      <!-- Logo -->
      <a href="#" class="logo" >
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>AGG</b></span>
        <!-- logo for regular state and mobile devices -->
        <!-- <span class="logo-lg"><b>AGOGO</b>MIN</span> -->
         <center >
            <img src="{{ asset('assets/dist/img/fixLogo.png') }}" height="40px" class="logo-lg" style="margin-top:5px; background: #FFFFFF; padding:5px; border-radius: 5px; width: 80px;">
          </center>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            
            <!-- Notifications: style can be found in dropdown.less -->
            @if(Auth::user()->level_id == "2")
              <li class="notifications-menu">
                 <a href="#" data-toggle="control-sidebar" id="notPesan">
                  <i class="fa fa-envelope"></i>
                  <span class="label label-warning jumPesanNot"></span>
                </a>
              </li>
            @endif
            
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <span class="label label-warning jumNotif"></span>
              </a>
              
              <ul class="dropdown-menu">
                <li class="header">Anda Memiliki <u class="jumNotif"></u> Notifikasi</li>
                <li>
                  <!-- inner menu: contains the actual data -->
                  <ul class="menu" id="listNot">
                    
                  </ul>
                </li>
                <li class="footer text-left">
                  <a href="{{ route('list_notifikasi') }}"><u>Lihat Semua</u></a>
                </li>
              </ul>
            </li>
           
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                @if(!empty(Auth::User()->foto))
                   <img src="{{ asset('upload/images-100/'.Auth::User()->foto) }}" class="user-image" alt="User Image">
                @else
                   <img src="{{asset('assets/dist/img/user.png') }}" class="user-image" alt="User Image">
                @endif
                
                <span class="hidden-xs">{{ Auth::user()->name }}</span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  @if(!empty(Auth::User()->foto))
                    <img src="{{ asset('upload/images-400/'.Auth::User()->foto) }}" class="img-circle" alt="User Image">
                  @else
                     <img src="{{asset('assets/dist/img/user.png') }}" class="img-circle" alt="User Image">
                  @endif
                 

                  <p>
                    <small><span style="cursor: pointer;" data-target="#modal_ganti_fp" data-toggle="modal"><u>Ganti Foto Profil</u></span></small>
                    {{ Auth::User()->name }} - @if(Auth::User()->level_id == 1)SuperAdmin @else Administrator @endif
                   
                  </p>
                </li>
               
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{ route('in_ganti_password') }}" class="btn btn btn-flat">Ganti Password</a>
                  </div>
                  <div class="pull-right">
                    <a  class="btn btn-default btn-flat" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                    </a>
                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                     </form>
                  </div>
                </li>
              </ul>
            </li>
           
        
          </ul>
        </div>
      </nav>
    </header>