function displayAddInterventionPopup(intervention_op,id_globalobject, type) {
	var id_popup = dims_openOverlayedPopup(800,300);
	if(type == null) type = 0;
	dims_xmlhttprequest_todiv('admin.php', 'dims_op=intervention&intervention_op='+intervention_op+'&id_globalobject='+id_globalobject+'&type='+type+'&id_popup='+id_popup,'','p'+id_popup);
}
function displayEditInterventionPopup(intervention_op,id){
    var id_popup = dims_openOverlayedPopup(800,300);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=intervention&intervention_op='+intervention_op+'&id='+id+'&id_popup='+id_popup,'','p'+id_popup);
}
