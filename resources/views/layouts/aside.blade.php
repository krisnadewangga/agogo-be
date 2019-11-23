    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MAIN NAVIGATION</li>
          <li >
            <a href="#">
              <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              
            </a>
           
          </li>
          <li class="treeview">
            <a href="#">
              <i class="fa fa-files-o"></i>
              <span>Master User</span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('level.index') }}"><i class="fa fa-circle-o"></i>Level</a></li>
              <li><a href="{{ route('administrator.index') }}"><i class="fa fa-circle-o"></i>Admin</a></li>
               <li><a href="{{ route('kurir.index') }}"><i class="fa fa-circle-o"></i>Kurir</a></li>
              <li><a href="../layout/boxed.html"><i class="fa fa-circle-o"></i>Member</a></li>
            </ul>
          </li>

          <li class="treeview">
            <a href="#">
              <i class="fa fa-files-o"></i>
              <span>Master Item</span>
              
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('kategori.index') }}"><i class="fa fa-circle-o"></i>Kategori</a></li>
              <li><a href="{{ route('item.index') }}"><i class="fa fa-circle-o"></i>Item</a></li>
            </ul>
          </li>
          
          <li class="treeview">
            <a href="#">
              <i class="fa fa-files-o"></i>
              <span>Transaksi</span>
              
            </a>
            <ul class="treeview-menu">
              <li ><a href="{{ route('transaksi.index') }}"><i class="fa fa-files-o"></i> <span>Transaksi</span></a></li>
               <li ><a href="{{ route('pengiriman.index') }}"><i class="fa fa-files-o"></i> <span>Pengiriman</span></a></li>
            </ul>
          </li>

          

        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>