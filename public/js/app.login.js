Ext.ns('login');
login={
	init:function(){
		/*Ext.Ajax.request({
			url:'/login/inicio/valida/',
			params:{},
			success:function(response,options){
				alert(response.responseText);
			}
		});*/
		window.setTimeout(
			function(){
				//Ext.get('Div_Extjs_Carga').dom.innerHTML = '<iframe src="/inicio/index/" />';
			}
			,1000
		);
	}
}
Ext.onReady(login.init,login);
/*login=function(usuario,clave,call){
	$.ajax({
		url:pathController,
		type:'post',
		dataType:'json',
		data:{
			action:'getLogin',
			controller:'Login',
			usuario:usuario,
			clave:clave
		},
		beforeSend:function(){
			//$.blockUI();
		},
		success:function(resultado){
                    
			if(resultado.status == 1){
                            
                       

				$.ajax({
							url:pathController,
							type:'post',
							dataType:'json',
							data:{
								action:'getOpciones',
								controller:'Login',
								vp_iduser:resultado.data[0].Expr_2,
								vp_idsis:$.trim(resultado.data[0].Expr_4)
							},
							success:function(result){
                                    alert(result.status);
						         if(result.status==1){

                                                            if(resultado.data[0].Expr_4=="7"){
                                                                if(call=="1"){
                                                                    document.form1.submit();
                                                                }else{
                                                                   document.form2.submit();
                                                                }
                                                            }else if(resultado.data[0].Expr_4=="8"){

                                                                if(call=="1"){
                                                                    document.form1.action="http://200.107.156.228/aprobar.php";
                                                                    document.form1.submit();
                                                                }else{
                                                                    document.form1.action="http://200.107.156.228/aprobar.php";
                                                                    document.form2.submit();
                                                                }
                                                            }else{
                                                                
                                                              alert('LOGIN INCORRECTO');
                                                                
                                                            }
								}else{
									alert('LOGIN INCORRECTO');

								}
							}
						});
                                                
			}
			else{
				   alert('LOGIN INCORRECTO');
			}
		
		},
		complete:function(){
			//$.unBlockUI();
		}
	});
};*/
