<!DOCTYPE html>
<html>
<head>
	<!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
      <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">      <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">
      <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.2.1/css/bootstrap-reboot.min.css">
   
	<title>Aktifasi Akun AgogoBakery.com</title>

</head>
<body>
    
    <div class="row" style="margin-top:50px;">

        <div class="col-md-6 col-md-offset-3" style="padding:10px;">
            <img src="{{ asset('assets/dist/img/logo_kuning.png') }}" style="height: 50px;">
            <div class="row">
                <div class="col-md-12" style="">
                   @if($data['success'] == '1')
                       <div class="alert alert-success alert-dismissible">
                            <h4><i class="icon fa fa-check"></i> Hi, {{ $data['name'] }} !!</h4>
                            <span style="font-size: 9pt;">
                            Anda Berhasil Melakukan Aktifasi Akun AgogoBakery.com, Silahkan Nikmati Belanja Online Murah, Mudah Dan Dapat Dipercaya.

                            </span>
                            <div style="margin-top: 10px;font-size: 9pt;">
                                Salam Dari Kami. AgogoBakery.com & Happy Buyying ^-^
                            </div>
                        </div>
                    @elseif($data['success'] == '0')
                        <div class="alert alert-danger alert-dismissible">
                            <h4><i class="icon fa fa-ban"></i> Maaf !!</h4>
                            <span style="font-size: 9pt;">
                                No Aktifasi Yang Anda Punya Tidak Ditemukan, Silahkan Hubungi Official  AgogoBakery.com 
                                <br/>
                            </span>

                            <div style="margin-top: 10px;font-size: 9pt;">
                                <i class="fa fa-phone"></i> 0823 4396 5747 <br/>
                                <i class="fa fa-envelope"></i> officialAgogoBakery.com 
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning alert-dismissible">
                            <h4><i class="icon fa fa-ban"></i> Maaf, {{ $data['name'] }} !!</h4>
                            <span style="font-size: 9pt;">
                                Anda Gagal Melakukan Aktifasi Akun Di AgogoBakery.com,Silahkan Hubungi Official AgogoBakery.com 
                                <br/>
                            </span>

                            <div style="margin-top: 10px;font-size: 9pt;">
                                <i class="fa fa-phone"></i> 0823 4396 5747 <br/>
                                <i class="fa fa-envelope"></i> officialAgogoBakery.com 
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>