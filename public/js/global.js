/*
 * Desarrollado por Luis Remicio 
 * Funciones Generales del sistema.
 * laro_06@hotmail.com
 */
win = {
    request:[],
    modules:[],
    loaded:false,
    getModule:function(v){
        var ms = this.modules;
        for(var i = 0, len = ms.length; i < len; i++){
            if(ms[i].id == v ){
                return ms[i];
            }
        }
        return null;
    },
    loadModuleComplete:function(success, vid){
        if(success === true && id){
            this.request.push({
                id:vid
            });
        }
    },
    requestModule:function(id){
        var ms = this.request;
        for(var i = 0, len = ms.length; i < len; i++){
            if(id==ms[i].id) return true;
        }
        return false;
    },
    init:function(){
        
    },
    /*
     * Recomendada para produccion
     */
    show:function(param){
        this.param = param;
        this.param.vurl=this.param.vurl==undefined?'':this.param.vurl;
        this.param.vwidth=this.param.vwidth==undefined?'':parseInt(this.param.vwidth);
        this.param.vheight=this.param.vheight==undefined?'':parseInt(this.param.vheight);
        this.param.vjs=this.param.vjs==undefined?'':this.param.vjs;
        this.param.title=this.param.title==undefined?this.param.vnombre:this.param.title;
        this.param.mask = this.param.mask==undefined?Ext.getBody():this.param.mask;
        //Ext.getCmp('tab_index_cautin').el.mask('Cargando...', 'x-mask-loading');
        var myMask = new Ext.LoadMask(this.param.mask, {
            msg:"Por favor espere..."
        });
        //console.debug(myMask);
        myMask.show();
        Ext.getCmp('index_weburbano_carga').load({
            url:this.param.vurl,
            timeout:180000,
            scripts:true,
            callback:function(){
                myMask.hide();
            }
        });
    },
    /*
     * Recomendada para produccion
     */
    showx:function(param){
        this.param = param;
        this.param.options = this.param.options==undefined?'':this.param.options;
        this.param.moduleId = this.param.moduleId==undefined?'':this.param.moduleId;
        this.param.vurl=this.param.vurl==undefined?'':this.param.vurl;
        this.param.mask = this.param.mask==undefined?Ext.getBody():this.param.mask;
        var op = this.param.options;
        var vid = this.param.moduleId;
        var myMask = new Ext.LoadMask(this.param.mask, {
            msg:"Por favor espere..."
        });
        this.modules.push({
            id:vid
        });
        var m = this.getModule(vid);
        if(m){
            if(this.requestModule(vid)){
                var javascript = eval(vid);
                javascript.init(op);
            }else{
                myMask.show();
                Ext.getCmp('index_weburbano_carga').load({
                    url:this.param.vurl,
                    scripts:true,
                    timeout:180000,
                    callback:function(){
                        myMask.hide();
                        win.loadModuleComplete(true,vid);
                    }
                });
            }
        }
    }
}
var LarSyrExt = function(){
    this.next_obj = function(arrayId, obj){
        var cc = Ext.fly(obj).getAttribute('maxlength');
        var ca = arrayId.length;
        i=null;
        for(i=0;i<ca;i++){
            var id = arrayId[i].split("|");
            if(id[0]==obj){
                var j = 2;
                k=null;
                if(i!=ca-1)
                    for(k=1;k<j;k++){
                        var idf = arrayId[i+k].split("|");
                        if(Ext.getCmp(idf[0]).disabled) j++;
                    }
                if(id[1]==1) Ext.getCmp(obj).setValue(this.Ext_ceros([Ext.fly(obj).getValue(),cc]));
                if(i!=ca-1) Ext.getCmp(idf[0]).focus();
            }
        }
    };
    this.validar = function(arrayId){
        var i = null;            
        for(i = 0; i < arrayId.length; i++)
        {
            var tipo = Ext.getCmp(arrayId[i]).getXType();                
            switch (tipo)
            {
                case 'datefield':
                    break;
                case 'textfield':
                    if (Ext.getCmp(arrayId[i]).getValue() == '')
                    {
                        alert("Debe de ingresar " + Ext.getCmp(arrayId[i]).title);
                        ColocarFocoObjeto(arrayId[i]);                        	
                        return false;
                    }
                    break;
                case 'combo':
                    if (Ext.getCmp(arrayId[i]).getValue() == '')
                    {
                        alert("El(La) "+ Ext.getCmp(arrayId[i]).title +" tiene que se diferente de 00");
                        ColocarFocoObjeto(arrayId[i]);
                        return false;
                    }
                    break;
            }
        } 
        return true;
    }; 
    this.Ext_ceros = function(val){
        var a = val[0];
        for(i=a.length;i<val[1];++i) a = '0'+a;
        return a;
    };
    this.Display_Panel = function(array,parent){
        var est;
        for(i=0;i<array.length;++i){
            est = parseInt(array[i].split('|')[1])==1?true:false;
            Ext.getCmp(array[i].split('|')[0]).setVisible(est);
        }
        Ext.getCmp(parent).doLayout();
    };
    this.ShowPdf = function(p){
        p.url = p.url==undefined?'/inicio/index/getFormPdf/':p.url;
        p.vurl = p.vurl==undefined?'':p.vurl;
        p.title = p.title==undefined?'Reporte':p.title;
        p.width = p.width==undefined?800:p.width;
        p.heigth = p.heigth==undefined?450:p.heigth;
        p.clos = p.clos==undefined?true:p.clos;
        win.show({
            vurl:p.url+'?vfile='+p.vurl+'&vwidth='+p.width+'&vheigth='+p.heigth+'&title='+p.title,
            mask:'inicio-tabContent'
        });
    };
    this.Msg = function(p){
        var icons = [Ext.Msg.ERROR, Ext.Msg.INFO, Ext.Msg.WARNING, Ext.Msg.QUESTION];
        var button = [Ext.Msg.CANCEL, Ext.Msg.OK, Ext.Msg.OKCANCEL, Ext.Msg.YESNO, Ext.Msg.YESNOCANCEL];
        p.title = p.title==undefined?'.:Urbano Express:.':p.title;
        p.msg = p.title==undefined?'':p.msg;
        p.buttons = p.buttons==undefined?1:p.buttons;
        p.icon = p.icon==undefined?1:p.icon;
        p.fn = p.fn==undefined?false:p.fn;
        Ext.Msg.show({
            title: p.title,
            msg: p.msg,
            buttons: button[p.buttons],
            icon: icons[p.icon],
            fn:p.fn
        });
    };
    this.setEstObj = function(_obj){
        var i;
        var est;
        var obj;
        for(i=0;i<_obj.length;++i){
            obj = _obj[i].split('|');
            est = parseInt(obj[1])==1?false:true;
            Ext.getCmp(obj[0]).setDisabled(est);
        }
    };
    this.clearTxt = function(_obj){
        var i;
        for(i=0;i<_obj.length;++i)
            Ext.getCmp(_obj[i]).setValue('');
    };
    this.notification = function(p){
        this.p = p;
        this.p.vtitle = this.p.vtitle == undefined?'Notificacion':this.p.vtitle;
        this.p.vhtml = this.p.vhtml == undefined?'M&oacute;dulos Cargados':this.p.vhtml;
        this.p.vtime = this.p.vtime == undefined?5000:parseInt(this.p.vtime);
        new Ext.ux.Notification({
            title : this.p.vtitle,
            html : this.p.vhtml,
            autoDestroy : true,
            hideDelay : this.p.vtime,
            shadow : false,
            padding : 5
        }).show(Ext.getBody());
    };
    this.concatenar = function(p){
        var vhtml = '';
        Ext.each(p, function(obj, index){
            vhtml+=obj;
        });
        return vhtml;
    }
}

var escapeHTML = function(str) {
    str = String(str);
    str = str.replace(/&/gi, '');
    
    var div = document.createElement("div");
    var text = document.createTextNode('');
    div.appendChild(text);
    text.data = str;
    
    var result = div.innerHTML;
    result = result.replace(/"/gi, '&quot;');
    
    return result;
}

var laroext = new LarSyrExt();