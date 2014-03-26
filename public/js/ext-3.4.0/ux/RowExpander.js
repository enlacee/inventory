/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 * 
 * Modifications by Mykhail Stadnik <http://mikhailstadnik.com>:
 *  - Nesting grids support added
 */
Ext.ns( 'Ext.ux.grid');

/**
 * @class Ext.ux.grid.RowExpander
 * @extends Ext.util.Observable
 * Plugin (ptype = 'rowexpander') that adds the ability to have a Column in a grid which enables
 * a second row body which expands/contracts.  The expand/contract behavior is configurable to react
 * on clicking of the column, double click of the row, and/or hitting enter while a row is selected.
 *
 * @ptype rowexpander
 */
Ext.ux.grid.RowExpander = Ext.extend( Ext.util.Observable, {

    /**
     * @cfg {Boolean} expandOnEnter
     * <tt>true</tt> to toggle selected row(s) between expanded/collapsed when the enter
     * key is pressed (defaults to <tt>true</tt>).
     */
    expandOnEnter : true,

    /**
     * @cfg {Boolean} expandOnDblClick
     * <tt>true</tt> to toggle a row between expanded/collapsed when double clicked
     * (defaults to <tt>true</tt>).
     */
    expandOnDblClick : true,

    header           : '',
    width            : 20,
    sortable         : false,
    fixed            : true,
    menuDisabled     : true,
    dataIndex        : '',
    id               : 'expander',
    lazyRender       : true,
    enableCaching    : true,
    actAsTree        : false,
    treeLeafProperty : 'is_leaf',
    appendRowClass   : true,

	constructor: function( config){
		if (!config.id) {
			config.id = Ext.id();
		}

		Ext.apply( this, config);

		var css =
			'.x-' + this.id + '-grid3-row-collapsed .x-grid3-row-expander { background-position:0 0; }' +
			'.x-' + this.id + '-grid3-row-expanded .x-grid3-row-expander { background-position:-25px 0; }' +
			'.x-' + this.id + '-grid3-row-collapsed .x-grid3-row-body { display:none !important; }' +
			'.x-' + this.id + '-grid3-row-expanded .x-grid3-row-body { display:block !important; }' +
			'.x-grid-expander-leaf .x-grid3-row-expander { background: none; }'
		;

		Ext.util.CSS.createStyleSheet( css, Ext.id());

		this.expanderClass     = 'x-grid3-row-expander';
		this.rowExpandedClass  = 'x-' + this.id + '-grid3-row-expanded';
		this.rowCollapsedClass = 'x-' + this.id + '-grid3-row-collapsed';
		this.leafClass         = 'x-grid-expander-leaf';

        this.addEvents({
            /**
             * @event beforeexpand
             * Fires before the row expands. Have the listener return false to prevent the row from expanding.
             * @param {Object} this RowExpander object.
             * @param {Object} Ext.data.Record Record for the selected row.
             * @param {Object} body body element for the secondary row.
             * @param {Number} rowIndex The current row index.
             */
            beforeexpand: true,
            /**
             * @event expand
             * Fires after the row expands.
             * @param {Object} this RowExpander object.
             * @param {Object} Ext.data.Record Record for the selected row.
             * @param {Object} body body element for the secondary row.
             * @param {Number} rowIndex The current row index.
             */
            expand: true,
            /**
             * @event beforecollapse
             * Fires before the row collapses. Have the listener return false to prevent the row from collapsing.
             * @param {Object} this RowExpander object.
             * @param {Object} Ext.data.Record Record for the selected row.
             * @param {Object} body body element for the secondary row.
             * @param {Number} rowIndex The current row index.
             */
            beforecollapse: true,
            /**
             * @event collapse
             * Fires after the row collapses.
             * @param {Object} this RowExpander object.
             * @param {Object} Ext.data.Record Record for the selected row.
             * @param {Object} body body element for the secondary row.
             * @param {Number} rowIndex The current row index.
             */
            collapse: true
        });

        Ext.ux.grid.RowExpander.superclass.constructor.call(this);

        if(this.tpl){
            if(typeof this.tpl == 'string'){
                this.tpl = new Ext.Template(this.tpl);
            }
            this.tpl.compile();
        }

        this.state = {};
        this.bodyContent = {};
    },

    getRowClass : function(record, rowIndex, p, ds){
        p.cols = p.cols-1;
        var content = this.bodyContent[record.id];
        if(!content && !this.lazyRender){
            content = this.getBodyContent(record, rowIndex);
        }
        if(content){
            p.body = content;
        }
        var cssClass = this.state[record.id] ? this.rowExpandedClass : this.rowCollapsedClass;
        if (this.actAsTree && record.get( this.treeLeafProperty)) {
        	cssClass = this.leafClass;
        }
        return cssClass;
    },

    init : function(grid){
        this.grid = grid;

        var view = grid.getView();
        view.getRowClass = this.getRowClass.createDelegate( this);

        view.enableRowBody = true;

        grid.on( 'render',        this.onRender,        this);
        grid.on( 'destroy',       this.onDestroy,       this);

        view.on( 'beforerefresh', this.onBeforeRefresh, this);
        view.on( 'refresh',       this.onRefresh,       this);
    },

    // @private
    onRender: function() {
        var grid = this.grid;
        var mainBody = grid.getView().mainBody;
        mainBody && mainBody.on( 'mousedown', this.onMouseDown, this, {delegate: '.' + this.expanderClass});
        if (this.expandOnEnter) {
            this.keyNav = new Ext.KeyNav(this.grid.getGridEl(), {
                'enter' : this.onEnter,
                scope: this
            });
        }
        if (this.expandOnDblClick) {
            grid.on('rowdblclick', this.onRowDblClick, this);
        }
        if (this.actAsTree) {
        	/**
			 * Stop bubbling parent events 
			 */
			grid.getEl().swallowEvent([ 'mouseover', 'mouseout', 'mousedown', 'click', 'dblclick' ]);
        }
    },

    // @private
    onBeforeRefresh : function() {
    	var rows = this.grid.getEl().select( '.' + this.rowExpandedClass);
    	rows.each( function( row) {
    		this.collapseRow( row.dom);
    	}, this);
    },

    // @private
    onRefresh : function() {
    	var rows = this.grid.getEl().select( '.' + this.rowExpandedClass);
    	rows.each( function( row) {
    		Ext.fly( row).replaceClass( this.rowExpandedClass, this.rowCollapsedClass);
    	}, this);
    },

    // @private    
    onDestroy: function() {
    	this.keyNav.disable();
        delete this.keyNav;
        var mainBody = this.grid.getView().mainBody;
        mainBody && mainBody.un( 'mousedown', this.onMouseDown, this);
    },

    // @private
    onRowDblClick: function( grid, rowIdx, e) {
        this.toggleRow(rowIdx);
    },

    onEnter: function( e) {
        var g = this.grid;
        var sm = g.getSelectionModel();
        var sels = sm.getSelections();
        for (var i = 0, len = sels.length; i < len; i++) {
            var rowIdx = g.getStore().indexOf(sels[i]);
            this.toggleRow(rowIdx);
        }
    },

    getBodyContent : function( record, index){
        if (!this.enableCaching) {
            return this.tpl.apply( record.data);
        }
        var content = this.bodyContent[record.id];
        if (!content){
            content = this.tpl.apply( record.data);
            this.bodyContent[record.id] = content;
        }
        return content;
    },

    onMouseDown : function(e, t){
        e.stopEvent();
        var row = e.getTarget( '.x-grid3-row');
        this.toggleRow(row);
    },

    renderer : function(v, p, record){
        p.cellAttr = 'rowspan="2"';
        return '<div class="' + this.expanderClass + '">&#160;</div>';
    },

    beforeExpand : function(record, body, rowIndex){
        if(this.fireEvent('beforeexpand', this, record, body, rowIndex) !== false){
            if(this.tpl && this.lazyRender){
                body.innerHTML = this.getBodyContent(record, rowIndex);
            }
            return true;
        }else{
            return false;
        }
    },

    toggleRow : function(row){
        if(typeof row == 'number'){
            row = this.grid.view.getRow(row);
        }
        if (Ext.fly(row).hasClass( this.leafClass)) {
        	return ;
        }
        this[Ext.fly(row).hasClass( this.rowCollapsedClass) ? 'expandRow' : 'collapseRow'](row);
    },

    expandRow : function( row){
        if(typeof row == 'number'){
            row = this.grid.view.getRow( row);
        }
        if (Ext.fly(row).hasClass( this.leafClass)) {
        	return ;
        }
        var record = this.grid.store.getAt( row.rowIndex);
        var body = Ext.DomQuery.selectNode( 'tr:nth(2) div.x-grid3-row-body', row);
        if (this.beforeExpand(record, body, row.rowIndex)){
            this.state[record.id] = true;
            Ext.fly( row).replaceClass( this.rowCollapsedClass, this.rowExpandedClass);
            this.fireEvent( 'expand', this, record, body, row.rowIndex);
        }
    },

    /**
     * Avoid memory leaks by destroying all nested grids recursively
     * 
     * @param {Ext.Element} - grid element to destroy
     */
    destroyNestedGrids : function( gridEl) {
		if (gridEl) {
			if (childGridEl = gridEl.child( '.x-grid-panel')) {
				this.destroyNestedGrids( childGridEl);
			}
			var grid = Ext.getCmp( gridEl.id);
			if (grid && (grid != this.grid)) {
				if (grid instanceof Ext.grid.EditorGridPanel) {
					var cm = grid.getColumnModel();
					for (var i = 0, s = cm.getColumnCount(); i < s; i++) {
						for (var ii = 0, ss = grid.getStore().getCount(); ii < ss; ii++) {
							if (editor = cm.getCellEditor( i, ii)) {
								editor.destroy();
							}
						}
					}
		        	cm.destroy();
		        }
				grid.destroy();
			}
		}
    },

    collapseRow : function( row){
        if (typeof row == 'number'){
            row = this.grid.view.getRow( row);
        }
        if (Ext.fly( row).hasClass( this.leafClass)) {
        	return ;
        }
	    var record = this.grid.store.getAt( row.rowIndex);
	    var body = Ext.fly( row).child( 'tr:nth(1) div.x-grid3-row-body', true);
	    if (this.fireEvent( 'beforecollapse', this, record, body, row.rowIndex) !== false) {
	    	this.destroyNestedGrids( Ext.get( row).child( '.x-grid-panel'));
	        if (record) this.state[record.id] = false;
	        Ext.fly( row).replaceClass( this.rowExpandedClass, this.rowCollapsedClass);
	        this.fireEvent( 'collapse', this, record, body, row.rowIndex);
	    }
    }
});

Ext.preg( 'rowexpander', Ext.ux.grid.RowExpander);

//backwards compatibility
Ext.grid.RowExpander = Ext.ux.grid.RowExpander;


/*
    -Agregado por Luis Remicio
    -Grid Sumary
*/
Ext.ns('Ext.ux.grid');

Ext.ux.grid.GridSummary = function(config) {
        Ext.apply(this, config);
};

Ext.extend(Ext.ux.grid.GridSummary, Ext.util.Observable, {
    init : function(grid) {
        this.grid = grid;
        this.cm = grid.getColumnModel();
        this.view = grid.getView();

        var v = this.view;

        // override GridView's onLayout() method
        v.onLayout = this.onLayout;

        v.afterMethod('render', this.refreshSummary, this);
        v.afterMethod('refresh', this.refreshSummary, this);
        v.afterMethod('syncScroll', this.syncSummaryScroll, this);
        v.afterMethod('onColumnWidthUpdated', this.doWidth, this);
        v.afterMethod('onAllColumnWidthsUpdated', this.doAllWidths, this);
        v.afterMethod('onColumnHiddenUpdated', this.doHidden, this);

        // update summary row on store's add/remove/clear/update events
        grid.store.on({
            add: this.refreshSummary,
            remove: this.refreshSummary,
            clear: this.refreshSummary,
            update: this.refreshSummary,
            scope: this
        });

        if (!this.rowTpl) {
            this.rowTpl = new Ext.Template(
                '<div class="x-grid3-summary-row x-grid3-gridsummary-row-offset">',
                    '<table class="x-grid3-summary-table" border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
                        '<tbody><tr>{cells}</tr></tbody>',
                    '</table>',
                '</div>'
            );
            this.rowTpl.disableFormats = true;
        }
        this.rowTpl.compile();

        if (!this.cellTpl) {
            this.cellTpl = new Ext.Template(
                '<td class="x-grid3-col x-grid3-cell x-grid3-td-{id} {css}" style="{style}">',
                    '<div class="x-grid3-cell-inner x-grid3-col-{id}" unselectable="on" {attr}>{value}</div>',
                "</td>"
            );
            this.cellTpl.disableFormats = true;
        }
        this.cellTpl.compile();
    },

    calculate : function(rs, cm) {
        var data = {}, cfg = cm.config;
        for (var i = 0, len = cfg.length; i < len; i++) { // loop through all columns in ColumnModel
            var cf = cfg[i], // get column's configuration
                cname = cf.dataIndex; // get column dataIndex

            // initialise grid summary row data for
            // the current column being worked on
            data[cname] = 0;

            if (cf.summaryType) {
                for (var j = 0, jlen = rs.length; j < jlen; j++) {
                    var r = rs[j]; // get a single Record
                    data[cname] = Ext.ux.grid.GridSummary.Calculations[cf.summaryType](r.get(cname), r, cname, data, j);
                }
            }
        }

        return data;
    },

    onLayout : function(vw, vh) {
        if (Ext.type(vh) != 'number') { // handles grid's height:'auto' config
            return;
        }
        // note: this method is scoped to the GridView
        if (!this.grid.getGridEl().hasClass('x-grid-hide-gridsummary')) {
            // readjust gridview's height only if grid summary row is visible
            this.scroller.setHeight(vh - this.summary.getHeight());
        }
    },

    syncSummaryScroll : function() {
        var mb = this.view.scroller.dom;

        this.view.summaryWrap.dom.scrollLeft = mb.scrollLeft;
        this.view.summaryWrap.dom.scrollLeft = mb.scrollLeft; // second time for IE (1/2 time first fails, other browsers ignore)
    },

    doWidth : function(col, w, tw) {
        var s = this.view.summary.dom;

        s.firstChild.style.width = tw;
        s.firstChild.rows[0].childNodes[col].style.width = w;
    },

    doAllWidths : function(ws, tw) {
        var s = this.view.summary.dom, wlen = ws.length;

        s.firstChild.style.width = tw;

        var cells = s.firstChild.rows[0].childNodes;

        for (var j = 0; j < wlen; j++) {
            cells[j].style.width = ws[j];
        }
    },

    doHidden : function(col, hidden, tw) {
        var s = this.view.summary.dom,
            display = hidden ? 'none' : '';

        s.firstChild.style.width = tw;
        s.firstChild.rows[0].childNodes[col].style.display = display;
    },

    renderSummary : function(o, cs, cm) {
        cs = cs || this.view.getColumnData();
        var cfg = cm.config,
            buf = [],
            last = cs.length - 1;

        for (var i = 0, len = cs.length; i < len; i++) {
            var c = cs[i], cf = cfg[i], p = {};

            p.id = c.id;
            p.style = c.style;
            p.css = i === 0 ? 'x-grid3-cell-first ' : (i == last ? 'x-grid3-cell-last ' : '');

            if (cf.summaryType || cf.summaryRenderer) {
                p.value = (cf.summaryRenderer || c.renderer)(o.data[c.name], p, o);
            } else {
                p.value = '';
            }
            if (p.value === undefined || p.value === "") {
                p.value = "&#160;";
            }
            buf[buf.length] = this.cellTpl.apply(p);
        }

        return this.rowTpl.apply({
            tstyle: 'width:' + this.view.getTotalWidth() + ';',
            cells: buf.join('')
        });
    },

    refreshSummary : function() {
        var g = this.grid, ds = g.store,
            cs = this.view.getColumnData(),
            cm = this.cm,
            rs = ds.getRange(),
            data = this.calculate(rs, cm),
            buf = this.renderSummary({data: data}, cs, cm);

        if (!this.view.summaryWrap) {
            this.view.summaryWrap = Ext.DomHelper.insertAfter(this.view.scroller, {
                tag: 'div',
                cls: 'x-grid3-gridsummary-row-inner'
            }, true);
        }
        this.view.summary = this.view.summaryWrap.update(buf).first();
    },

    toggleSummary : function(visible) { // true to display summary row
        var el = this.grid.getGridEl();

        if (el) {
            if (visible === undefined) {
                visible = el.hasClass('x-grid-hide-gridsummary');
            }
            el[visible ? 'removeClass' : 'addClass']('x-grid-hide-gridsummary');

            this.view.layout(); // readjust gridview height
        }
    },

    getSummaryNode : function() {
        return this.view.summary;
    }
});
Ext.reg('gridsummary', Ext.ux.grid.GridSummary);

/*
 * all Calculation methods are called on each Record in the Store
 * with the following 5 parameters:
 *
 * v - cell value
 * record - reference to the current Record
 * colName - column name (i.e. the ColumnModel's dataIndex)
 * data - the cumulative data for the current column + summaryType up to the current Record
 * rowIdx - current row index
 */
Ext.ux.grid.GridSummary.Calculations = {
    sum : function(v, record, colName, data, rowIdx) {
        return data[colName] + Ext.num(v, 0);
    },

    count : function(v, record, colName, data, rowIdx) {
        return rowIdx + 1;
    },

    max : function(v, record, colName, data, rowIdx) {
        return Math.max(Ext.num(v, 0), data[colName]);
    },

    min : function(v, record, colName, data, rowIdx) {
        return Math.min(Ext.num(v, 0), data[colName]);
    },

    average : function(v, record, colName, data, rowIdx) {
        var t = data[colName] + Ext.num(v, 0), count = record.store.getCount();
        return rowIdx == count - 1 ? (t / count) : t;
    }
};

