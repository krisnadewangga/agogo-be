    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MAIN NAVIGATION</li>
          <li class="" id="m_dashboard">
            <a href="{{ route('home') }}">
              <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              
            </a>
           
          </li>
          <li class="treeview" id="m_user">
            <a href="#">
              <i class="fa  fa-users"></i>
              <span>Master User</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
              <li id="sm_level"><a href="{{ route('level.index') }}"><i class="fa fa-circle-o"></i>Level</a></li>
              <li id="sm_admin"><a href="{{ route('administrator.index') }}"><i class="fa fa-circle-o"></i>Admin</a></li>
               <li id="sm_kurir"><a href="{{ route('kurir.index') }}"><i class="fa fa-circle-o"></i>Kurir</a></li>
              
            </ul>
          </li>

          <li class="treeview" id="m_item">
            <a href="#">
              <i class="fa fa-suitcase"></i>
              <span>Master Item</span>
              <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              
            </a>
            <ul class="treeview-menu">
              <li id="sm_kategori"><a href="{{ route('kategori.index') }}"><i class="fa fa-circle-o"></i>Kategori</a></li>
              <li id="sm_item"><a href="{{ route('item.index') }}"><i class="fa fa-circle-o"></i>Item</a></li>
            </ul>
          </li>
          
          <li class="treeview" id="m_transaksi">
            <a href="#">
              <i class="fa fa-exchange"></i>
              <span>Transaksi</span>
              <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
              <li id="sm_transaksi"><a href="{{ route('transaksi.index') }}"><i class="fa fa-circle-o"></i> <span>Pesanan</span></a></li>
               <li id="sm_pengiriman"><a href="{{ route('pengiriman.index') }}"><i class="fa fa-circle-o"></i> <span>Pengiriman</span></a></li>
               <li id="sm_topup"><a href="{{ route('topup_saldo.index') }}"><i class="fa fa-circle-o"></i> <span>TopUp Saldo</span></a></li>
               <li id="sm_promo"><a href="{{ route('setup_promo.index') }}"><i class="fa fa-circle-o"></i> <span>SetUp Promo</span></a></li>
            </ul>
          </li>

          <li class="treeview" id="m_laporan">
            <a href="#">
              <i class="fa fa-files-o"></i>
              <span>Laporan</span>
              <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
              <li id="sm_user" ><a href="{{ route('lap_user') }}"><i class="fa fa-circle-o"></i> <span>User</span></a></li>
              <li id="sm_penjualan"><a href="{{ route('penjualan') }}"><i class="fa fa-circle-o"></i> <span>Penjualan</span></a></li>
              <li id="sm_pendapatan"><a href="{{ route('lap_pendapatan') }}"><i class="fa fa-circle-o"></i> <span>Pendapatan</span></a></li>
              
            </ul>
          </li>


          

        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>