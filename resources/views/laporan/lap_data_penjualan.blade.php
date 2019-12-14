@extends('layouts.app1')

@section('content')
     <div class="content">
         <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-6">
                <h3 style="margin-top: 0px;">Lap. Penjualan</h3>
            </div>
            <div class="col-md-6 text-right">
                <div style="margin-top: 0px;">
                     <a href="{{ route('penjualan') }}"><button class="btn btn-default btn-flat" data-toggle="modal" data-target="#modal_input">Grafik</button></a><button class="btn btn-flat btn-primary" data-toggle="modal" data-target="#modal_input">Data</button>
                 </div>
             </div>
          </div>

           <div class="card">
	        	<form method="POST" action="{{ route('filter_laporan_data_penjualan') }}">
		        	@csrf
		        	<div class="row">
		        		<div class="col-md-5">
		        			<div class="form-group">
				                <label>Mulai Tanggal</label>

				                <div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input type="text" id="mt" class="form-control pull-right datepicker" name="mt" autocomplete="off" value="{{ $input['mt'] }}">
				                </div>
				                <!-- /.input group -->
		              		</div>
		        		</div>
		        		<div class="col-md-5">
		        			<div class="form-group">
				                <label>Sampai Tanggal</label>

				                <div class="input-group date">
				                  <div class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                  </div>
				                  <input type="text" class="form-control pull-right datepicker" id="st" name="st" autocomplete="off" value="{{ $input['st'] }}" >
				                </div>
				                <!-- /.input group -->
		              		</div>
		        		</div>
		        		<div class="col-md-2" style="margin-top: 25px;">
		        			<button class="btn btn-primary">Filter</button>
		        			<a href="{{ route('data_penjualan') }}"><label class="btn btn-warning" >Reset</label></a>
		        		</div>
		        	</div>
	        	</form>
           </div>
           
           <div class="card" style="margin-top: 10px;">
	        	<div class="text-center">
	        		<h4><u><b>{{ $kop }}</b></u></h4>
	        	</div>
           </div>
      
      </div>
       <script type="text/javascript">
        	$(function(){
        		 $('.datepicker').datepicker({
		           format: 'dd/mm/yyyy',
		           autoclose: true
		        });
        	});
        </script>
@endsection('content')