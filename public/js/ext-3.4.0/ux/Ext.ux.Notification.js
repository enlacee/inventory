/*stWindow
*
* @author  Edouard Fattal
* @date	March 14, 2008
*
* @class Ext.ux.ToastWindow
* @extends Ext.Window
*/

Ext.namespace("Ext.ux");

Ext.ux.NotificationMgr = {
    positions: []
};

Ext.ux.Notification = Ext.extend(Ext.Window, {
	initComponent: function(){
		Ext.apply(this, {
			iconCls		: this.iconCls || 'x-icon-information',
			cls			: 'x-notification',
			width		: 200,
			autoHeight	: true,
			plain		: false,
			draggable	: false,
			bodyStyle	: 'text-align:center'
		});
		if(this.autoDestroy) {
			this.task = new Ext.util.DelayedTask(this.hide, this);
		} else {
			this.closable = true;
		}
		Ext.ux.Notification.superclass.initComponent.call(this);
	},
	setMessage: function(msg){
		this.body.update(msg);
	},
	setTitle: function(title, iconCls){
		Ext.ux.Notification.superclass.setTitle.call(this, title, iconCls||this.iconCls);
	},
	onRender:function(ct, position) {
		Ext.ux.Notification.superclass.onRender.call(this, ct, position);
	},
	onDestroy: function(){
		Ext.ux.NotificationMgr.positions.remove(this.pos);
		Ext.ux.Notification.superclass.onDestroy.call(this);
	},
	cancelHiding: function(){
		this.addClass('fixed');
		if(this.autoDestroy) {
			this.task.cancel();
		}
	},
	afterShow: function(){
		Ext.ux.Notification.superclass.afterShow.call(this);
		//Ext.fly(this.body.dom).on('click', this.cancelHiding, this);
		if(this.autoDestroy) {
			this.task.delay(this.hideDelay || 5000);
		}
	},
	animShow: function(){
		this.pos = 0;
		while(Ext.ux.NotificationMgr.positions.indexOf(this.pos)>-1)
		this.pos++;
		Ext.ux.NotificationMgr.positions.push(this.pos);
		this.setSize(200,100);
		this.el.alignTo(document, "br-br", [ -20, -42-((this.getSize().height+10)*this.pos) ]);
		this.el.slideIn('b', {
			duration: 1,
			callback: this.afterShow,
			scope: this
		});
	},
	animHide: function(){
		Ext.ux.NotificationMgr.positions.remove(this.pos);
		this.el.ghost("b", {
			duration	: 1,
			scope		: this,
			callback	: this.destroy
		});
	},																																					
	focus: Ext.emptyFn 
});
/*
#info{
	position:absolute;
	height:50px;
	padding:5px 10px;
	bottom:10px;
	right:0px;
	text-align:right;
	background-color:#15428B;
}

div.info{
	position:absolute;
	height:50px;
	padding:5px 10px;
	text-align:right;
	background-color:#15428B;
}
div.es{
	bottom:10px;
	right:0px;
}

div.en{
	bottom:90px;
	right:0px;
}
H1 a{
	color:#fff;
	font-family:"Trebuchet MS",Arial,sans-serif;
	font-size:18px;
	text-decoration:none;
}
a{
	color:#fff
}

*/