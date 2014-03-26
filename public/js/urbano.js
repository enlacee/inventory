$(document).ready(function() {
	$("#nombre").val("");
	$("#password").val("");
	/* Acceder */
	$('.acceder').hover(function(event){
		$(this).addClass('acceder_hover');
	},function(event){ 
		$(this).removeClass('acceder_hover');
	});
	/* Acceder */
	$('.campo').hover(function(event){
		$(this).addClass('campo_hover');
	},function(event){ 
		$(this).removeClass('campo_hover');
	});
	/* Acceder */
	
	$(".enviar").hover(function(event){
		$(".enviar").addClass("enviar_hover");
	},function(event){
		$(".enviar").removeClass("enviar_hover");
	});
	/* Acceder */
	$('.boton_acti').hover(function(event){
		$(this).addClass('boton_acti_hover');
	},function(event){ 
		$(this).removeClass('boton_acti_hover');
	});
        /*29-10-10 19:49
         *magilera
         *Actulizacion
         *  para q cierre avisos de la antigua estructura
         */
        /*cerrar avisos de antiguo modelo*/
          $('#cerraravisobien').click(function(event){
            $("#avisobien").fadeOut("slow");
          });

          $('#cerraravisomal').click(function(event){
            $("#avisomal").fadeOut("slow");
          });
        /*fin*/
	/* Acceder */
	
	$("#nombre").change(function event(){
				nombre=jQuery.trim($("#nombre").val());
				$("#sistema").addOption("1","Cargando....");
				$.post("class/sistema_ajax.php",{accion:'sistemas_usuario',param1:nombre},
			        function(data){
                                                $("#sistema").removeOption(/./);
                                                valor=new Array();
                                                datos=data.split("|");
                                                if (datos.length-1>0){
                                                    
                                                    for (var i=0; i<=datos.length-1; i++)
                                                    {
                                                          if ((i%2)==0)
                                                          {valor=datos[i];}
                                                          if ((i%2)==1)
                                                          {
                                                              $("#sistema").addOption(valor, jQuery.trim(datos[i]),false);
                                                          }
                                                    }
                                                }else{
                                                    $("#sistema").addOption(0,"No asignados",false);
                                                }
				});
	});
	
	$('#acceder_boton').click(function(event){
		$("#error-nombre").hide();
		$("#error-password").hide();
		ban=1;
		
		if($('#nombre').val()==""){
			$("#error-nombre").slideDown("slow");
			ban=0;
		}
		
		/*if($('#password').val()==""){
			$("#error-password").slideDown("slow");
			ban=0;
		}*/
		if($('#sistema').val()=="#"){
			$("#error-sistema").slideDown("slow");
			ban=0;
		}
		
		if(ban==1){
			$("#formulario_login").submit();
		}
		
		
	});
	
	$('#password_boton').click(function(event){
		
	 	if($("#devolver_password:first").is(":hidden")) {
		        $("#devolver_password").slideDown("slow");
		      } else {
		        $("#devolver_password").hide();
		      }
		
		
		
	});
	$("#error-sistema-login").slideDown("slow");

function CambiaraMay(obj) {
  txt = obj.value;
  txt = txt.substr(0,txt.length).toUpperCase();
  obj.value = txt;
}
	
});
	