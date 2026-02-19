@extends('layouts.app')

@section('content')
    <div class="login-box" style="margin-bottom: 0px; margin-top: 150px;" >
      <div class="login-logo">
        
      </div>
      <!-- /.login-logo -->
      <div class="login-box-body" style="margin-top: -20px;">
        <!-- <p class="login-box-msg">Silahkan Masukan Email Dan Password Anda</p> -->
        <div style="margin-top: 10px;">
            <!-- <h3 style="margin-top: 0px;" class="fg-black"><u><b>LOGIN</b></u></h3> -->
            <center><img src="{{ asset('assets/dist/img/fixLogo.png') }}" height="70px"></center>
            <p class="login-box-msg" style="margin-top: 10px;">Silahkan Masukan Email dan Password Anda</p>
        </div>
        <div style="margin-top: 0px; margin-bottom: 10px;">
          <form method="POST" action="{{ route('login') }}">
          @csrf
          
          <div class="form-group has-feedback @error('email') has-error @enderror ">    

            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="off" autofocus placeholder="Email" >
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

            @error('email')
                <label class="control-label" for="inputError">
                    <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                </label>    
            @enderror
          </div>
          
          <div class="form-group has-feedback @error('password') has-error @enderror">
             <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="password">
             <span class="glyphicon glyphicon-lock form-control-feedback"></span>
             
             @error('password')
                <label class="control-label" for="inputError">
                    <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                </label>    
             @enderror
            
          </div>
          
          <div class="row">
            
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
            </div>
            
          </div>
        
            </form>
        </div>
      </div>
      <!-- /.login-box-body -->
    </div>

    <!-- /.login-box -->
    <div style="margin-top: 10px; text-align: center;">
        <label>copyright © 2019 <a href='http://suaratech.com/'>Cv. Azkha Indo Pratama</a>.  All rights reserved.</label>
      </div>
@endsection
