<header class="main-header">
      <!-- Logo -->
      <a href="../../index2.html" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>AGG</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>AGOGO</b>MIN</span>
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
            <li class="notifications-menu">
               <a href="{{ route('transaksi.index') }}">
                <i class="fa fa-shopping-cart"></i>
                <span class="label label-warning jumPesanan"></span>
              </a>
            </li>

            <li class="notifications-menu">
               <a href="{{ route('pengiriman.index') }}">
                <i class="fa fa-send"></i>
                <span class="label label-warning jumPengiriman"></span>
              </a>
            </li>
             

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
                <li class="footer"><a href="#">View all</a></li>
              </ul>
            </li>
           
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="{{asset('assets/dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                <span class="hidden-xs">{{ Auth::user()->name }}</span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="{{asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">

                  <p>
                    Alexander Pierce - Web Developer
                    <small>Member since Nov. 2012</small>
                  </p>
                </li>
               
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="#" class="btn btn-default btn-flat">Profile</a>
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