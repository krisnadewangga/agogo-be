 <aside class="control-sidebar control-sidebar-light ">
 	
 	
 	<div id="page1"  >
		
	          <div class="header-user-panel">
	              <div class="user-panel">
	                <div class="pull-left image">
	                  <img src="{{asset('assets/dist/img/cs.jpg') }}" class="img-circle" alt="User Image">

	                </div>
	                <div class="pull-left info">
	                  <p style="margin-bottom: 3px;">CS Agogo Bakery</p>
	                  <a href="#" style="color:green"><i class="fa fa-circle text-success"></i> 
	                  	{{ Auth::user()->name }} </a>
	                </div>
	              </div>
	              <hr></hr>
	              <!-- <div class="search-user" >
	                  <div class="form-group">
	                    <input type="text" name="input_search_nama" placeholder="Cari User" autocomplete="off" value="" class="form-control"  id="input_search_nama">
	                  </div>
	              </div> -->
	          </div>
	          <div id="loading_page1" class="text-center">
 			
 			  </div>
	          <div class="list-kontak">
	             
	              
	          </div>
	      
 	</div>
 	
 	<div id="page2" hidden>
 		
		<div class="header-user-panel">
	      <div class="user-panel">

	        <div class="pull-left image">
	        	<i class="fa  fa-arrow-left" style="cursor: pointer;" onclick="back()"></i>&nbsp;
	          <img src="{{asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image" id="imageFPS">
	        </div>
	        <div class="pull-left info" style="margin-left: 10px;">
	          <p class='nama-bukpes' style="margin-bottom: 3px;">Alexander Pierce</p>
	          <a href="#" style="color:green"><i class="fa fa-circle text-success"></i> <label id="status_member"></label></a>
	        </div>
	      </div>
	      <hr></hr>
	      
	    </div>
 		<div id="loading_page2" class="text-center">
 			
 		</div>
	
            
            
        <div class="box-body direct-chat direct-chat-primary">
         
	          <div class="direct-chat-messages" id="list-pesan">
	            
	        
	          </div>

        </div>
        
 		<div class="input-pesan" style="position: absolute;bottom: 0;right: 0; width: 100%" >
	          <form method="POST" action="{{route('kirim_pesan')}}" id="formKirimPesan">
	        	@csrf

	        	<input type="text" name="user_id_k" hidden>
	        	<div class="form-group">
		            <input type="text" name="pesan" placeholder="Ketik Pesan " class="form-control" autocomplete="off">
		        </div>
		        <button type="submit" hidden>asa</button>
	          </form>
	    </div>
 	</div>
 	
 </aside>
 <div class="control-sidebar-bg"></div>
 
 <script type="text/javascript">
 	var listPesan = "";
 	var listKontak = "";

 	$(document).ready(function(){

	  	loadDashboardPesan();
	

   	  	$("#formKirimPesan").submit(function(e){
   	  	 e.preventDefault();
   	  	 var data = $("#formKirimPesan").serialize();
   	  	 
   	  	 $.ajax({
   	  	 	url : 'kirim_pesan',
   	  	 	type : 'POST',
   	  	 	data : data,
   	  	 	beforeSend:function()
   	  	 	{
   	  	 		$("input[name='pesan']").val("");
 				loadingPesan('loading_page2','Sedang Mengirim Pesan');
   	  	 	},success:function(msg){
   	  	 		// console.log(msg);
              	setKirimPesan(msg.msg);
              	resetLoading('loading_page2');
   	  	 	}
   	  	 });
   	  })

     //    $("#input_search_nama").on('input',function(){
     //    	var nama = $(this).val();
     //    	var tampungListKontak = listKontak;
     //    	var listKontakCari = "";

     //    	if(nama != ""){
     //    		console.log("ada isi");

     //    		$.ajax({
	 			// 	url : 'cari_user',
	 			// 	data : {nama:nama},
	 			// 	success : function(msg){
	 			// 		console.log(nama);
	 			// 		if(nama != ""){
	 			// 			if(msg.msg.length > 0){
		 		// 				// console.log(nama);
		 		// 				$.each(msg.msg, function(index, value){

					// 				if(value.jumPesan > 0){
					// 					jumPesan = `<div class="pull-right" style="" id="jumPesanDash`+value.user_id+`"> 
					// 		                   				<sup class="label label-success" style="border-radius:100%; right:10px; top:-1px;">`+value.jumPesan+`</sup>
					// 		                    	</div>`;
					// 				}else{
					// 					jumPesan = '';
					// 				}

					// 				if(value.pesan.length >= 31){
					// 					sambungan_pesan = ' ...';
					// 				}else{
					// 					sambungan_pesan = '';
					// 				}

					// 				if(value.foto == null){
					// 					var foto = 'assets/dist/img/user.png';
					// 				}else{
					// 					var foto = 'upload/images-100/'+value.foto;
					// 				}


					// 				listKontakCari += `<div id="kontak`+value.user_id+`">
					// 									<div class="box-footer box-comments" onclick="bukaPesan('`+value.user_id+`','`+value.name+`','`+foto+`','`+value.jumPesan+`')">
					// 						                  <div class="box-comment">

					// 						                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('`+foto+`') }}" alt="User Image">

					// 						                    <div class="comment-text">
					// 						                          <span class="username">
					// 						                            `+value.name+`
					// 						                            <span class="text-muted pull-right">`+value.waktu+`</span>
					// 						                          </span><!-- /.username -->
					// 						                          `+jumPesan+`
					// 						                         `+value.pesan+sambungan_pesan+`
					// 						                    </div>
					// 						                  </div>
					// 						              </div>
					// 						              <hr/>
					// 					              </div>
					// 							  `;
					// 				$(".list-kontak").html(listKontakCari);
					// 			})

		 		// 			}else{
		 		// 				listKontakCari = `<div class='text-center'>
		 		// 									 <i class='fa fa-times-circle fa-4x'></i>
		 		// 									 <h4>User Tidak Ditemukan</h4>
		 		// 								  </div>`;
		 		// 				$(".list-kontak").html("");
		 		// 			}
	 			// 		}else{
	 			// 			$(".list-kontak").html(tampungListKontak);
	 			// 		}
	 					
	 					
	 			// 	}
	 			// });
     //    	}else{
     //    		console.log("kosong");
     //    		$(".list-kontak").html(tampungListKontak);
     //    	}
        	
     //    });
 	});


 	function setKirimPesan(value)
 	{
		PesanNew =  `<div class="direct-chat-msg right " `+cursor+`  id="chat`+value.id+`" >
				              <div class="direct-chat-text  ">
				                `+value.pesan+`  
				                <hr style='margin:3px 0px 3px 0px;'></hr>
				                <span style='font-size:11px;' class='waktu_chat'>`+value.waktu+`</span>
				                <i class='fa fa-trash pull-right' id="tombol_hapus`+value.id+`" style='margin-top:5px;' onclick="hapus_pesan('`+value.id+`')" ></i>
				              </div>
				            </div>`;
    	$("#list-pesan").append(PesanNew);
    	$("#list-pesan").scrollTop($("#list-pesan")[0].scrollHeight);
 	}

 	function loadDashboardPesan()
 	{
 		
 			$.ajax({
				url : url+'dashboard_pesan',
				beforeSend:function(){
					loadingPesan('loading_page1','Sedang Meload Dashboard Pesan');
					 // $(".list-kontak").html("");
				},success:function(msg){
					$.each(msg, function(index, value){


						if(value.jumPesan > 0){
							jumPesan = `<div class="pull-right" style="" id="jumPesanDash`+value.user_id+`"> 
				                   				<sup class="label label-success" style="border-radius:100%; right:10px; top:-1px;">`+value.jumPesan+`</sup>
				                    	</div>`;
						}else{
							jumPesan = '';
						}



						if(value.pesan.length >= 31){
							sambungan_pesan = ' ...';
						}else{
							sambungan_pesan = '';
						}

						if(value.foto == null){
							var foto = 'assets/dist/img/user.png';
						}else{
							var foto = 'upload/images-100/'+value.foto;
						}

						listKontak += `<div id="kontak`+value.user_id+`">
											<div class="box-footer box-comments" onclick="bukaPesan('`+value.user_id+`','`+value.name+`','`+foto+`','`+value.jumPesan+`','`+value.status_member+`')">
								                  <div class="box-comment">

								                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('`+foto+`') }}" alt="User Image">

								                    <div class="comment-text">
								                          <span class="username">
								                            `+value.name+`
								                            <span class="text-muted pull-right">`+value.waktu+`</span>
								                          </span><!-- /.username -->
								                          `+jumPesan+`
								                         `+value.pesan+sambungan_pesan+`
								                    </div>
								                  </div>
								              </div>
								              <hr/>
							              </div>
									  `;
					})
					$(".list-kontak").html(listKontak);
					resetLoading('loading_page1');

				}
			});
 		
 	}

 	function setDashboardPesan(value)
 	{
 		// console.log(value);
 		if(value.jumPesan > 0){
			jumPesan = `<div class="pull-right" style="" id="jumPesanDash`+value.user_id+`"> 
                   				<sup class="label label-success" style="border-radius:100%; right:10px; top:-1px;">`+value.jumPesan+`</sup>
                    	</div>`;
		}else{
			jumPesan = '';
		}

		if(value.pesan.length >= 31){
			sambungan_pesan = ' ...';
		}else{
			sambungan_pesan = '';
		}

		if(value.foto == null){
			var foto = 'assets/dist/img/user.png';
		}else{
			var foto = 'upload/images-100/'+value.foto;
		}

		listKontakNew = `<div id="kontak`+value.user_id+`">
							<div class="box-footer box-comments" onclick="bukaPesan('`+value.user_id+`','`+value.name+`','`+foto+`','`+value.jumPesan+`','`+value.status_member+`')">
				                  <div class="box-comment">

				                    <img class="img-circle img-sm" style="height: 100px;" src="{{ asset('`+foto+`') }}" alt="User Image">

				                    <div class="comment-text">
				                          <span class="username">
				                            `+value.name+`
				                            <span class="text-muted pull-right">`+value.waktu+`</span>
				                          </span><!-- /.username -->
				                          `+jumPesan+`
				                         `+value.pesan+sambungan_pesan+`
				                    </div>

				                  </div>
				              </div>
				              <hr/>
			              </div>
					  `;
		
		$(".list-kontak").prepend(listKontakNew);
		listKontak = $(".list-kontak").html();
 	}

 	function bukaPesan(user_id,name,foto,jumPesan,status_member){
 		
  		$(".nama-bukpes").html(name);
 		$("#imageFPS").prop('src',url+foto);
 		if(status_member == "0"){
 			$("#status_member").html('Not Member');
 		}else{
 			$("#status_member").html('Member');
 		}

 		$("#page1").hide('slow');
 		$("#page2").show('slow');
 		
 		$("input[name='user_id_k']").val(user_id);
 		$("input[name='pesan']").focus();
 		
 		if(jumPesan > 0){
 			bacaPesan(user_id);
 		}

 		$.ajax({
			url : url+'list_pesan',
			data : 'user_id='+user_id,
			beforeSend:function(){
				loadingPesan('loading_page2','Sedang Meload Pesan');
			},success:function(msg){
				// console.log(msg);
				$.each(msg, function(index, value){
					if(value.status == '1'){
						right = 'right';
						cursor = `style='cursor:pointer;'`;
						hapus = `<i class='fa fa-trash pull-right' onclick="hapus_pesan('`+value.id+`')" style='margin-top:5px;'  ></i>`;
					}else{
						right = '';
						cursor = '';
						hapus = '';
					}

					listPesan += ` <div class="direct-chat-msg `+right+` " `+cursor+` id="chat`+value.id+`" >
						              <div class="direct-chat-text  ">
						                `+value.pesan+`  
						                <hr style='margin:3px 0px 3px 0px;'></hr>
						                <span style='font-size:11px;' class='waktu_chat'>`+value.waktu_chat+`</span>
						                `+hapus+`
						              </div>
						            </div>
								  `;
				})
				$("#list-pesan").html(listPesan);

				$("#list-pesan").scrollTop($("#list-pesan")[0].scrollHeight);
				resetLoading('loading_page2');
			}
		});

 	}

 	function setPesan(value){

 		PesanNew =  `<div class="direct-chat-msg "  >
		              <div class="direct-chat-text  ">
		                `+value.pesan_nda_potong+`  
		                <hr style='margin:3px 0px 3px 0px;'></hr>
		                <span style='font-size:11px;' class='waktu_chat'>`+value.waktu+`</span>
		                
		              </div>
		            </div>`;
		$("#list-pesan").append(PesanNew).show('slow');
		$("#list-pesan").scrollTop($("#list-pesan")[0].scrollHeight);
 	}

 

 	function back()
 	{
 		$("#page1").show('slow');
 		$("#page2").hide('slow');
 		$("#list-pesan").html('');
 		$("input[name='user_id_k']").val("");
 		listPesan = "";
 		
 	}

 
 	function hapus_pesan(id){
 		var konfir = confirm('Hapus Pesan ?');
 		if(konfir){
 			$.ajax({
 				url : url+'hapus_pesan/'+id,
 				beforeSend:function(){

 				},
 				success:function(msg){
 					console.log(msg);
 					$("#chat"+id).hide('slow');	
 				}
 			});
 			
 		}
 		
 	}

 	function bacaPesan(user_id){
 		// alert(user_id);
 		$.ajax({
 			url : url+"baca_pesan/"+user_id,
 			beforeSend:function(){
 			},success:function(msg){
 				console.log(msg);
 			}
 		})
 	}

 	function loadingPesan(id,msg){
 		$("#"+id).html("<label class='label label-warning'><i class='fa fa-spinner fa-pulse fa-fw' ></i> "+msg+"...</label>");
 	}

 	function resetLoading(id){
 		$("#"+id).html("");
 	}
 </script>