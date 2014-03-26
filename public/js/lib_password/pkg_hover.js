var pkgs_html = { 'loading...':'<img src="/yui/treeview/assets/loading.gif">' };
var pkgcontainer;
var pkgreq;

function hover_pkg(mousetargetEl,pkg) {
    if (!pkg && document.mainform && document.mainform.msel) { pkg = document.mainform.msel[document.mainform.msel.selectedIndex].text; }
    if (!pkg && document.getElementById('pkgselect')) { var pkgEl=document.getElementById('pkgselect'); pkg = pkgEl.options[pkgEl.selectedIndex].text; }
    if (!pkg || pkg == '---' || pkg == 'deleted%20account') { return; }
    
    if (pkgs_html[pkg]) {
        display_pkg(mousetargetEl,pkg);
    } else {
        if (pkgreq) { YAHOO.util.Connect.abort(pkgreq); }
        display_pkg(mousetargetEl,'loading...');
        var displaycallback =
        {
            success:loadpkg,
            argument:{ 'package':pkg, 'mousetargetEl': mousetargetEl }
        };
        var sUrl = "/scripts/display_package_info?pkg=" + encodeURIComponent(pkg);
        pkgreq = YAHOO.util.Connect.asyncRequest('GET', sUrl , displaycallback, null);
    }
}
function loadpkg(o) {
    var workdiv = document.createElement('div');
    workdiv.innerHTML=o.responseText;
    var tables = workdiv.getElementsByTagName('table');
    if (!tables || tables.length == 0) {
        pkgs_html[o.argument.package] = -1;
        return;
    }
    pkgs_html[o.argument.package] = tables[0].parentNode.innerHTML;
    workdiv.innerHTML='';
    workdiv=null;
    display_pkg(o.argument.mousetargetEl,o.argument.package);
}
function dehover_pkg(mousetargetEl,pkg) {
    if (pkgcontainer) { pkgcontainer.hide(); }
}
function display_pkg(mousetargetEl,pkg) {
    if (!pkgcontainer) {
        pkgcontainer =   new YAHOO.widget.Panel("pkgpanel",
                { width:"175px",
fixedcenter:false,
close:false,
draggable:false,
modal:false,
visible:false
}
);
        }
    
    if (pkgs_html[pkg] == -1 ){ return; }
pkgcontainer.setHeader(pkg);
        pkgcontainer.setBody(pkgs_html[pkg]);
        pkgcontainer.render(document.body);
        
        var targetPt = YAHOO.util.Dom.getXY(mousetargetEl)
        targetPt[0] += mousetargetEl.offsetWidth + 1;
        targetPt[1] += mousetargetEl.offsetHeight + 1;
        pkgcontainer.cfg.setProperty('xy',targetPt);
        pkgcontainer.show();
}

