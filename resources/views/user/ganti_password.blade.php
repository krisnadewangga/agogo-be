@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Ganti Passoword', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Ganti Password','link' => '#')
                                                    	) 
                                  ])
        <div class="card">
			<p class="card-description ">
                	Silahkan Masukan Password Lama Dan Baru Anda<br/>
         	</p>
         	<hr style="margin-top: 3px; margin-bottom: 10px;" />
     		
         	<div class="row">
      			<div class="col-md-6">
      				@if (session('success'))
					 	@component("components.alert", ["type" => "success"])
							{{ session('success') }}
						@endcomponent
					@endif

					@if (session('error'))
					 	@component("components.alert_error", ["type" => "danger"])
							{{ session('error') }}
						@endcomponent
					@endif
      				<form method="POST" action="{{ route('submitGantiPassword') }}">
		      		 	@csrf
		    		  	<div class="form-group @error('password_lama') has-error @enderror">
		                  	<label for="exampleInputName1">Password Lama</label>
		                  	<input type="password" class="form-control" id="exampleInputName1" placeholder="Masukan Password Lama" name="password_lama" >
		                    @error('password_lama')
					            <label class="control-label" for="inputError">
			                    	<i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
			                	</label>    
					        @enderror 
		                </div>
		              

						<div class="form-group  @error('password') has-error @enderror">
						    <label for="exampleInputName1">Password Baru</label>
						    <input type="password" class="form-control" placeholder="Password" name="password">	   
						</div>
						  
						<div class="form-group @error('password') has-error @enderror">
						   	<label for="exampleInputName1">Konfirmasi Password Baru</label>
						    <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation">
						    @error('password')
						         <label class="control-label" for="inputError">
						            <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>      
						        </label>   
						    @enderror
						    <label class="text-danger">{{$errors->first('password_confirmation')}}</label>
						</div>
					   
						<button type="submit" class="btn btn-primary ">Ganti Password</button>
		      		</form>
      			</div>
      		</div>
		</div>                          
	@endcomponent
	
@endsection