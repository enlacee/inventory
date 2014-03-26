/*** @author Eespiritu*/
var pathController='libs/FrontController.php';
var pathControllerIndex='libs/FrontController.php';
var formatDate='dd/mm/yy';
var pathIco='css/ico/';
var icoCalendar='css/ico/calendar.png';
var newHtml=null;
var class_temp=null;
var id_temp=null;
var ico_temp=null;
var loadMessage='<option>Cargando...</option>'
var dpto_tmp=prov_tmp=dist_tmp=null;
var _selected=null;
var _data=[];
var _items=[];
var _index=0;
var _ubigeo='';
var dataJSON={};
var err=0;
	
destroyButtonCloseUI=function(){
	$(this).parents('.ui-dialog:first').find('.ui-dialog-titlebar-close').remove();
};

getHour=function(){
	var hora = new Date();
	return hora.getHours()+':'+hora.getMinutes()+':'+hora.getSeconds();
};


getList=function(obj,dataJSON,extras){
$.ajax({
url:pathController,
type:'get',
dataType:'json',
data:dataJSON,
beforeSend:function(){
$('#'+obj).html('<option>Cargando...</option>').attr('disabled',true);
if (typeof extras != 'undefined' && extras == 'object') {
if (typeof extras.before != 'undefined'  && typeof extras.before=='function') {
extras.before();
}
}
},
success:function(result){
if(result.status==1 && result.data.length>0){
if(extras=='TODOS'){
loadList(obj,result.data,1);
}
else{
loadList(obj,result.data,0);	
}
}else{
$('#'+obj).empty().append('<option value="00">SIN DATOS</option>').attr('disabled',true);
}
},
complete:function(){
if(typeof extras!='undefined' && typeof extras=='object'){
if(typeof extras.selected!='undefined'){
$('#'+obj).val(extras.selected);
}

if(typeof extras.trigger!='undefined' && typeof extras.trigger=='function'){
$('#'+obj).trigger('change');
}

if(typeof extras.finish!='undefined' && typeof extras.finish=='function'){
extras.finish();
}
if(typeof extras!='undefined' && typeof extras=='TODOS'){
$('#'+obj+' option:eq(0)').html('TODOS');
}
}
}
});
};

loadList=function(obj,data,indica){
obj=$('#'+obj);
if(indica==1){
newHtml='<option value="00">TODOS</option>';
}
else{
newHtml='<option value="00">[SELECCIONE]</option>';
}
$.each(data,function(key, field){
_index=0;
$.each(field,function(key,item){
_items[_index]=item;
_index++;
});
newHtml+='<option value="'+$.trim(_items[0])+'">'+$.trim(_items[1])+'</option>';
});
obj.empty().append(newHtml).attr('disabled',false);
};

implode=function(obj,union){
	tmp='';
	$.each(obj,function(key,item){
		tmp=tmp+$('#'+item).val()+union;
	});
	
	return tmp.substr(0,tmp.length-1);
};

inObject=function(obj,elt,index){
	stop=false;
	$.each(obj,function(key,field){		
		if(field[index]==elt){
			stop=true;
		}
	});
	return stop; 
};

delInObject=function(obj,index){
	//by=false;
	var obj_tmp=[];
	console.debug(obj);
	$.each(obj,function(key,field){
		console.debug(key+'=='+index);
		if (key == index) {
			obj_tmp[obj_tmp.length]=field;
		}
	});
	return obj_tmp;
};


printer=function(selector,json){
	var params='';
	if(json.data.length>0){
		$.each(json.data,function(key,param){
			_index=0;
			$.each(param,function(key,item){
				_items['key']=key;
				_items['value']=item;
			});
			params=params+_items['key']+'='+_items['value']+'&';
		});
	}
	var url=json.url+'?'+params;
	alert(url);
	if(typeof json.open=='undefined'){
		var page='<iframe src="'+url+'" />';
		$('#'+selector).html(page);
	}else{
		window.open(url);
	}	
};

printManager=function(json){	
	var _width=(typeof json.width!='undefined'?json.width:700);
	var _height=(typeof json.height!='undefined'?json.height:700);
	var params='';

	$.each(json.data,function(key,param){
		_items['key']=key;
		_items['value']=param;
		params=params+_items['key']+'='+_items['value']+'&';
	});
	_width+=100;
	var url=json.file+'?'+params;
	
	var iframe='<iframe border="0" src="'+url+'" style="width:'+(_width-5)+'px;height:'+(_height-40)+'px;left:0px;top:opx;" />';
	$('#'+json.id).empty().append(iframe).dialog('destroy').dialog({
		modal:true,
		autoOpen:true,
		width:_width,
		height:_height,
		open:function(){
			$(this).parents('.ui-dialog:first').find('.ui-dialog-content').css({
	  		padding: 0,
				overflow:'hidden'
	 		});
		},
		title:(typeof json.title!='undefined'?json.title:'')
	}).dialog('open').fadeIn(50);
};


str_pad=function(str,chars,longitud,type){
	var newcad=cad='';
  str=''+str;
  longitud=parseInt(longitud)-parseInt(str.length);
  for(var pos=0;pos<longitud;pos++){
		cad+=chars;
	}
	if(typeof type!='undefined' && type!='L'){
		type=type.toUpperCase();
		newcad=str+''+cad;
	}else{
		newcad=cad+''+str;
	}

	return newcad;
}

resumeDate=function(dias){
	var newdate=new Date();
	dia=newdate.getDate();
	if(dia<10){
		dia='0'+newdate.getDate().toString();
	}
	if(typeof dias=='undefined'){
		return newdate.getDate()+'/'+(newdate.getMonth()+1)+'/'+newdate.getFullYear();
	}
	newdate.setTime(newdate.getTime() -(-0) + dias*24*60*60*1000);  
	dia=newdate.getDate();
	if(dia<10){
		dia='0'+newdate.getDate().toString();
	}
	//return (newdate.getDate())+'/'+(newdate.getMonth()+1)+'/'+newdate.getFullYear();
	return (dia)+'/'+(newdate.getMonth()+1)+'/'+newdate.getFullYear();
}

String.prototype.renew = function(value){
	if(this.indexOf('%')!=-1){
		return (this.replace('%',value));
	}
	return this;
}

checkInputs=function(objs){
var err=0;
var id='';
$('.required_color').css('color','').removeClass('required_color');
$.each(objs,function(key,obj){
id=$(obj).attr('id').slice(2);
if($(obj).attr('type')=='text'){
if($(obj).val()=='' || $(obj).val()==0){
$('#e_'+id).css('color','red').addClass('required_color');
err++;                    
}
}
if(obj.type=='select-one' && (obj.value)=='00'){
$('#e_'+id).css('color','red').addClass('required_color');
err++;
}
if(obj.type=='checkbox' && obj.cheched==false){
$('#e_'+id).css('color','red').addClass('required_color');
err++;
}
});
return err;
};
