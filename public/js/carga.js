$(document).ready(function(){
        $('.rightmenu a').die("click");
        $('.link').die("click");
        $('.link').live("click",function(){
                ini_carga_contenedor();
                $('.link').removeClass('ok');
                $("#proceso_session").val($(this).attr('name'));
                idSubProcesoTarea=$(this).attr("id");
        		$("#ocultoSubprocesoTarea").val(idSubProcesoTarea);
                $(this).parent("li").parent("ul").parent("div").parent("div").parent("div").parent("div").fadeOut("fast");
                aux_var=0;
                pagina_ini=$(this).attr('href');
                array_pagina_ini=pagina_ini.split("/");
                url_ini='';
                for (g=0;g<array_pagina_ini.length-3;g++)
                    url_ini +=array_pagina_ini[g]+'/';
                fin_carga_contenedor(pagina_ini);
                return false;
    });
});
function ini_carga_contenedor(){
    if($("#container").height()>0)
        $("#carga_body").css("height",$("#container").height())
    if ($("#carga_body").is(":hidden"))
        $("#carga_body").show();
}
function fin_carga_contenedor(pagina,tipo,mensaje,titulo){
        $("#container").show();
        $("#dashboard").hide();
        $('.boton').removeClass('listo');
        $("#carga_body").fadeOut(200);
        inicio.aux_var=0;
        win.show({vurl:pagina});
}