function objetoAjax(){ 
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

//inicio validando formularios
//funci�n para validar los datos enviados para la consulta.
function  w_validacion(call){
			
			if(call=="1"){
				var usuario = document.form1.usuario.value;
				var password = document.form1.clave.value;
				//var server14 = document.form1.ipserver14.value;
			}
			if(call=="2"){
                                var usuario = document.form2.usuario.value;
				var password = document.form2.clave.value;
				//var server14 = document.form2.ipserver14.value;
			}
				
	
		var msn = "";
	
		if(usuario==""){
			msn = msn+"Usuario\n";
		}
		
		if(password==""){
			msn = msn+"Password\n";
		}
	
		if(msn!=""){
			
                        alert("Deben de ingresar los siguientes datos:\n\n"+msn);
		
		}else{
			login(usuario,password,call);
		
		}

}//fin validacion

function detalle_clientes(pagina,total){
	
	var contenido = parent.document.getElementById('conte');
	var texto =  parent.document.criterio.texto.value;
	var contexto_pag = "&totalRows_total_repre="+total+"&pageNum_rs_movi="+pagina+"";
	contenido.src = texto+""+contexto_pag;
	
}

function ver_mapa1(link_a){
	window.open(""+link_a+"","","width=450,height=350");
}
