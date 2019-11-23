var host = window.location.host;
function loadNotifikasi(){

    $.ajax({
    	url : "load_notif",
    	data : "user_id="+user_id,
    	beforeSend:function(){
    		$("#listNot").html(`<center><span class='bg-warning text-yellow' style='padding:2px;'>
    								<i class='fa fa-spinner fa-pulse fa-fw' ></i> Loading ... </span>
								</center>`);
    	},
    	success:function(msg){
            console.log(msg);
    		if(msg.status == '1'){
    			$(".jumNotif").html(msg.jumNot);
    			
    			var listNotif = '';
    			$.each(msg.listNotif, function( index, value ) {
    			  if(value.dibaca == '0'){
    			  	var bg = '#f4f4f4';
    			  }else{
    			  	var bg = '#FFFFFF';
    			  }

                  if(value.jenis_notif == '1'){
                    var link_notif = "detail_transaksi?transaksi_id="+value.judul_id;
                    var label = "<label class='label label-warning'>Transaksi</label>";
                  }else if(value.jenis_notif == '2'){
                    var link_notif = 'konfir_bayar';
                    var label = "";
                  }else if(value.jenis_notif == '7'){
                    var link_notif = '';
                    var label = "<label class='label label-success'>BukPay</label>";
                  }else if(value.jenis_notif == '4'){
                    var link_notif = '';
                    var label = "<label class='label label-info'>Pengiriman</label>";
                  }
                  
				  listNotif += `<li style="background-color : `+bg+`" >
                                    <a href="`+link_notif+`" target='blank()' style="font-size: 13px; ">
                                        <i class="fa fa-bell-o" style="color:#FBB901;"></i> `+value.created_at+` - `+label+` <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; `+value.judul+` lorem ipsm  
                                    </a>
                                </li>`;
                 
				});
			
    			$("#listNot").html(listNotif);
    		}
    	}

    });
}

function bacaNotif(){
	$.ajax({
		url : 'baca_notif',
		data : 'user_id='+user_id,
		success : function(msg){
			if(msg.status == '1'){
				setTimeout(refreshNotif, 3000);
			}
		}
	});
}

function refreshNotif(){
 	$.ajax({
    	url : "load_notif",
    	data : "user_id="+user_id,
    	
    	success:function(msg){
    		if(msg.status == '1'){
    			$(".jumNot").html(msg.jumNot);
    		}
    	}

    });
}