document.write('<link type="text/css" rel="stylesheet" href="/js/contextMenu/jquery.contextMenu.css" media="screen" />');
function showCase(id_case, case_operation){
    var body = document.getElementsByTagName('body').item(0);
    script = document.createElement('script');
    script.src = "/js/contextMenu/jquery.contextMenu.js";
    script.type = 'text/javascript';
    body.appendChild(script);
    var id_popup = dims_openOverlayedPopup(800,600);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=case_manager&case_op='+case_operation+'&id_case='+id_case+'&id_popup='+id_popup, '', 'p'+id_popup);
}

function deleteCase(id_case) {
    dims_confirmlink('/admin.php?dims_op=case_manager&case_op=6&id_case='+id_case,'Etes vous sûr ?');
}

