@extends('layouts.app1')

@section('content')
	@component('components.card', ['title' => 'Maps Transaksi', 
								   'breadcumbs' => array(
                                                          array('judul' => 'Maps Transaksi','link' => '#')
                                                    	) 
                                  ])

    
		

        <div class="card" style="margin-bottom: 10px;" >
        	Status : <b>{{$tampil_status}}</b>
        	<div id="googleMap" style="width:100%;height:400px; margin-top: 5px;"></div>
        </div>
        
      	 <!-- Menyisipkan library Google Maps -->
	    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyDLo5DHCK4qejCx34GkJvP9Tx9zak1az6s"></script>
	    <script>
	    
	     	var data = '{!! $infoMarker !!}';
	     	let markers = JSON.parse(data);

		    function initialize() {
		        var mapCanvas = document.getElementById('googleMap');

		        var mapOptions = {
		          center:new google.maps.LatLng(1.4400358, 125.1367023),
		          zoom:13,
		          mapTypeId: google.maps.MapTypeId.ROADMAP,
		        }  

		        var map = new google.maps.Map(mapCanvas, mapOptions)
		 
			    var infowindow = new google.maps.InfoWindow(), marker, i;
			    var bounds = new google.maps.LatLngBounds(); // diluar looping

			   
			    $.each(markers,function(index, value){
					// console.log(value.nama);

					pos = new google.maps.LatLng(value.lat, value.long);
				    bounds.extend(pos); // di dalam looping
				    marker = new google.maps.Marker({
				        position: pos,
				        map: map,
				        animation: google.maps.Animation.DROP,
				        // icon: "{{ asset('assets/dist/img/marker.png') }}"
				    });
				    

				    google.maps.event.addListener(marker, 'click', (function(marker, i) {
				        return function() {
				        	var html = `<table class='table'>
				        					<tr>
				        						<td>No Transaksi</td>
				        						<td>:</td>
				        						<td>`+value.no_transaksi+`</td>
				        					</tr>
				        					<tr>
				        						<td>Atas Nama</td>
				        						<td>:</td>
				        						<td>`+value.nama+`</td>
				        					</tr>
				        					<tr>
				        						<td>Alamat</td>
				        						<td>:</td>
				        						<td>`+value.detail_alamat+`</td>
				        					</tr>
				        					<tr>
				        						<td>Jenis Transaksi</td>
				        						<td>:</td>
				        						<td>`+value.marker_jt+`</td>
				        					</tr>
				        					<tr>
				        						<td>Status Transaksi</td>
				        						<td>:</td>
				        						<td>`+value.marker_status+`</td>
				        					</tr>
				        				</table>`;
				            infowindow.setContent(html);
				            infowindow.open(map, marker);
				        }
				    })(marker, i));
				    map.fitBounds(bounds); // setelah looping
				});
			    
		    }
		 
		    google.maps.event.addDomListener(window, 'load', initialize);
	    </script>
	@endcomponent
	
@endsection