<!DOCTYPE html>
<html>
<head>
	<!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
      <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">      <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">
      <!-- Theme style -->
   <!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.2.1/css/bootstrap-reboot.min.css"> -->

    <style type="text/css">
    	.bungkus{
    		 width: 100%;  padding:10px;
    	}

    	.header{
    		/*background-color: #FBB901;*/
    		font-weight: bold;
    		
    		padding:5px 0px;
    		color : #FFFFFF;

    	}

    	.header img{
    		height: 50px;
    	}

 

    	.bodi div{
    		color : #000000;
    	}

    	.bodi h4{
    		margin-bottom: 10px;
    		
    		/*border-top:none;*/
    	}

    	.label_bodi{
    		font-size: 10pt;
    	}

    	.footer{
    		font-size: 9pt;
    		color : #aeaca8;
    	}

        .myButton {
            background-color:#44c767;
            border-radius:7px;
            display:inline-block;
            cursor:pointer;
            color:#ffffff;
            font-family:Arial;
            font-size:17px;
            padding:10px;
            border:0px;
        }
        .myButton:hover {
            background-color:#5cbf2a;
        }
        .myButton:active {
            position:relative;
            top:1px;
        }


       table.blueTable {
          width: 100%;
          text-align: left;
          border-collapse: collapse;
        }
        table.blueTable td, table.blueTable th {
          border: 1px solid #AAAAAA;
          padding: 3px 2px;
        }
        table.blueTable tbody td {
          font-size: 14px;
          color: #000000;
        }
        table.blueTable thead {
          background: #ededed;
        }
        table.blueTable thead th {
          font-size: 17px;
          font-weight: bold;
          color: #000000;
          padding: 10px;
        }
        table.blueTable tfoot td {
          font-size: 14px;
        }
        table.blueTable tfoot .links {
          text-align: right;
        }
        table.blueTable tfoot .links a{
          display: inline-block;
          background: #1C6EA4;
          color: #FFFFFF;
          padding: 2px 8px;
          border-radius: 5px;
        }
        
    </style>
	<title></title>

</head>
<body>
	<div class="bungkus">
		<div class="header">
			<img src="{{ asset('assets/dist/img/fixLogo.png') }}" style="height: 60px;">
		</div>
		<div class="bodi">
			<h4>Hi, <u>{{ $name }}</u></h4>
            <span class="label_bodi">
                {!! $email_body !!}
                
                <p></p>
                <div class="footer">
                    copyright &copy;@php date('Y') @endphp, </br>
                    CV. Azkha Indo Pratama All Rights Reserved <br/>
                    Kel. Kakenturan satu Kec. Maesa Kota. Bitung <br/>
                    Prov. Sulawesi Utara
                </div>
            </span>
		
		</div>
		
	</div>

	
</body>
</html>