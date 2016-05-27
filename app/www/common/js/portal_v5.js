var listmodules= new Array();
var wordsearch="";
var currentcampaign=0;
var currentmodule;
var timerportalrefresh;
var timerportalrefreshcampaign;
var timerusersportalrefresh;
var idrecord_over;
var idobj_over;
var idmod_over;
var timerdisplayresult;
var eventcour;
var statesearch;
var optx;
var opty;
var optw;
var opth;
var old_idobject="";
var old_idrecord="";
var old_idmodule="";
var cur_idobject;
var cur_idrecord;
var cur_idmodule;
var cur_idworkspace;
var cur_auto;
var typefavorite;
var blockclosepopup;
var timerkeepconnect;

function switchCategTagEdit(id_categ) {
	var ajax_load = "<img src='/common/img/loading16.gif' alt='loading...' />";
	$("#edit_categtag"+id_categ).css("display","block");
	$("#edit_categtag"+id_categ)
		.html(ajax_load)
		.load("admin.php?action=edit_categ_tag&id_categtag="+id_categ, {language: "php", version: 5}, function(responseText){

		});
}

function hideCategTag(id_categ) {
	$("#edit_categtag"+id_categ).css("display","none");
}

function saveCategTag(blockid) {
	var form = $('#form_categ_tag_edit'+blockid);
	$(form).submit();
}

function preview_docfile(md5id) {
	var id_popup = dims_openOverlayedPopup(1000,700);
	dims_xmlhttprequest_todiv('admin.php', 'dims_op=preview_docfile&md5id='+md5id+'&id_popup='+id_popup,'','p'+id_popup);
}

function switchDiv(name,id,nb) {
	for(i=1;i<=nb;i++) {
		document.getElementById(name+i).style.visibility="hidden";
		document.getElementById(name+i).style.display="none";
	}
	document.getElementById(name+id).style.visibility="visible";
	document.getElementById(name+id).style.display="block";
}

function displayMoreActions(id_action) {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=get_moreaction&action_id='+id_action,'||','more_'+id_action,'moreimg_'+id_action);
}

function keepConnexion() {
    timerkeepconnect = setTimeout("execKeepConnexion()", 120000);
}

function execKeepConnexion() {
    clearTimeout(timerkeepconnect);
    dims_xmlhttprequest('admin-light.php','dims_op=keep_connection');
    keepConnexion();
}

function redirectConnexion() {
    var elem=document.getElementById("page_content");

    if (elem!=null) {
        window.location.href="/admin.php";
    }
}

function printBarcode() {
    window.location.href="/admin.php?dims_op=printBarcode";
}

function updateSearchTagName() {
	var word = document.getElementById('search_desktop_tag_name').value;
	alert (word);
}

function deleteSelectedExec() {
	window.location.reload();
}

function addSelectedTag(tag,typetag,sel) {
	if (sel) {
		dims_xmlhttprequest('admin-light.php','dims_op=tag_deletetemptag&typetag='+typetag+'&tag='+tag);
	}
	else {
		dims_xmlhttprequest('admin-light.php','dims_op=tag_addtemptag&typetag='+typetag+'&tag='+tag);
	}
}

function deleteSelectedTag(tagid) {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=tag_deletesearch&tagid='+tagid,'','resultselectedtags');
	window.location.reload();
	//searchNewWord2();
}

function refreshSelectedTag() {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=tag_refreshselectedtag','','resultselectedtags');
}

function deleteSelectedTags() {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=tag_deleteselected','','resultselectedtags');
}

function updateTypeTag(typetag,tagsearch) {
	var elem=document.getElementById('filtercountrytag');
	var comp='';
	if (tagsearch==null) {
		tagsearch='';
	}

	if (elem!=null) {
		comp+="&filternametag="+elem.value;
	}

	if (tagsearch !='') {
		dims_xmlhttprequest_todiv('admin.php','dims_op=updateDetailContentTag&typetag='+typetag+comp+'&tagsearch=1','','detail_contenttag');
	}
	else {
		dims_xmlhttprequest_todiv('admin.php','dims_op=updateDetailContentTag&typetag='+typetag+comp,'','detail_contenttag');
	}
}

function displayPreviewNewsletter(id_env) {
	dims_showcenteredpopup("",970,600,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','action=previewnewsletter&id_env='+id_env,'','dims_popup');
}

function updateTypeSearch(type) {
	dims_xmlhttprequest_tofunction('admin.php','dims_op=updateTypeSearch&type='+type,reloadWindow);
}

var dernier_clic;
dernier_clic ='';

function checkdept(dept) {
	carte_svg = document.getElementById('carte').getSVGDocument();
	selectdept(carte_svg.getElementById('dept_'+dept));
}

function getElementsByClassName(classname, node) { if (!node) { node = document.getElementsByTagName('body')[0]; } var a = [], re = new RegExp('\\b' + classname + '\\b'); els = node.getElementsByTagName('*'); for (var i = 0, j = els.length; i < j; i++) { if ( re.test(els[i].className) ) { a.push(els[i]); } } return a; }


function switchMultiDiv(name,id,nb) {
    for(i=1;i<=nb;i++) {
            document.getElementById(name+i).style.visibility="hidden";
            document.getElementById(name+i).style.display="none";
    }
    document.getElementById(name+id).style.visibility="visible";
    document.getElementById(name+id).style.display="block";
}


function SwitchViewDiv(nom) {
    var elem = (document.getElementById) ? document.getElementById(nom) : eval("document.all[nom]");

    if (elem!=null) {
        if (elem.style.display=="block") {
            elem.style.visibility = "hidden";
            elem.style.display="none";
        }
        else {
            elem.style.visibility = "visible";
            elem.style.display="block";
        }
    }
}


function validChangeTypeaddTodo() {
	//var elem=document.getElementById('todo_type');
        var checks = document.getElementsByName('todo_type');

        if (checks[0].checked) val=0;
        else val=1;

	var elem=document.getElementById('contentswitchtodo');
	if (val==1) {
		elem.style.visibility='visible';
		elem.style.display='block';
	}
	else {
		elem.style.visibility='hidden';
		elem.style.display='none';
	}
}

function object_searchFileInitSearch(element) {
    $("nomsearch"+element).value="";
    $("nomsearch"+element).focus();
    dims_xmlhttprequest_tofunction('admin.php','dims_op=initSearchObject',object_searchUserExec,element);
}

function object_searchUser(element) {
    clearTimeout(timerdisplayresult);
    timerdisplayresult = setTimeout("object_searchUserExec('',"+element+")", 300);
}

function object_searchUserExec(result,element) {
    var nomsearch=document.getElementById("nomsearch"+element).value;
    dims_xmlhttprequest_todiv('/admin.php','dims_op=object_search_user&element='+element+'&nomsearch='+nomsearch,'||',"lst_tempuser"+element,"lstselectedusers"+element);
	$("nomsearch"+element).focus();
}

function object_updateUserActionFromSelected(element,op,id_user,input) {
    dims_xmlhttprequest_tofunction('/admin.php','dims_op='+op+'&element='+element+'&id_user='+id_user,object_searchUserExec,element);
}

function object_updateContactActionFromSelected(element,op,id_contact,input) {
    dims_xmlhttprequest_tofunction('/admin.php','dims_op='+op+'&element='+element+'&id_contact='+id_contact,object_searchUserExec,element);
}

function object_updateGroupActionFromSelected(element,op,id_grp,input) {
    dims_xmlhttprequest_tofunction('/admin.php','dims_op='+op+'&element='+element+'&id_grp='+id_grp,object_searchUserExec,element);
}


function displayAddTodo(event,objectid,moduleid,recordid,userid) {
	if (userid==null) {
		userid=0;
	}
	dims_showcenteredpopup("",700,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=add_todo&element='+objectid+"&moduleid="+moduleid+"&recordid="+recordid+'&userid='+userid,'','dims_popup');
}

function displayShareObject(event,idworkspace,id_object) {
	dims_showcenteredpopup("",950,600,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=shareobject_view&id_workspace='+idworkspace+'&id_object='+id_object,'','dims_popup');
}

function removeTagObject(idtagindex,idmodule,idobject,idrecord,msg) {
	if (idtagindex>0) {
		if (confirm(msg)) {
			cur_idobject=idobject;
			cur_idrecord=idrecord;
			cur_idmodule=idmodule;
			dims_xmlhttprequest_tofunction('admin-light.php','dims_op=removetagobject&idtagindex='+idtagindex,execAfterAddTag);
		}
	}
}

function removeTagObjectTemp(idtag) {
	if (idtag >0) {
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=removetagobjecttemp&idtag='+idtag,execAfterAddTag);
	}
}


function addTagObject(idtag) {
		if (cur_idobject>=0 && cur_idrecord>=0 && cur_idmodule>=0) {
			dims_xmlhttprequest_tofunction('admin-light.php','dims_op=addtagobject&idtag='+idtag+'&idrecord='+cur_idrecord+'&idobject='+cur_idobject+'&idmodule='+cur_idmodule,execAfterAddTag);
		}
}

function addNewTagObject(tag) {
		if (cur_idobject>=0 && cur_idrecord>=0 && cur_idmodule>=0) {
			dims_xmlhttprequest_tofunction('admin-light.php','dims_op=addnewtagobject&tag='+tag+'&idrecord='+cur_idrecord+'&idobject='+cur_idobject+'&idmodule='+cur_idmodule,execAfterAddTag);
		}
}

function execAfterAddTag() {
		dims_xmlhttprequest_tofunction('admin.php','dims_op=tagblockdisplay&idobject='+cur_idobject+'&idrecord='+cur_idrecord+'&idmodule='+cur_idmodule,execAfterAddTagSuite);
}

function execAfterAddTagSuite(result) {
		dims_getelem('tagblockdisplay').innerHTML=result;
		dims_getelem('searchtag').value="";
		dims_getelem('blockresulttags').innerHTML="";
		dims_getelem('searchtag').focus();
}

function searchTag(idmodule,idobject,idrecord) {
		cur_idobject=idobject;
		cur_idrecord=idrecord;
		cur_idmodule=idmodule;
		clearTimeout(timerdisplayresult);
		timerdisplayresult = setTimeout("searchExecuteTag()", 300);
}

function searchExecuteTag() {
	var elem=dims_getelem('searchtag');
	if (elem!=null) {
		dims_getelem('searchtag').focus();
		dims_xmlhttprequest_todiv('admin.php','dims_op=searchtag&idobject='+cur_idobject+'&idrecord='+cur_idrecord+'&idmodule='+cur_idmodule+'&tag='+elem.value,'','blockresulttags');
	}
}

function displayPreview(idobject,idrecord,idmodule) {
	if (idobject!=null && idobject>0) {
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=object_properties&idrecord='+idrecord+'&idobject='+idobject+'&idmodule='+idmodule,displayPreview);
	}
	else {
		document.getElementById('dims_popup').innerHTML="";
		dims_showcenteredpopup("",950,600,'dims_popup');
		dims_xmlhttprequest_todiv('admin.php','dims_op=object_detail_properties&preview=all','',"dims_popup");
	}
}

function refreshDesktopRight() {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=refresh_desktop_right','',"desktop_widget_right");
}

function detectOpenPopup(event) {

	if (!blockclosepopup) {
		var elem=document.getElementById("dims_popup");
	   x=event.clientX;
	   y=event.clientY+window.scrollY;
	   /* test si pas sur l'ascenseur */
	   if( window.innerWidth) w=window.innerWidth;
	   else w=document.body.offsetWidth;

	   if( window.innerHeight) h=window.innerHeight+window.scrollY;
	   else h=document.body.offsetHeight+window.scrollY;

	   if (elem!=null && (x <(w-20)) &&  (y <(h-20))) {
		   if (elem.style.visibility=="visible") {
			   //xbox=elem.style.left.replace("px","");
			   //ybox=elem.style.top.replace("px","");
			   xbox=elem.offsetLeft;
			   ybox=elem.offsetTop;
			   w=elem.offsetWidth;
			   h=elem.offsetHeight;

			   if ((x<xbox || x >(xbox*1+w*1)) || (y<ybox || y>(h*1+ybox*1))) {
				   elem.style.display="none";
				   elem.style.visibility="hidden";
				   cur_idobject=0;
				   cur_idworkspace=0;
				   cur_idmodule = 0;
				   cur_idrecord=0;
			   }
		   }
	   }
	}
}

function detectOpenPopupIE() {
	if (!blockclosepopup) {
		var elem=document.getElementById("dims_popup");
		x=event.clientX;
		y=event.clientY;

		/* test si pas sur l'ascenseur */
		if( window.innerWidth) w=window.innerWidth;
		else w=document.body.offsetWidth;

		if( window.innerHeight) h=window.innerHeight;
		else h=document.body.offsetHeight;

		if (elem!=null && (x <(w-20)) &&  (y <(h-20))) {
			if (elem.style.visibility=="visible" ) {
				xbox=elem.style.left.replace("px","");
				ybox=elem.style.top.replace("px","");

				w=elem.offsetWidth;
				h=elem.offsetHeight;

				if ((x<xbox || x >(xbox*1+w*1)) || (y<ybox || y>(h*1+ybox*1))) {
					elem.style.display="none";
					elem.style.visibility="hidden";
					cur_idobject=0;
					cur_idworkspace=0;
					cur_idmodule = 0;
					cur_idrecord=0;
				}
			}
		}
	}
}

function modifyViewTicket(type) {
    var elem=document.getElementById("contentdesktopticket");
    if (elem!=null) {
        if (type==0) {
            elem.style.display="none";
            elem.style.visibility="hidden";
        }
        else {
            elem.style.display="block";
            elem.style.visibility="visible";
        }
    }
}

function searchUserWorkflow(id_object,id_action) {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("searchUserWorkflowExec("+id_object+","+id_action+")", 200);
}

function searchUserWorkflowExec(id_object,id_action) {
	var nomsearch=document.getElementById("dims_workflow_userfilter"+id_action).value;
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=workflow_search_users&dims_workflow_userfilter='+nomsearch+'&id_object='+id_object+'&id_action='+id_action,'',"div_workflow_search_result"+id_action);
}

function searchUserShare() {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("searchUserShareExec()", 200);
}

function searchUserShareExec() {
	var nomsearch=document.getElementById("dims_shares_userfilter").value;
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=shares_search_users&dims_shares_userfilter='+nomsearch,'',"div_shares_search_result");
}

function switchModuleDisplay(idmodule) {
	var elem=dims_getelem("content"+idmodule);
	var elemtmp;
	var desktopdetail=dims_getelem("desktop_detail_content");
	var desktopright=dims_getelem("desktop_right_content");

	for(i=0;i<listmodules.length;i++) {
		opttxt=dims_getelem("modresult"+listmodules[i]);
		opttxt.className="modresult";

		if (listmodules[i]!=idmodule) {
			elemtmp=dims_getelem("content"+listmodules[i]);
			elemtmp.style.display="none";
			elemtmp.style.visibility="hidden";
		}
	}

	if (elem!=null) {
		if (elem.style.display=="block") {
			elem.style.display="none";
			elem.style.visibility="hidden";
			dims_xmlhttprequest('admin.php','dims_op=reset_currentobject');

			desktopdetail.style.visibility="hidden";
			desktopdetail.style.display="none";
			desktopright.style.visibility="visible";
			desktopright.style.display="block";
		}
		else {
			elem.style.display="block";
			elem.style.visibility="visible";
			opttxt=dims_getelem("modresult"+idmodule);
			opttxt.className="modresultvert";
		}
	}
}

function displayNewServices(event, idService) {
	dims_showpopup('',220,event,'click','dims_popup',0,0);
	dims_xmlhttprequest_todiv('admin.php','action=add_service&id_service='+idService,'','dims_popup');
}

function displayAction(event,idworkspace,idmodule,idparent,idtype) {
	dims_showpopup('',350,event,'click','dims_popup',10,-60,150);
	dims_xmlhttprequest_todiv('admin.php','dims_op=displayAction&idworkspace='+idworkspace+'&idmodule='+idmodule+'&idparent='+idparent+'&idtype='+idtype,'','dims_popup');
}

function displayOptions(event,idworkspace,idmodule,idobject,idrecord,decal,displayfavorite) {
	if (cur_idobject!=-1 && (cur_idobject!=idobject || cur_idworkspace!=idworkspace || cur_idmodule != idmodule || cur_idrecord != idrecord)) {
		cur_idobject=idobject;
		cur_idworkspace=idworkspace;
		cur_idmodule = idmodule;
		cur_idrecord = idrecord;
		if (displayfavorite==null) displayfavorite=0;
		dims_showpopup('',160,event,'click','dims_popup',10,-60,decal);
		dims_xmlhttprequest_todiv('admin.php','dims_op=displayObjectOptions&idworkspace='+idworkspace+'&idrecord='+idrecord+'&idobject='+idobject+'&idmodule='+idmodule+"&displayfavorite="+displayfavorite,'','dims_popup');
	}
}

function displayImportExample(event, type){
	if (type == 'ent'){
		dims_showpopup('',390,event,'click','dims_popup',10,-60,0);
		dims_xmlhttprequest_todiv('admin.php','dims_op=displayImportExampleEnt','','dims_popup');
	}else if(type == 'ct'){
		dims_showpopup('',315,event,'click','dims_popup',10,-60,0);
		dims_xmlhttprequest_todiv('admin.php','dims_op=displayImportExample','','dims_popup');
	}
}

function initDisplayOptions(opt) {
	if (opt==0) {
		cur_idobject=-1;
		// timer to avoid display just after close window
		timerusersportalrefresh = setTimeout("initDisplayOptions(1)", 300);
	}
	else {
		cur_idobject=0;
		clearTimeout(timerusersportalrefresh);
	}
}

function displayOptionsRefresh(idworkspace,idmodule,idobject,idrecord) {
	dims_xmlhttprequest_todiv('admin.php','dims_op=displayObjectOptions&idworkspace='+idworkspace+'&idrecord='+idrecord+'&idobject='+idobject+'&idmodule='+idmodule,'','dims_popup');
}

function changeListProjectDisplay(state) {
	dims_xmlhttprequest('admin.php','dims_op=change_projectlist&state='+state);
}
function viewDesktopDetail(id,type) {
	var desktopdetail=dims_getelem("desktop_detail_content");
	var desktopright=dims_getelem("desktop_right_content");

	if (id==1) {
		desktopdetail.innerHTML="";
		desktopdetail.style.visibility="visible";
		desktopdetail.style.display="block";
		desktopright.style.visibility="hidden";
		desktopright.style.display="none";
	}
	else {
		desktopdetail.style.visibility="hidden";
		desktopdetail.style.display="none";
		desktopright.style.visibility="visible";
		desktopright.style.display="block";
		//if (type==0) timerportalrefresh = setTimeout("searchRecursiveNewsTimer()", 250);
		//else timerportalrefresh = setTimeout("searchRecursiveFavoritesTimer()", 250);
	}
}

function searchRecursiveNewsTimer() {
	clearTimeout(timerportalrefresh);
	searchRecursiveNews();
}

function searchRecursiveFavoritesTimer() {
	clearTimeout(timerportalrefresh);
	searchRecursiveFavorites(typefavorite);
}

function closePropertiesObject(mainmenu, submenu) {
	window.location.href="/admin.php?dims_mainmenu="+mainmenu+"&dims_action=public&dims_desktop=block&submenu="+submenu+"&reset_object=1";
}

function viewPropertiesObject(idobject,idrecord,idmodule,force,auto) {
	cur_idobject=idobject;
	cur_idrecord=idrecord;
	cur_idmodule=idmodule;
	if (force==null) force='';
	if (auto==null) auto=0;

	dims_ajaxloader('object_content');

	cur_auto=auto;

	var elem;
	// update img et ligne
	if (old_idmodule!="" && old_idrecord!="" && old_idobject!="") {
		//elem=dims_getelem("obj_"+old_idobject+"_"+old_idrecord+"_"+old_idmodule);
		//if (elem!=null) elem.style.backgroundColor="";

		elem=dims_getelem("img_"+old_idobject+"_"+old_idrecord+"_"+old_idmodule);
		if (elem!=null) elem.src="./common/img/arrow-right.gif";
	}

	//elem=dims_getelem("obj_"+idobject+"_"+idrecord+"_"+idmodule);
	//if (elem!=null) elem.style.backgroundColor="#CADDFF";
	elem=dims_getelem("img_"+idobject+"_"+idrecord+"_"+idmodule);
	if (elem!=null) elem.src="./common/img/arrow-green-right.gif";

	old_idobject=idobject;
	old_idrecord=idrecord;
	old_idmodule=idmodule;

	var desktopdetail=dims_getelem("object_content");
	desktopdetail.style.visibility="visible";
	desktopdetail.style.display="block";

	var desktopright=dims_getelem("desktop_right_content");
	if (desktopright!=null) {
		desktopright.style.visibility="hidden";
		desktopright.style.display="none";
	}

	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=object_properties&idrecord='+idrecord+'&idobject='+idobject+'&idmodule='+idmodule+'&auto='+auto,execviewPropertiesObject,force);
}

function execviewPropertiesObject(result,force) {
	//dims_xmlhttprequest_todiv('admin-light.php','dims_op=refreshDesktop&block_id=3&type=object&desktopobjectheight='+desktopheightobject+'&'+force+'&auto=1',"||",'object_onglet','object_content');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=refreshDesktop&block_id=3&type=object&'+force+'&auto=1',"||",'object_onglet','object_content');
//	if (!cur_auto) dims_xmlhttprequest_todiv('admin-light.php','dims_op=refreshMenuSearch',"",'searchBar_obj_container');
        window.scrollTo(0,0);
}

function viewPropertiesTicket(idticket,idobject,idrecord,idmodule) {
	// update ticket status
	dims_xmlhttprequest('admin.php','op=ticket_open&ticket_id='+idticket);

	var desktopdetail=dims_getelem("desktop_detail_content");
	var desktopright=dims_getelem("desktop_right_content");

	desktopdetail.style.visibility="visible";
	desktopdetail.style.display="block";

	if (desktopright!=null) {
		desktopright.style.visibility="hidden";
		desktopright.style.display="none";
	}

	if (idobject>0 && idrecord>0 && idmodule>0) {
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=ticket_properties&idticket='+idticket,execviewPropertiesTicket,idticket);
	}
	else
		dims_xmlhttprequest_tofunction('admin-light.php','dims_op=ticket_properties&idticket='+idticket,execviewPropertiesTicketLight,idticket);
}

function execviewPropertiesTicket(result,idticket) {
	ticketsRefresh();
	dims_getelem("desktop_detail_content").innerHTML=result;

	// on lance la previsualisation du document
	var elem=dims_getelem("desktop_detail_object_content");
	if (elem!=null) elem.innerHTML="<table width=\"100%\" height=\"400\"><tr><td valign=\"center\" align=\"center\"><img src=\"./common/img/loading.gif\" alt=\"\"></td></tr></table>";
	dims_xmlhttprequest_todiv('admin.php','dims_op=object_detail_properties','',"desktop_detail_object_content");
}

function closeObject() {
	viewDesktopDetail(0);
	dims_xmlhttprequest('admin-light.php','dims_op=object_close');
}

function execCloseObject() {
	window.location.reload();
}

function reloadWindow() {
	window.location.reload();
}

function eraseTickets() {
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=eraseticket',updateTickets);
}

function eraseTicketsSent(){
        dims_xmlhttprequest_tofunction('admin-light.php','dims_op=eraseticketsent',updateTickets);
}

function deleteSelTickets(nb) {
	var lst="";
	for (i = 0; i < nb; i++) {
		if (document.getElementById("selticket"+i).checked) {
			if (lst=="") lst=document.getElementById("selticket"+i).value;
			else lst+=","+document.getElementById("selticket"+i).value;
		}
	}
	if (lst!="") dims_xmlhttprequest_tofunction('admin-light.php','dims_op=deleteselticket&lst='+lst,updateTickets);
}

function deleteSelSentTickets(nb) {
	var lst="";
	for (i = 0; i < nb; i++) {
		if (document.getElementById("selticket"+i).checked) {
			if (lst=="") lst=document.getElementById("selticket"+i).value;
			else lst+=","+document.getElementById("selticket"+i).value;
		}
	}
	if (lst!="") dims_xmlhttprequest_tofunction('admin-light.php','dims_op=deleteselsentticket&lst='+lst,updateTickets);
}

function deleteSelSentTicketOpen(id){
        if(confirm('?tes-vous certain de vouloir supprimer ce ticket ?') && (id != "")){
            dims_xmlhttprequest_tofunction('admin-light.php','dims_op=deleteselsentticket&lst='+lst,updateTickets);
        }
}

function updateTickets() {
	ticketsRefresh();
}

function execviewPropertiesTicketLight(result) {
	ticketsRefresh();
	dims_getelem("desktop_detail_content").innerHTML=result;

}

function searchUserPlanning() {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("searchUserPlanningExec()", 300);
}

function searchUserPlanningExec() {
	var nomsearch=document.getElementById("nomsearchplanning").value;
	dims_xmlhttprequest_todiv('admin.php','op=search_contact_planning&nomsearch='+nomsearch,'',"lst_planninguser");
}

function updateUserFromSelectedPlanning(op,id_user) {
	dims_xmlhttprequest_tofunction('admin-light.php','op='+op+"&id_user="+id_user,refresh_planning);
}

function ticketOpenResponse(idticket) {
	timerusersportalrefresh = setTimeout("execOpenResponse("+idticket+")", 500);
}

function execOpenResponse(idticket) {
	clearTimeout(timerdisplayresult);
	dims_xmlhttprequest_todiv('admin.php','op=ticket_replyto&ticket_id='+idticket,'',"ticket_response_"+idticket);
}

function ticketOpenResponses(idticket) {
	timerusersportalrefresh = setTimeout("execOpenResponses("+idticket+")", 500);
}

function execOpenResponses(idticket) {
	clearTimeout(timerdisplayresult);
	dims_xmlhttprequest_todiv('admin.php','op=ticket_open_responses&ticket_id='+idticket,'',"ticket_responses_"+idticket);
}

function ticketsRefresh(page) {
	if (page==null) dims_xmlhttprequest_todiv('admin-light.php','dims_op=tickets_refresh','','contentdesktopticket');
	else dims_xmlhttprequest_todiv('admin-light.php','dims_op=tickets_refresh&page='+page,'','contentdesktopticket');
}

function checkAllTickets(nbfiles) {
	for (i = 0; i < nbfiles; i++)
		document.getElementById("selticket"+i).checked = true;
}

function uncheckAllTickets(nbfiles) {
	for (i = 0; i < nbfiles; i++)
		document.getElementById("selticket"+i).checked = false;
}

function refreshDesktopPage(mod,op,page) {
	//dims_xmlhttprequest_todiv('admin-light.php','dims_op='+op+'&moduleid='+mod+'&page='+page,'||','ressearch'+mod,'content'+mod);
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op='+op+'&moduleid='+mod+'&page='+page,refreshDesktopPageSuite,mod);
}

function refreshDesktopPageSuite(result,mod) {
if (result!=null) tabxmlvalue=result.split("||");
	var elem=dims_getelem('ressearch'+mod);
	var elemcontent=dims_getelem('content'+mod);

	if (elem!=null) elem.innerHTML= "<a href=\"javascript:void(0);\" onclick=\"javascript:switchModuleDisplay("+mod+");\">"+tabxmlvalue[0]+"</a>";
	if (elemcontent!=null) elemcontent.innerHTML= tabxmlvalue[1];
}

function refreshFavorites(idfav,iduser,idmodule,idworkspace,idobject,idrecord,value,iduserfrom,divcontent,activemode,mustreload) {
	if (activemode==1) {
		dims_xmlhttprequest_todiv('admin-light.php','dims_op=updatefavoriteobject&idfav='+idfav+'&iduserfrom='+iduserfrom+'&iduser='+iduser+'&idmodule='+idmodule+'&idworkspace='+idworkspace+'&idobject='+idobject+'&idrecord='+idrecord+'&value='+value,'',divcontent);
	}
	else {
		dims_xmlhttprequest('admin-light.php','dims_op=updatefavoriteobject&idfav='+idfav+'&iduserfrom='+iduserfrom+'&iduser='+iduser+'&idmodule='+idmodule+'&idworkspace='+idworkspace+'&idobject='+idobject+'&idrecord='+idrecord+'&value='+value+'&passivemode=1');
		if (mustreload==null) mustreload=false;
		if (mustreload) window.location.reload();
	}
}

function refreshDesktop(type,action,op,divcontent) {
	document.location.href='/admin.php?type='+type+'&action='+action+'&op='+op+'&resetunique_object=1';
	//dims_xmlhttprequest_todiv('admin.php','dims_op=refreshdesktop&type='+type+'&action='+action+'&op='+op,'',divcontent);
}

function updateTimerDesktop() {
	clearTimeout(timerusersportalrefresh);
	refreshTimerDesktop();
}

function refreshTimerDesktop() {
	/* timerusersportalrefresh = setTimeout("execrefreshDesktop()",600000); */
}

function execrefreshDesktop() {
	clearTimeout(timerusersportalrefresh);
	window.location.reload();
}

function switchOption(event,cmd,id) {
	var opttxt=dims_getelem("optionstext"+id);
	var optcmd=dims_getelem("optionscmd"+id);

	for(i=0;i<listmodules.length;i++) {
		opttxt=dims_getelem("optionstext"+listmodules[i]);
		optcmd=dims_getelem("optionscmd"+listmodules[i]);
		opttxt.style.visibility="visible";
		opttxt.style.display="block";
		optcmd.style.visibility="hidden";
		optcmd.style.display="none";
	}
	var opttxt=dims_getelem("optionstext"+id);
	var optcmd=dims_getelem("optionscmd"+id);

	opttxt.style.visibility="hidden";
	opttxt.style.display="none";
	optcmd.style.visibility="visible";
	optcmd.style.display="block";
}

function displayModuleInformation(event,idmodule) {
	eventcour=event;
	dims_showpopup('',400,eventcour);
	dims_xmlhttprequest_todiv('admin.php','dims_op=displaymodinfo&idmodule='+idmodule,'','dims_popup');
}

function displayAddFiles(event,id_module) {
	dims_showcenteredpopup("",700,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=doc_uploadform_file&id_module='+id_module,'','dims_popup');
}

function displayDesktopBookmarks(event) {
	dims_showcenteredpopup("",700,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=display_bookmarks','','dims_popup');
}

function displayCreateTags(event,type_tag) {
	dims_showcenteredpopup("",300,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=object_create_tag&type='+type_tag,'','dims_popup');
}

function displayEditTag(event, id){
	dims_showcenteredpopup("",300,500,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=object_edit_tag&id='+id,'','dims_popup');
}

function displayMapWorkspaces(event) {
	var eventcour=event;
	/*var dims_popup=document.getElementById('dims_popup');
	var x=200;
	var y=50;
        var height=500;

	dims_popup.style.visibility='visible';
	dims_popup.style.display='block';
	dims_popup.style.position="absolute";

	if( window.innerWidth) {
		dims_popup.style.width=(window.innerWidth *4)/ 5+"px";
		x = (window.innerWidth / 2) - (dims_popup.offsetWidth / 2);
  		y = 60; //(window.offsetHeight / 2) - (dims_popup.offsetHeight / 2);
                height=window.offsetHeight / 3 * 2;
	}
  	else {
  		dims_popup.style.width=(document.body.offsetWidth *4)/ 5+"px";
		dims_popup.style.height=60; //(document.body.offsetHeight *4)/ 5+"px";
		x = (document.body.offsetWidth / 2) - (dims_popup.offsetWidth / 2);
  		//y = (document.body.offsetHeight / 2) - (dims_popup.offsetHeight / 2);
  		y = 60;
                height=document.body.offsetHeight / 3 * 2;
  	}

        if (height<400) height=400;
  	dims_popup.style.top = y+"px";
  	dims_popup.style.left = x+"px";
  	dims_popup.style.height = height+"px";
        dims_popup.style.display = "block";
        */
        dims_showcenteredpopup("",1000,450,'dims_popup');

        dims_xmlhttprequest_todiv('admin.php','dims_op=view_workspace','','dims_popup');

}

function displayCodeOfConduct() {

	var dims_popup=document.getElementById('dims_popup');
	var x=200;
	var y=50;

	dims_popup.style.visibility='visible';
	dims_popup.style.display='block';
	dims_popup.style.position="absolute";

	if( window.innerWidth) {
		dims_popup.style.width=(window.innerWidth *4)/ 5+"px";
		x = (window.innerWidth / 2) - (dims_popup.offsetWidth / 2);
  		y = 60; //(window.offsetHeight / 2) - (dims_popup.offsetHeight / 2);
	}
  	else {
  		dims_popup.style.width=(document.body.offsetWidth *4)/ 5+"px";
		dims_popup.style.height=60; //(document.body.offsetHeight *4)/ 5+"px";
		x = (document.body.offsetWidth / 2) - (dims_popup.offsetWidth / 2);
  		//y = (document.body.offsetHeight / 2) - (dims_popup.offsetHeight / 2);
  		y = 60;
  	}

  	dims_popup.style.top = y+"px";
  	dims_popup.style.left = x+"px";
  	dims_popup.style.display = "block";

	dims_xmlhttprequest_todiv('admin.php','dims_op=view_code_of_conduct','','dims_popup');

}

function dislayResultOverTimer(event,idmodule,idobject,idrecord) {
	eventcour=event;
	dims_showpopup('',400,eventcour);
	dims_xmlhttprequest_todiv('admin.php','dims_op=displaysearchresult&idmodule='+idmodule+'&idobject='+idobject+'&idrecord='+idrecord,'','dims_popup');
}

function dislayContentOverTimer(event,idmodule,idobject,idrecord) {
	eventcour=event;
	dims_showpopup('',600,eventcour,'click',"dims_popup2");
	dims_xmlhttprequest_todiv('admin.php','dims_op=displaycontent&moduleid='+idmodule+'&idobject='+idobject+'&idrecord='+idrecord,'','dims_popup2');
}

function displaySharesModules(event,idobject,idrecord,idmodule) {
	dims_showpopup('',400,event);
	dims_xmlhttprequest_todiv('admin.php','dims_op=shares_viewmodule&idmodule='+idmodule+'&idobject='+idobject+'&idrecord='+idrecord,'','dims_popup');
}

function saveSharesModules(idobject,idrecord,idmodule) {
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=shares_savemodule&idmodule='+idmodule+'&idobject='+idobject+'&idrecord='+idrecord,saveSharesModulesSuite,idrecord);
}

function saveSharesModulesSuite(result,idmodule) {
	dims_getelem('dims_popup').style.visibility='hidden';
	refreshSharesModules(idmodule);
}

function refreshSharesModules(idmodule) {
	dims_xmlhttprequest_todiv('admin.php','dims_op=shares_refreshmodule&idmodule='+idmodule,'||','adminmod_'+idmodule,'adminviewmod_'+idmodule);
}

function displayDomainInfo(event,typeaccess,iddomain) {
	//dims_showpopup('',460,event,'click');
	dims_showcenteredpopup("",500,600,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=domains_viewdomain&iddomain='+iddomain+'&typeaccess='+typeaccess,'','dims_popup');
}

function displayTemplateInfo(event,idworkspace) {
	//dims_showpopup('',400,event,'click');
	dims_showcenteredpopup("",500,700,'dims_popup');
	dims_xmlhttprequest_todiv('admin.php','dims_op=templates_view&idworkspace='+idworkspace,'','dims_popup');
}

function dislayResultOver(idmodule,idobject,idrecord) {
	dims_showpopup('',400,eventcour,'click');
	dims_xmlhttprequest_todiv('admin.php','dims_op=displaysearchresult&idmodule='+idmodule+'&idobject='+idobject+'&idrecord='+idrecord,'','dims_popup');
}

function undodisplayResult() {
	clearTimeout(timerdisplayresult);
}

function switchsearch() {
	var elem=dims_getelem('block_search');
	var state=0;

	if (elem!=null) {
		if(elem.style.display!='block') {
			elem.style.display='block';
			elem.style.visibility='visible';
			dims_getelem('wordsearch').focus();
			state=1;
		}
		else {
			elem.style.display='none';
			elem.style.visibility='hidden';
			state=0;
		}
		dims_xmlhttprequest('admin.php','dims_op=active_search&state='+state);

		//if (state==0) searchNews();
		//else searchEmpty();
	}
	//else {
	//	document.location="admin.php?dims_mainmenu=0&dims_desktop=portal&displaysearch=1";
	//}
}

function changetagsearch() {
	var elem=dims_getelem('checktagsearch');
	var state=0;

	if (elem!=null) {
		if(elem.checked) state=1;
		else {
			dims_getelem('resulttags').innerHTML="";
			state=0;
		}
		dims_getelem('wordsearch').focus();
		dims_xmlhttprequest('admin.php','dims_op=checktagsearch&state='+state);
	}

}

/******************************************************************************************************/
/*      Function clipboard                                                                            */
/******************************************************************************************************/
var currentselection="";
var elemselection;
var elemsrc;

function detectSelectedText(event) {
	/*
	if (document.selection!=null && document.selection.createRange().text)
		sel = document.selection.createRange().text;
	else if (window.getSelection())
		sel = window.getSelection();

	event= (!event) ? window.event : event;
	// on peut d�tecter le type de champ si c'est un input ou textarea
	var src= (event) ? event.target : event.srcElement;

	if((src.tagName=="INPUT" && src.type=="text") || src.type=="textarea") {
		elemsrc=src;
		elemselection=src;

	}

	if (sel != "") {
		// on appelle la barre de proposition de copie
		if (currentselection!=sel) {
			currentselection=""+sel;
			dims_showpopup('',90,event,'click','dims_clipboard');
			dims_xmlhttprequest_todiv('admin.php','dims_op=clipboard_showcmd','','dims_clipboard');
		}

	}
	else {
		if((src.tagName=="INPUT" && src.type=="text") || src.type=="textarea") {

			dims_showpopup('',90,event,'click','dims_clipboard',0,-55);
			dims_xmlhttprequest_todiv('admin.php','dims_op=clipboard_showcmdget','','dims_clipboard');
		}
	}
	*/
}

function clipboard_getallSuite(result) {
	if((elemsrc.tagName=="INPUT" && elemsrc.type=="text") || elemsrc.type=="textarea") {
		elemselection.value=result;
	}
	dims_hidepopup('dims_clipboard');
	elemselection.focus();
}

function clipboard_copy(event) {
	if (document.selection!=null && document.selection.createRange().text)
		sel = document.selection.createRange().text;
	else if (window.getSelection())
		sel = window.getSelection();

	if (sel != "") {
		dims_xmlhttprequest('admin.php','dims_op=clipboard_add&paste='+window.getSelection());
		dims_hidepopup('dims_clipboard');
		//dims_showpopup('',400,event,'click','dims_clipboard');
		//dims_xmlhttprequest_todiv('admin.php','dims_op=clipboard_show','','dims_clipboard');
	}
	else
		alert("Aucune selection !");
}

function clipboard_copyto(id_element) {
	var elem = dims_getelem(id_element);
	elem.select();
}

function clipboard_get(element) {
	dims_xmlhttprequest_tofunction('admin-light.php','dims_op=clipboard_pasteall',clipboard_getallSuite);
}

function clipboard_delete(id) {
	dims_xmlhttprequest_todiv('admin.php','dims_op=clipboard_delete&id='+id, '' ,'dims_clipboard');
}

function dims_show_clipboard(event) {
	dims_showpopup('',400,event,'click','dims_clipboard');
	dims_xmlhttprequest_todiv('admin.php','dims_op=clipboard_show','','dims_clipboard');
}

function zoomOuputBlock(mod) {
	var contentzoom=$("zoomContent");
	contentzoom.innerHTML="";
	contentzoom.style.visibility="hidden";
	contentzoom.style.display="none";

	var elem = $("dimsminimize");
	elem.style.display="none";
	elem.style.visibility="hidden";

	elem = $("dimsblock");
	elem.style.display="block";
	elem.style.visibility="visible";
}

function zoomBlock(mod) {
	var elem = $("dimsblock");
	elem.style.display="none";
	elem.style.visibility="hidden";

	elem = $("dimsminimize");
	elem.style.display="block";
	elem.style.visibility="visible";

	viewActiveZoom(mod);
}


function viewActiveZoom(mod) {
	var contentzoom=$("zoomContent");

	contentzoom.style.visibility="visible";
	contentzoom.style.display="block";
	var elem=$("block-"+mod);
	var ch=elem.innerHTML;
	ch=ch.replace("content"+mod,"zoomcontent"+mod);
	ch=ch.replace("moduleContent0","moduleContentZoom");
	ch=ch.replace("moduleContent1","moduleContentZoom");
	ch=ch.replace("closestate","closestatehide");
	ch=ch.replace("zoomBlock","zoomOuputBlock");
	ch=ch.replace("zoom.png","zoomouput.png");

	contentzoom.innerHTML=ch.replace("search_explorer_main","search_explorer_mainzoom");
}

function updateValidate(mod,refresh) {
    if (refresh==null) refresh=false;
	//var elem=$("ressearch"+mod);
	//elem.innerHTML="<font class=\"fontgray\">-</font>";
    if (refresh)
        dims_xmlhttprequest_tofunction('admin-light.php',"dims_op=updatevalidate&moduleid="+mod,updateValidateSuite);
	else
        dims_xmlhttprequest("admin-light.php","dims_op=updatevalidate&moduleid="+mod);
}

function updateValidateSuite() {
    document.location.reload();
}

function updateAllValidate() {
	dims_xmlhttprequest("admin-light.php","dims_op=updateallvalidate");
}

function updateState(mod,state) {
	elemstate=$('state'+mod);
	var elemdiv=$("content"+mod);
	var elemimg=$("bkimg"+mod);
	var h=0;

	var src="";

	if (elemstate.innerHTML=="0") {
		h="165px";
		state=1;
		elemstate.innerHTML="1";
		src="./common/img/minimize.gif";

		if (statesearch=="search") {
			searchWordSuite("",mod);
		}
		else {
			dims_xmlhttprequest_todiv('admin-light.php','dims_op=searchnews&moduleid='+mod,'||','ressearch'+mod,'content'+mod);
		}
	}
	else {
		h="0px";
		state=0;
		elemstate.innerHTML="0";
		src="./common/img/maximize.gif";
	}

	dims_xmlhttprequest("admin-light.php","dims_op=updatestate&module="+mod+"&state="+state);
	elemdiv.style.height=h;
	elemimg.src=src;
}

function addTags(event,data) {
 	dims_showpopup('',350, event);
	timerportalrefresh = setTimeout("execaddTags('"+data+"')", 100);
}

function execaddTags(data) {
	clearTimeout(timerportalrefresh);
	dims_xmlhttprequest_todiv("admin-light.php",data,'',"dims_popup");
}

function refreshAgenda(moduleid,month,year) {
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=searchnews&moduleid='+moduleid+'&agenda_month_block='+month+'&agenda_year_block='+year,'||','ressearch'+moduleid,'content'+moduleid);
}


// Classe onglet
function onglet (container, onglet, containerAjax, initCallback, initParametres, ongletSelected, useHash) {
	if(!ongletSelected){
		ongletSelected	= 1;
	}

	var local 				= this;

	this.onglet 			= onglet;
	this.onglets   			= $(onglet).childElements();
	this.ongletSelected 	= ongletSelected;
	this.nbOnglets 			= 1;
	this.containerAjax 		= containerAjax;

	this.initCallback 		= initCallback;
	this.initParametres 	= initParametres;

	this.special			= new Array();
	this.specialOpen 		= new Array();

	this.useHash 			= useHash;

	// __CONSTRUCT permettant de compter le nombre d'onglets total et de leur assigner la classe qui va bien
	this.init			= function () {
		this.nbOnglets 	= this.onglets.length;

		for (i=1; i <= this.nbOnglets; i++) {
			$(this.onglet+i+'_label').className 	= (i == this.ongletSelected) ? 'onglet_actif' : 'onglet_inactif';
			$(this.onglet+i+'_before').className 	= (i == this.ongletSelected) ? 'onglet_actif_before' : 'onglet_inactif_before';
			$(this.onglet+i+'_after').className 	= (i == this.ongletSelected) ? 'onglet_actif_after' : 'onglet_inactif_after';
		}

		if (typeof(this.initCallback) == 'function') {
			this.initCallback(this.initParametres);
		}
	};

	// On lance le _CONSTRUCT manuellement afin d'emuler le comportement normal de POO
	local.init();

	// Methode placant l'onglet ID (ou tous les onglets si !ID) en inactif
	this.hide 			= function (id) {
		// Si id n'est pas defini, on met tous les onglets en inactif sinon, seulement celui choisi
		for (i=1; i <= this.nbOnglets; i++) {
			if (!id) {
				$(this.onglet+i+'_label').className 	= 'onglet_inactif';
				$(this.onglet+i+'_before').className 	= 'onglet_inactif_before';
				$(this.onglet+i+'_after').className 	= 'onglet_inactif_after';
			} else if (id != '' && i == id) {
				$(this.onglet+i+'_label').className 	= 'onglet_inactif';
				$(this.onglet+i+'_before').className 	= 'onglet_inactif_before';
				$(this.onglet+i+'_after').className 	= 'onglet_inactif_after';
			}
		}
	};

	// Methode placant l'onglet ID en actif et appelant l'INSTANCE via Ajax
	this.show 			= function (id, callback, parametres) {
		this.ongletSelected = id;

		for (i=1; i <= this.nbOnglets; i++) {
			$(this.onglet+i+'_label').className 	= (i == id) ? 'onglet_actif' : 'onglet_inactif';
			$(this.onglet+i+'_before').className 	= (i == id) ? 'onglet_actif_before' : 'onglet_inactif_before';
			$(this.onglet+i+'_after').className 	= (i == id) ? 'onglet_actif_after' : 'onglet_inactif_after';
		}

		if (typeof(callback) == 'function') {
			callback(parametres);
		}
		/*
		if (this.useHash) {
			window.location.href 	= '#'+id+'/'+parametres;
			lastHash 				= '#'+id+'/'+parametres;
		}
		*/
	};

	// Methode permettant la mise a jour d'un element
	this.update 		= function (id, callback, parametres, titre) {
		// Actions de l'onglet
		$(this.onglet+id).onclick 				= function () {local.show(id, callback, parametres);};
		$(this.onglet+id+'_label').innerHTML 	= titre;

		// Et on affiche l'onglet en question
		local.show(id, callback, parametres);
	};

	// Methode permettant d'ajouter un onglet dans le CONTAINERONGLET avec un TITRE et appelant l'INSTANCE via Ajax
	this.add			= function (titre, callback, parametres) {
		// Cachage de tous les onglets presents
		local.hide();

		if(in_array(titre, this.special)) {
			if (!array_key_exists(titre, this.specialOpen)) {
				this.nbOnglets++;
				this.specialOpen[titre] 	= this.nbOnglets;
				var valid 					= true;
			} else {
				local.update(this.specialOpen[titre], callback, parametres, titre);
				var valid 					= false;
			}
		} else {
			var valid 					= true;
		}

		if (valid == true) {
			var temp_nbOnglets 			= this.nbOnglets;

			// Parametrage du li
			var newLi 					= document.createElement('li');
			newLi.onclick 				= function () {local.show(temp_nbOnglets, callback, parametres);};
			newLi.setAttribute('id', this.onglet+this.nbOnglets);

			// Parametrage du nouvel onglet
			var newOnglet     			= document.createElement('span');
			newOnglet.className 		= 'onglet_actif';
			newOnglet.innerHTML 		= titre;
			newOnglet.setAttribute('id', this.onglet+this.nbOnglets+'_label');

			// Arrondi gauche
			var newOngletBefore 		= document.createElement('span');
			newOngletBefore.className 	= 'onglet_actif_before';
			newOngletBefore.setAttribute('id', this.onglet+this.nbOnglets+'_before');

			// Arrondi droite
			var newOngletAfter	 		= document.createElement('span');
			newOngletAfter.className 	= 'onglet_actif_after';
			newOngletAfter.setAttribute('id', this.onglet+this.nbOnglets+'_after');

			// Creation du nouvel onglet
			$(this.onglet).appendChild(newLi);
			newLi.appendChild(newOngletBefore);
			newLi.appendChild(newOnglet);
			newLi.appendChild(newOngletAfter);

			// On affiche l'onglet
			local.show(this.nbOnglets, callback, parametres);
		}
	};
}



function Loader(){
	this.initialize = function () {
		this.script 	= $H();
		this.loader 	= false;

	}

	this.add = function (script) {
		this.script.set(this.script.size(), script);

	}

	this.load = function () {
		var local 		= this;

		if (!this.loader && !document.loaded) {
			this.loader 	= setInterval(function () {local.load();}, 100);
		}

		if (document.loaded){
			clearInterval(this.loader);

			this.script.toJSON().evalJSON();

			this.script.each(function (script) {
				if (typeof(script.value) === "function") {
					script.value();
				}
			});
		}
	}
}

/*var Popup = jQuery.create({
	initialize: function (name, file, width, minHeight, top, title, closable, draggable) {
		var local 		= this;

		this.name 		= name;
		this.file 		= file;
		this.closable 	= closable;
		this.draggable 	= draggable;
		this.width 		= width;
		this.top 		= top;
		this.title 		= title;
		this.interval 	= setInterval(function(){local.set();}, 100);
		this.blackboard = 'blackboard'+this.name;
		this.larg 		= 0;
		this.haut 		= 0;
		this.minHeight 	= minHeight;

		var detect 		= navigator.userAgent.toLowerCase();
		this.ie 		= detect.indexOf('msie') + 1;
	},

	is_numeric: function (mixed_var) {
		return (typeof(mixed_var) === 'number' || typeof(mixed_var) === 'string') && mixed_var !== '' && !isNaN(mixed_var);
	},

	set: function () {
		var local 		= this;

		if (document.loaded) {
			clearInterval(this.interval);

			if ($(this.blackboard) == null) {
				var obj = document.createElement('div');

				obj.setAttribute('id', this.blackboard);
				document.body.appendChild(obj);
			}

			$(this.blackboard).addClassName('blackboard');
			$(this.blackboard).onclick 				= function () {
				local.hide();
			};

			if ($(this.name) == null) {
				var box = document.createElement('div');

				box.setAttribute('id', this.name);
				document.body.appendChild(box);
			}

			$(this.name).addClassName('popup');
			$(this.name).style.width 				= this.width + 'px';
			$(this.name).style.top 					= this.top + 'px';

			var title = document.createElement('div');

			title.setAttribute('id', this.name+'_title');
			$(this.name).appendChild(title);
			$(this.name+'_title').addClassName('title');
			$(this.name+'_title').innerHTML = this.title;

			if (this.draggable) {
				var move = document.createElement('span');

				move.setAttribute('id', this.name+'_move');
				$(this.name).appendChild(move);
				$(this.name+'_move').addClassName('move');
				$(this.name+'_move').title = "D�placer";

				new Draggable(this.name, {handle: this.name+'_move'});

				$(this.name+'_title').style.cursor 	= 'move';
				new Draggable(this.name, {handle: this.name+'_title'});
			}

			if (this.closable) {
				var close = document.createElement('span');

				close.setAttribute('id', this.name+'_close');
				$(this.name).appendChild(close);
				$(this.name+'_close').addClassName('close');
				$(this.name+'_close').title 	= "Fermer";
				$(this.name+'_close').onclick 	= function () {
					local.hide();
				};
			}

			var container = document.createElement('div');

			container.setAttribute('id', this.name+'_container');
			$(this.name).appendChild(container);
			$(this.name+'_container').addClassName('container');

			var container2 = document.createElement('div');

			container2.setAttribute('id', this.name+'_container2');
			$(this.name+'_container').appendChild(container2);
			$(this.name+'_container2').addClassName('container2');

			var content = document.createElement('div');

			content.setAttribute('id', this.name+'_content');
			$(this.name+'_container2').appendChild(content);
			$(this.name+'_content').addClassName('content');
			$(this.name+'_content').insert('Chargement en cours ...');

			$(this.blackboard).hide();
			$(this.name).hide();
		}
	},

	show: function () {
		var local 	= this;

		new Ajax.Updater(this.name+'_content', this.file);

		if (window.innerHeight){
			this.larg 	= (window.innerWidth);
			this.haut 	= (window.innerHeight);
		} else {
			this.larg 	= (document.body.clientWidth);
			this.haut 	= (document.body.clientHeight);
		}

		$(this.name).style.left 			= ((this.larg - this.width)/2) + 'px';

		$(this.blackboard).style.zIndex 	= 1000;
		$(this.name).style.zIndex 			= 1001;

		$(this.blackboard).appear({duration:0.5, from: 0.0, to:0.8});
		$(this.name).appear({duration:0.5, from: 0.0, to:1.0});

		setTimeout(function(){local.resize();}, 500);
	},

	hide: function () {
		var local 		= this;

		if (this.closable && typeof(this.closable) == 'string') {
			location.hash 	= '#'+this.closable;
		}

		$(this.blackboard).appear({duration:0.5, from: 0.8, to:0.0});
		$(this.name).appear({duration:0.5, from: 1.0, to:0.0});

		setTimeout(function(){local.unset();}, 500);
	},

	unset: function () {
		$(this.blackboard).hide();
		$(this.blackboard).style.zIndex = 0;

		$(this.name).hide();
		$(this.name).style.zIndex 		= 0;
	},

	resize: function () {
		var local 		= this;

		if (this.ie) {
			var max 		= (this.haut - this.top + 30)/2;
		} else {
			var max 		= this.haut - (this.top*2);
		}

		if (local.is_numeric(this.minHeight) && this.minHeight > 0) {
			if (this.minHeight > max) {
				$(this.name).style.height 	= max + 'px';
			} else {
				$(this.name).style.height 	= this.minHeight + 'px';
			}
		} else {
			if ($(this.name).getHeight() > max) {
				$(this.name).style.height 	= max + 'px';
			}
		}

		$(this.name+'_content').style.height 	= ($(this.name).getHeight() - 55) + 'px';
	}
});*/

dims_window_onload_functions = new Array();

function switchElementDisplay(id) {
	var elem	= dims_getelem(id);
	var img		= dims_getelem('img'+id);

	if (elem.style.display=="block") {
		elem.style.display		= "none";
		elem.style.visibility	= "hidden";
		img.src					= "./common/img/plus.gif";
	}else {
		elem.style.display		= "block";
		elem.style.visibility	= "visible";
		img.src					= "./common/img/minus.gif";
	}

}

function dims_window_onload_stock(func) {
	dims_window_onload_functions[dims_window_onload_functions.length] = func;
}

function dims_window_onload_launch()
{
	window.onload = function() {
		for(i = 0; i < dims_window_onload_functions.length; i++) {
			dims_window_onload_functions[i]();
		}
	}
}

function dims_openwin(url,w,h,name) {
   var top = (screen.height-(h+60))/2;
   var left = (screen.width-w)/2;

   if(name == '') name = 'dimswin';
   dimswin=window.open(url,name,'top='+top+',left='+left+',width='+w+', height='+h+', status=no, menubar=no, toolbar=no, scrollbars=yes, resizable=yes, screenY=20, screenX=20');
   dimswin.focus();
}


function dims_confirmform(form, message) {
	if (confirm(message)) form.submit();
}

function dims_confirmlink(link, message) {
	if (confirm(message)) location.href=link;
}

function dims_switchstyle(obj, opacity) {
	obj.style.filter='alpha(opacity:'+(opacity)+')';
	obj.style.MozOpacity = opacity/100;
}

function dims_switchdisplay(id) {
	e = dims_getelem(id);
	if (e) e.style.display = (e.style.display == 'none') ? 'block' : 'none';
}

function dims_validatefield(field_label, field_object, field_type,objform) {
	var ok = true;
	var i;
	var nbpoint = 0;
	var msg = new String();
	var reg = new RegExp("<FIELD_LABEL>","gi");

	if (field_object) {

		field_value = field_object.value;
		if (field_type == 'selected' || field_type == 'select') {
			msg = lstmsg[9];
			ok = (field_object.selectedIndex > 0);
		}

		if (field_type == 'checked' || field_type == 'radio') {
			msg = lstmsg[9];
			ok = false;
			var checks = document.getElementsByName(field_object);

			for (c = 0; c < checks.length; c++) {
				if (checks[c].checked) ok = true;
			}
		}

		if (field_type == 'email') {

			var email = field_value;
			var aroba = email.indexOf("@");

			if (aroba == -1)
			{
				ok = false;
				msg = lstmsg[0];
			}

			if (ok)
			{
				var point = email.indexOf(".", aroba);
				if ((point == -1) || (point == (aroba + 1)))
				{
					ok=false;
					msg = lstmsg[1];
				}
			}

			if (ok)
			{
				var point = email.lastIndexOf(".");
				if ((point + 1) == email.length)
				{
					ok = false;
					msg = lstmsg[2];
				}
			}

			if (ok)
			{
				point = email.indexOf("..")
				if (point != -1)
				{
					ok = false;
					msg = lstmsg[3];
				}
			}
		}

		if (field_type == 'emptyemail')
		{
			if (field_value.length!=0)
			{
				var email = field_value;
				var aroba = email.indexOf("@");

				if (aroba == -1)
				{
					ok = false;
					msg = lstmsg[0];
				}

				if (ok)
				{
					var point = email.indexOf(".", aroba);
					if ((point == -1) || (point == (aroba + 1)))
					{
						ok=false;
						msg = lstmsg[1];
					}
				}

				if (ok)
				{
					var point = email.lastIndexOf(".");
					if ((point + 1) == email.length)
					{
						ok = false;
						msg = lstmsg[2];
					}
				}

				if (ok)
				{
					point = email.indexOf("..")
					if (point != -1)
					{
						ok = false;
						msg = lstmsg[3];
					}
				}
			}
		}

		if (field_type == 'color')
		{
			var color = new dims_rgbcolor(field_value);
			if (!color.ok)
			{
				ok = false;
				msg = lstmsg[10];
			}
		}

		if (field_type == 'string' || field_type == 'text')
		{
			if (field_value.replace(/(^\s*)|(\s*$)/g,'').length==0)
			{
				ok = false;
				msg = lstmsg[4];
			}
		}

		if (field_type == 'int')
		{
			if (field_value.length==0 || field_value.length>12) ok = false;
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (!ok) msg = lstmsg[5];
		}

		if (field_type == 'emptyint')
		{
			if (field_value.length>12) ok = false;
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (!ok) msg = lstmsg[5];
		}

		if (field_type == 'float')
		{
			if (field_value.length==0) ok = false;
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)=='.' || field_value.charAt(i)==',') nbpoint++;
				else if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (nbpoint>1) ok = false;

			if (!ok) msg = lstmsg[6];
		}

		if (field_type == 'emptyfloat')
		{
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)=='.' || field_value.charAt(i)==',') nbpoint++;
				else if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (nbpoint>1) ok = false;

			if (!ok) msg = lstmsg[6];
		}

		if (field_type == 'date')
		{
			var thedate = field_value.split("/");
			if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split("-");
			if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split(":");
			if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) ok = false;
			if (ok)
			{
				var datetotest = new Date(eval(thedate[2]),eval(thedate[1])-1,eval(thedate[0]));
				var year = datetotest.getYear()
				if ((Math.abs(year)+"").length < 4) year = year + 1900
				ok = ((datetotest.getDate() == eval(thedate[0])) && (datetotest.getMonth() == eval(thedate[1])-1) && (year == eval(thedate[2])));
			}
			if (!ok) msg = lstmsg[7];
		}

		if (field_type == 'time')
		{
			if (field_value.length!=5) ok = false;
			else
			{
				h=field_value.substring(0,2);
				m=field_value.substring(3,5);
				if (parseInt(h)<0 || parseInt(h)>23) ok = false;
				if (parseInt(m)<0 || parseInt(m)>59) ok = false;
				madate=new Date(01,01,2000,h,m);
				if (madate=="NaN" || field_value.charAt(2)!=':') ok = false;
			}
			if (!ok) msg = lstmsg[8];
		}

		if (field_type=='emptydate')
		{
			if (field_value.length!=0)
			{
				var thedate = field_value.split("/");
				if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split("-");
				if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split(":");
				if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) ok = false;
				if (ok)
				{
					var datetotest = new Date(eval(thedate[2]),eval(thedate[1])-1,eval(thedate[0]));
					var year = datetotest.getYear()
					if ((Math.abs(year)+"").length < 4) year = year + 1900
					ok = ((datetotest.getDate() == eval(thedate[0])) && (datetotest.getMonth() == eval(thedate[1])-1) && (year == eval(thedate[2])));
				}
				if (!ok) msg = lstmsg[7];
			}
		}

		if (field_type=='emptytime')
		{
			if (field_value.length!=0)
			{
				if (field_value.length!=5) ok = false;
				else
				{
					h=field_value.substring(0,2);
					m=field_value.substring(3,5);
					if (parseInt(h)<0 || parseInt(h)>23) ok = false;
					if (parseInt(m)<0 || parseInt(m)>59) ok = false;
					madate=new Date(01,01,2000,h,m);
					if (madate=="NaN" || field_value.charAt(2)!=':') ok = false;
				}
				if (!ok) msg = lstmsg[8];
			}
		}
	}
	else
	{
		ok = false;
	}

	if (!ok) {
                alert(msg.replace(reg,field_label));
		if (field_type != 'checked' && field_type != 'radio') {
			field_object.style.background = error_bgcolor;
			field_object.focus();
		}
	}

	return (ok);
}

function dims_checkall(form, mask, value, byid)
{
	var len = form.elements.length;
	var reg = new RegExp(mask,"g");

	if (isNaN(byid)) byid = false;

	for (var i = 0; i < len; i++)
	{
		var e = form.elements[i];
		if (byid) { if (e.id.match(reg)) e.checked = value; }
		else if (e.name.match(reg)) e.checked = value;
	}
}

var	timer_started = false;
var popup_displayed = false;
var	posx = 0;
var	positiony = 0;
var posy = 0;
var	msg = '';


function dims_showpopup_delayed(w,namepopup,h) {
	if (timer_started) {
		w = parseInt(w);
		if (h==null) h="auto";
		else h=parseInt(h)+'px';

        if (namepopup=="" || namepopup==null) namepopup="dims_popup";
		var dims_popup = dims_getelem(namepopup);

//		with (dims_popup.style)
//		{

			//dims_popup.style.display = 'none';
			//dims_popup.innerHTML = msg+' '+posx+','+posy;
			if (msg!="") dims_popup.innerHTML = msg;

			tmpleft = parseInt(posx) + 20;

			if(navigator.appName == 'Microsoft Internet Explorer')
				tmptop = parseInt(positiony);
			else
				tmptop = parseInt(posy);

			if (w > 0) dims_popup.style.width = w+'px';
			else w = parseInt(dims_popup.offsetWidth);

            if (h != 'auto')
                dims_popup.style.minHeight = h;
			dims_popup.style.height = 'auto';

			if (20 + w + parseInt(tmpleft) > parseInt(document.body.offsetWidth)) {
				tmpleft =parseInt(document.body.offsetWidth) - w - 10;
			}

			//if (tmptop+200>parseInt(document.body.offsetHeight)) // && tmptop > 300)
			//	dims_popup.style.top = (tmptop-200)+'px';
			//else
			dims_popup.style.top = tmptop+'px';

			dims_popup.style.left = tmpleft+'px';
			dims_popup.style.display = 'block';
			dims_popup.style.visibility = 'visible';
			//dims_popup.style.overflow = "auto";
//		}

		popup_displayed = true;
		timer_started=false;
	}
}


function dims_addslashes(str)
{
	str = str.replace(/\\/g,"\\\\");
	str = str.replace(/\'/g,"\\'");
	str = str.replace(/\"/g,"\\\"");
	return(str);
}

function dims_redirect(url){
    parent.location = url;
}

function dims_openOverlayedPopup(w,h, identifier) {
	if (identifier=="" || identifier==null) identifier=dims_getUniqId();

	var popupId = 'p'+identifier;
	var overlayId = 'o'+identifier;

	var popupCode = '<div style="display: none;" class="ui-dialog ui-widget ui-widget-content ui-corner-all dims_popup" id="'+popupId+'"></div>';
	var overlayCode = '<div style="display: none;" class="overlay" id="'+overlayId+'"></div>';

	$('div#popup_container').append(overlayCode,popupCode);

	$('div#'+overlayId).fadeIn().click(function(){
		var popupId = 'p'+identifier;
		var overlayId = 'o'+identifier;

		$('div#'+popupId).fadeOut();
		$('div#'+overlayId).fadeOut(function () {
			$('div#'+popupId).remove();
			$('div#'+overlayId).remove();
		});
	});
	dims_showcenteredpopup('',w,h,popupId);

	return identifier;
}

function dims_closeOverlayedPopup(identifier) {
	var popupId = 'p'+identifier;
	var overlayId = 'o'+identifier;

	$('div#'+popupId).fadeOut();
	$('div#'+overlayId).fadeOut(function () {
		$('div#'+popupId).remove();
		$('div#'+overlayId).remove();
	});
}

function dims_showcenteredpopup(message,w,h,namepopup) {
	blockclosepopup=true;
	if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	if (w=="") w=500;
	if (h=="") h='auto';

	var msg = message;

	if( window.innerWidth) {
		largeur=window.innerWidth;
	}else{
		largeur=document.body.offsetWidth;
	}

	if(window.innerHeight) {

		hauteur=window.innerHeight;
	}
  	else {

		if (w > 500)
			hauteur=document.body.offsetHeight/3;
  		else
			hauteur=document.body.offsetHeight/2;
  	}

	if (h=='auto') {
		hcal=(hauteur-100)/2;
	}
	else {
		hcal=(hauteur-h)/2;
	}

	if (hcal<0) hcal=50;

	posx=(largeur-w)/2;

	if (self.pageYOffset){
		posy = hcal+self.pageYOffset;
	}else if (document.documentElement && document.documentElement.scrollTop){
		posy = hcal+document.documentElement.scrollTop;
	}else if (document.body) {
		posy = hcal+document.body.scrollTop;
	}else{
		posy=hcal+window.scrollY;
	}

        if (posy<50) posy=50;
	positiony = posy ;

	if (!timer_started) {
		timer_started = true;
		setTimeout("dims_showpopup_delayed('"+w+"','"+namepopup+"','"+h+"')", 10);
	}
}

function dims_showpopup(message, w, e, origine,namepopup,decalx,decaly,decalfix_x,decalfix_y) {
	if (namepopup=="" || namepopup==null) namepopup="dims_popup";
    if (decalx=="" || decalx==null) decalx=0;
    else decalx=decalx*1;

    if (decaly=="" || decaly==null) decaly=0;
    else decaly=decaly*1;

    if (decalfix_x=="" || decalfix_x==null) decalfix_x=0;
    if (decalfix_y=="" || decalfix_y==null) decalfix_y=0;

    msg = message;

	if (!origine) var origine = '';

	if( window.innerWidth) {
		largeur=window.innerWidth;
	}
  	else {
  		largeur=document.body.offsetWidth;
  	}
    if( window.innerHeight) {
		hauteur=window.innerHeight;
	}
  	else {
  		hauteur=document.body.offsetHeight;
  	}

	if (!e) var e = window.event;

	if (e.pageX || e.pageY)	{
		posx = e.pageX;
		posy = e.pageY ;
	}
	else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}


    /* calul de la position fixe du block */
    if (decalfix_x!=0) {
        if (decalfix_x<0) {
            // largeur - decalx
            posx=(largeur+decalfix_x)*1;
            posy-=50;
        }
        else {
            posx=decalfix_x;
        }
    }
    else if (decalx!=0) posx=posx+decalx;

   /* calul de la position fixe du block */
    if (decalfix_y!=0) {
        if (decalfix_y<0) {
            // largeur - decalx
            posy=(hauteur+decalfix_y)*1;
        }
        else {
            posy=decalfix_y;
        }
    }
    else if (decaly!=0) posy=posy+decaly;

	positiony = posy ;

	document.getElementById(namepopup).innerHTML="";
    if (origine == 'click') {
		timer_started = true;
		dims_showpopup_delayed(w,namepopup);
	}
	else {
		if (!timer_started) {
			timer_started = true;

			setTimeout("dims_showpopup_delayed('"+w+"','"+namepopup+"')", 10*timerdelay);
		}

		if (!popup_displayed) dims_showpopup_delayed(w,namepopup);
	}
}

function dims_movepopup(e,namepopup) {
    if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	if (!e) var e = window.event;

	if (e.pageX || e.pageY)	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}

	if (popup_displayed) dims_showpopup_delayed(0,namepopup);
}

function dims_hidepopup(namepopup) {
    if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	timer_started = false;
	popup_displayed = false;
        var dims_popup = '';

	if (document.getElementById) {
            dims_popup = document.getElementById(namepopup);
        }
        else {
            dims_popup = eval("document.all["+namepopup+"]");
        }

	with (dims_popup.style) {
		visibility = 'hidden';
		display = 'none';
	}

	cur_idobject=-10;
	cur_idworkspace=-10;
	cur_idmodule = -10;
	cur_idrecord=-10;
}

function dims_sleep(t)
{
	setTimeout("dims_wakeup()", t*timerdelay);
}

function dims_wakeup() {}

function dims_getelem(elem, doc) {
	if (!doc) doc = document;
	return (doc.getElementById) ? doc.getElementById(elem) : eval("document.all['"+dims_addslashes(elem)+"']");
}

function dims_tickets_search_users() {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("dims_tickets_search_users_exec()", 500);
}

function dims_tickets_search_users_exec() {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("searchExecuteTag()", 500);
	filter=dims_getelem('ticket_search').value;
    if(filter != ""){
		dims_xmlhttprequest_todiv('index-light.php','dims_op=tickets_search_users&dims_ticket_userfilter='+filter,'','div_ticket_search_result');
    }else{
        dims_getelem('div_ticket_search_result').innerHTML = "";
    }
}

function dims_tickets_new(event, id_object, id_record, object_label) {
	var data = '';

	if (object_label) data += '&object_label='+object_label;
	if (id_object) data += '&id_object='+id_object;
	if (id_record) data += '&id_record='+id_record;

	dims_showpopup('',500,event,'click');dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_new'+data,'','dims_popup');
}


function dims_gethttpobject(callback) {
	var xmlhttp = false;

	/* on essaie de cr�er l'objet si ce n'est pas d�j� fait */
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		try {
			xmlhttp = new XMLHttpRequest();
		}
		catch (e) {
			xmlhttp = false;
		}
	}

	return xmlhttp;
}

/**
  * Envoie des donn�es � l'aide d'XmlHttpRequest?
  * @param string methode d'envoi ['GET'|'POST']
  * @param string url
  * @param string donn�es � envoyer sous la forme var1=value1&var2=value2...
  */
function dims_sendxmldata(method, url, data, xmlhttp, asynchronous) {
    if (!xmlhttp) {
        return false;
    }

    if(method == "GET") {
		if(data == 'null') {
			xmlhttp.open("GET", url, asynchronous);
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;'); //charset=ISO-8859-15
		}
		else {
			xmlhttp.open("GET", url+"?"+data, asynchronous);
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;');//charset=ISO-8859-15
		}
		xmlhttp.send(null);
	}
	else if(method == "POST") {
		xmlhttp.open("POST", url+"?"+data, asynchronous);

		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;'); //charset=ISO-8859-15
		xmlhttp.send(data);
	}
	return true;
}

function dims_xmlhttprequest(url, data, asynchronous, getxml) {
	if (isNaN(asynchronous)) asynchronous = false;
	if (isNaN(getxml)) getxml = false;

	xmlhttp = dims_gethttpobject();
	dims_sendxmldata('GET', url, data, xmlhttp, asynchronous);

	// if asynchronous = false => return request content
	if (getxml) return(xmlhttp.responseXML);
	else return(xmlhttp.responseText);
}

function dims_xmlhttprequest_post(url, data, asynchronous, getxml) {
	if (isNaN(asynchronous)) asynchronous = false;
	if (isNaN(getxml)) getxml = false;

	xmlhttp = dims_gethttpobject();
	dims_sendxmldata('POST', url, data, xmlhttp, asynchronous);

	// if asynchronous = false => return request content
	if (getxml) return(xmlhttp.responseXML);
	else return(xmlhttp.responseText);
}

function dims_xmlhttprequest_tofunction(url, data, callback, ticket, getxml) {
    var xmlhttp = dims_gethttpobject();

	if (isNaN(getxml)) getxml = false;

	if (xmlhttp) {
		/* on d�finit ce qui doit se passer quand la page r�pondra */
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState == 4) {/* 4 : �tat "complete" */
				if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */
					if (getxml) callback(xmlhttp.responseXML,ticket);
					else {
						callback(xmlhttp.responseText,ticket);
					}
				}
			}
		}
	}
	return !dims_sendxmldata('GET', url, data, xmlhttp, true);
}

function dims_xmlhttprequest_todiv(url, data, sep, async) {
    var xmlhttp = dims_gethttpobject();
	var args;

	if (xmlhttp) {
		if (dims_xmlhttprequest_todiv.arguments!=null) {
			args = dims_xmlhttprequest_todiv.arguments;

			/* on d�finit ce qui doit se passer quand la page r�pondra */
			xmlhttp.onreadystatechange=function() {
				if (!isNaN(xmlhttp.readyState) && xmlhttp.readyState == 4) {/* 4 : �tat "complete" */
					if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */
						var tabxmlvalue = new Array();
						var result= xmlhttp.responseText;

						if (sep == '') tabxmlvalue[0] = result;
						else tabxmlvalue=result.split(sep);
						if (args.length!=null) {
							for(i=0;i<args.length-3;i++) {
								if (tabxmlvalue[i]) {
									dims_getelem(args[i+3]).innerHTML = tabxmlvalue[i];
									/* modify by pat to evaluate javascript */
									var x = dims_getelem(args[i+3]).getElementsByTagName("script");
									for (var j = 0; j < x.length; j++) {
										if (x[j].text!="") {
											eval(x[j].text);
										}
									}
								}
								else dims_getelem(args[i+3]).innerHTML = '';
							}
						}
					}
				}
			}
		}
	}
    if (async == null) async = true;
	return !dims_sendxmldata('GET', url, data, xmlhttp, async);
}

function dims_xmlhttprequest_todivpost(url, data, sep,arg) {
    var xmlhttp = dims_gethttpobject();
	var args= new Array();

	args[0]=arg;

	if (xmlhttp) {
		//if (dims_xmlhttprequest_todiv.arguments!=null) {
			//args = dims_xmlhttprequest_todiv.arguments;
			//alert('la');
			/* on d�finit ce qui doit se passer quand la page r�pondra */
			xmlhttp.onreadystatechange=function() {
				if (!isNaN(xmlhttp.readyState) && xmlhttp.readyState == 4) {/* 4 : �tat "complete" */
					if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */

						var tabxmlvalue = new Array();
						var result= xmlhttp.responseText;

						if (sep == '') tabxmlvalue[0] = result;
						else tabxmlvalue=result.split(sep);
						if (args.length!=null) {
							for(i=0;i<args.length;i++) {
								if (tabxmlvalue[i]) {
									dims_getelem(args[i]).innerHTML = tabxmlvalue[i];
									/* modify by pat to evaluate javascript */
									var x = dims_getelem(args[i]).getElementsByTagName("script");
									for(var j=0;j<x.length;j++) {
										if (x[j].text!="") {
											eval(x[j].text);
										}
									}
								}
								else dims_getelem(args[i]).innerHTML = '';
							}
						}
					}
				}
			}
		//}
	}
	return !dims_sendxmldata('POST', url, data, xmlhttp, true);
}

//fonction dupliquée pour ne pas faire de bétise par rapport à la précédente, qui tient compte des arguments passés en param
function dims_xmlhttprequest_todivpostargs(url, data, sep) {
    var xmlhttp = dims_gethttpobject();
	var args;


	if (xmlhttp) {
		if (dims_xmlhttprequest_todivpostargs.arguments!=null) {
			args = dims_xmlhttprequest_todivpostargs.arguments;

			/* on définit ce qui doit se passer quand la page répondra */
			xmlhttp.onreadystatechange=function() {
				if (!isNaN(xmlhttp.readyState) && xmlhttp.readyState == 4) {/* 4 : état "complete" */
					if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */

						var tabxmlvalue = new Array();
						var result= xmlhttp.responseText;

						if (sep == '') tabxmlvalue[0] = result;
						else tabxmlvalue=result.split(sep);
						if (args.length!=null) {
							for(i=0;i<args.length -3;i++) {
								if (tabxmlvalue[i]) {
									dims_getelem(args[i+3]).innerHTML = tabxmlvalue[i];
									/* modify by pat to evaluate javascript */
									var x = dims_getelem(args[i+3]).getElementsByTagName("script");
									for(var j=0;j<x.length;j++) {
										if (x[j].text!="") {
											eval(x[j].text);
										}
									}
								}
								else dims_getelem(args[i+3]).innerHTML = '';
							}
						}
					}
				}
			}
		}
	}
	return !dims_sendxmldata('POST', url, data, xmlhttp, true);
}

function dims_ajaxloader(div) {
	var ajaxloader = '<div style="text-align:center;padding:40px 10px;"><img src="./common/img/ajax-loader.gif"></div>';
	if (div && $(div)) $(div).innerHTML = ajaxloader;
	else return ajaxloader;
}

function dims_innerHTML(div, html)
{
	if ($(div)) {
		$(div).innerHTML = html;
		$(div).innerHTML.evalScripts();
	}
}

// SHARE MANAGEMENT

function dims_share_searchdata()
{
	dims_xmlhttprequest_todiv('index-light.php','dims_op=search_list&text=','','contentsearch');
}

function dims_share_add_elem(typeshare,idshare)
{
	dims_xmlhttprequest_todiv('index-light.php','dims_op=add_elem&type_share='+typeshare+'&id_share='+idshare,'','contentattach');
}

function dims_share_del_elem(typeshare,idshare)
{
	dims_xmlhttprequest_todiv('index-light.php','dims_op=del_elem&type_share='+typeshare+'&id_share='+idshare,'','contentattach');
}

function dims_share_contentattach()
{
	var ts=document.getElementById('contentattach');
	dims_xmlhttprequest_todiv('index-light.php','dims_op=contentattach','','contentattach');
}



var tag_timer;
var tag_search;
var tag_results = new Array();

var tag_last_array = new Array();
var tag_new_array = new Array();

var tag_lastedit = '';
var tag_modified = -1

function dims_tag_init(idrecord)
{
	dims_getelem('dims_annotationtags_'+idrecord).onkeyup = dims_tag_keyup;
	dims_getelem('dims_annotationtags_'+idrecord).onkeypress = dims_tag_keypress;
}

function dims_tag_search(idrecord, search)
{
	clearTimeout(tag_timer);
	tag_search = search;
	tag_timer = setTimeout("dims_tag_searchtimeout("+idrecord+")", 100);
}

function dims_tag_searchtimeout(idrecord)
{
	// replace(/(^\s*)|(\s*$)/g,'') = TRIM
	list_tags = tag_search.split(' ');

	if (list_tags.length>0) dims_xmlhttprequest_tofunction('index-quick.php','dims_op=tags_search&tag='+list_tags[list_tags.length-1],dims_tag_display,idrecord);
}

function dims_tag_display(result,ticket)
{
	if (result != '')
	{
		tag_results = new Array();

		splited_result = result.split('|');
		tagstoprint = '';

		for (i=0;i<splited_result.length;i++)
		{
			detail = splited_result[i].split(';');
			if (tagstoprint != '') tagstoprint += ' ';
			if (i==0) tagstoprint += '<b>';
			tagstoprint += '<a href="javascript:dims_tag_complete('+ticket+','+i+')">'+detail[0]+'</a> ('+detail[1]+')';
			if (i==0) tagstoprint += '</b>';
			tag_results[i] = detail[0];
		}

		dims_getelem('tagsfound_'+ticket).innerHTML = tagstoprint;
	}
	else
	{
		dims_getelem('tagsfound_'+ticket).innerHTML = '';
		tag_results = new Array();
	}
}

function dims_tag_prevent(e)
{
	if (window.event) window.event.returnValue = false
	else e.preventDefault()
}



function dims_tag_keypress(e)
{
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target;

	switch(e.keyCode)
	{
		case 38: case 40:
			prevent(e)
		break
		case 9:
			dims_tag_prevent(e)
		break
		case 13:
			dims_tag_prevent(e)
		break
		default:
			tag_lastedit = dims_getelem(src.id).value;
		break;
	}
}

function dims_tag_keyup(e)
{
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field
	idrecord = src.id.split('_')[2]; // get id record from source field id

	switch(e.keyCode)
	{
		case 38: case 40:
			prevent(e);
		break
		case 9:
			dims_tag_complete(idrecord);
			dims_tag_prevent(e);
		break
		case 13:
			dims_tag_complete(idrecord);
			dims_tag_prevent(e);
		break
		case 35: //end
		case 36: //home
		case 39: //right
		case 37: //left
		//case 32: //space
		break
		default:
			tag_last_array = new Array();
			tag_new_array = new Array();

			tag_last_array = tag_lastedit.split(' ');
			tag_new_array = dims_getelem(src.id).value.split(' ');

			tag_modified = -1;
			for (i=0;i<tag_new_array.length;i++)
			{
				if (tag_new_array[i] != tag_last_array[i])
				{
					if (tag_modified == -1) tag_modified = i;
					else tag_modified = -2
				}
			}
			if (tag_modified>=0) dims_tag_search(idrecord, tag_new_array[tag_modified]);
		break;
	}
}

function dims_tag_complete(idrecord, idtag)
{
	if (!(idtag>=0)) idtag = 0;

	if (tag_results[idtag])
	{
		tag_new_array[tag_modified] = tag_results[idtag];

		taglist = '';
		for (i=0;i<tag_new_array.length;i++)
		{
			if (taglist != '') taglist += ' ';
			taglist += tag_new_array[i]
		}

		dims_getelem('dims_annotationtags_'+idrecord).value = taglist.replace(/(^\s*)|(\s*$)/g,'')+' ';
		dims_getelem('tagsfound_'+idrecord).innerHTML = '';
	}

	tag_results = new Array();
}

function dims_calendar_open(inputfield_id, event,funct) {
	dims_showpopup('',188,event,'click');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=calendar_open&selected_date='+dims_getelem(inputfield_id).value+'&inputfield_id='+inputfield_id+'&funct='+funct,'','dims_popup');
}

function dims_calendar_open_3(inputfield1_id, inputfield2_id, inputfield3_id, event, funct) {
	dims_showpopup('', 180, event, 'click');
	dims_xmlhttprequest_todiv(
		'admin-light.php',
		'dims_op=calendar_open&selected_date='+dims_getelem(inputfield1_id).value+'/'+dims_getelem(inputfield2_id).value+'/'+dims_getelem(inputfield3_id).value+'&inputfield1_id='+inputfield1_id+'&inputfield2_id='+inputfield2_id+'&inputfield3_id='+inputfield3_id+'&funct='+funct,
		'',
		'dims_popup');
}

function dims_submitmac()
{
	document.formlogin.dims_usermac.value = document.mac.getMacaddress();
}

/* COLORPICKER FUNCTIONS */

var rgb, hsv;

function colorpicker_hex2rgb(hex_string, default_)
{
	if (default_ == undefined)
	{
		default_ = null;
	}

	if (hex_string.substr(0, 1) == '#')
	{
		hex_string = hex_string.substr(1);
	}

	var r;
	var g;
	var b;
	if (hex_string.length == 3)
	{
		r = hex_string.substr(0, 1);
		r += r;
		g = hex_string.substr(1, 1);
		g += g;
		b = hex_string.substr(2, 1);
		b += b;
	}
	else if (hex_string.length == 6)
	{
		r = hex_string.substr(0, 2);
		g = hex_string.substr(2, 2);
		b = hex_string.substr(4, 2);
	}
	else
	{
		return default_;
	}

	r = parseInt(r, 16);
	g = parseInt(g, 16);
	b = parseInt(b, 16);
	if (isNaN(r) || isNaN(g) || isNaN(b))
	{
		return default_;
	}
	else
	{
		return {r: r / 255, g: g / 255, b: b / 255};
	}
}

function colorpicker_rgb2hex(r, g, b, includeHash)
{
	r = Math.round(r * 255);
	g = Math.round(g * 255);
	b = Math.round(b * 255);
	if (includeHash == undefined)
	{
		includeHash = true;
	}

	r = r.toString(16);
	if (r.length == 1)
	{
		r = '0' + r;
	}
	g = g.toString(16);
	if (g.length == 1)
	{
		g = '0' + g;
	}
	b = b.toString(16);
	if (b.length == 1)
	{
		b = '0' + b;
	}
	return ((includeHash ? '#' : '') + r + g + b).toUpperCase();
}

function colorpicker_hsv2rgb(hue, saturation, value)
{
	var red;
	var green;
	var blue;
	if (value == 0.0)
	{
		red = 0;
		green = 0;
		blue = 0;
	}
	else
	{
		var i = Math.floor(hue * 6);
		var f = (hue * 6) - i;
		var p = value * (1 - saturation);
		var q = value * (1 - (saturation * f));
		var t = value * (1 - (saturation * (1 - f)));
		switch (i)
		{
			case 1: red = q; green = value; blue = p; break;
			case 2: red = p; green = value; blue = t; break;
			case 3: red = p; green = q; blue = value; break;
			case 4: red = t; green = p; blue = value; break;
			case 5: red = value; green = p; blue = q; break;
			case 6: // fall through
			case 0: red = value; green = t; blue = p; break;
		}
	}
	return {r: red, g: green, b: blue};
}

function colorpicker_rgb2hsv(red, green, blue)
{
	var max = Math.max(Math.max(red, green), blue);
	var min = Math.min(Math.min(red, green), blue);
	var hue;
	var saturation;
	var value = max;
	if (min == max)
	{
		hue = 0;
		saturation = 0;
	}
	else
	{
		var delta = (max - min);
		saturation = delta / max;
		if (red == max)
		{
			hue = (green - blue) / delta;
		}
		else if (green == max)
		{
			hue = 2 + ((blue - red) / delta);
		}
		else
		{
			hue = 4 + ((red - green) / delta);
		}
		hue /= 6;
		if (hue < 0)
		{
			hue += 1;
		}
		if (hue > 1)
		{
			hue -= 1;
		}
	}
	return {
		h: hue,
		s: saturation,
		v: value
	};
}

function colorpicker_initelements()
{
	x = (hsv.v*199)-5;
	if (x<-5) x=-5;
	if (x>194) x=194;
	dims_getelem('colorpicker_crosshairs').style.left = x.toString() + 'px';
	y = ((1-hsv.s)*199)-5;
	if (y<-5) y=-5;
	if (y>194) y=194;
	dims_getelem('colorpicker_crosshairs').style.top = y.toString() + 'px';
	x = (hsv.h*199)-5;
	if (x<-5) x=-5;
	if (x>194) x=194;
	dims_getelem('colorpicker_position').style.top = x.toString() + 'px';
}

function colorpicker_colorchanged()
{
	var hex = colorpicker_rgb2hex(rgb.r, rgb.g, rgb.b);
	var hueRgb = colorpicker_hsv2rgb(hsv.h, 1, 1);
	var hueHex = colorpicker_rgb2hex(hueRgb.r, hueRgb.g, hueRgb.b);
	dims_getelem('colorpicker_selectedcolor').style.background = hex;
	dims_getelem('colorpicker_inputcolor').value = hex;
	dims_getelem('colorpicker_sv').style.background = hueHex;
}

function colorpicker_rgbchanged()
{
	hsv = colorpicker_rgb2hsv(rgb.r, rgb.g, rgb.b);
	colorpicker_colorchanged();
}
function colorpicker_hsvchanged()
{
	rgb = colorpicker_hsv2rgb(hsv.h, hsv.s, hsv.v);
	colorpicker_colorchanged();
}

function colorpicker_pagecoords(node)
{
	var x = node.offsetLeft;
	var y = node.offsetTop;
	var parent = node.offsetParent;
	while (parent != null)
	{
		x += parent.offsetLeft;
		y += parent.offsetTop;
		parent = parent.offsetParent;
	}
	return {x: x, y: y};
}


function colorpicker_fixcoords(node, x, y)
{
	var nodePageCoords = colorpicker_pagecoords(node);
	x = (x - nodePageCoords.x) + document.documentElement.scrollLeft;
	y = (y - nodePageCoords.y) + document.documentElement.scrollTop;
	if (x < 0) x = 0;
	if (y < 0) y = 0;
	if (x > node.offsetWidth - 1) x = node.offsetWidth - 1;
	if (y > node.offsetHeight - 1) y = node.offsetHeight - 1;
	return {x: x, y: y};
}

function colorpicker_onmousedown(e)
{
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field

	coords = colorpicker_fixcoords(src, e.clientX, e.clientY);

	if (src.id == 'colorpicker_sv')
	{
		colorpicker_placeelement('colorpicker_crosshairs',coords.x,coords.y);
	}
	else if (src.id == 'colorpicker_crosshairs')
	{
		x = parseInt(dims_getelem('colorpicker_crosshairs').style.left) + coords.x;
		y = parseInt(dims_getelem('colorpicker_crosshairs').style.top) + coords.y;
		colorpicker_placeelement('colorpicker_crosshairs',x,y);
	}
	else if (src.id == 'colorpicker_h')
	{
		colorpicker_placeelement('colorpicker_position',0,coords.y);
	}
	else if (src.id == 'colorpicker_position')
	{
		y = parseInt(dims_getelem('colorpicker_position').style.top) + coords.y;
		colorpicker_placeelement('colorpicker_position',0,y);
	}
}

function colorpicker_placeelement(element,x,y)
{
	if (x<0) x=0;
	if (x>199) x=199;

	if (y<0) y=0;
	if (y>199) y=199;

	if (element == 'colorpicker_position')
	{
		dims_getelem('colorpicker_position').style.top = (y-5) + 'px';
		hsv.h = y/199;
	}
	else if (element == 'colorpicker_crosshairs')
	{
		dims_getelem('colorpicker_crosshairs').style.left = (x-5) + 'px';
		dims_getelem('colorpicker_crosshairs').style.top = (y-5) + 'px';
		hsv.s = 1-(y/199);
		hsv.v = (x/199);
	}
	colorpicker_hsvchanged();
}


function colorpicker_input_onchange()
{
	rgb = colorpicker_hex2rgb(dims_getelem('colorpicker_inputcolor').value, {r: 0, g: 0, b: 0});
	colorpicker_rgbchanged();
	colorpicker_initelements();
}

function colorpicker_start()
{
	dims_getelem('colorpicker_sv').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_h').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_position').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_crosshairs').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_inputcolor').onchange = colorpicker_input_onchange;

	colorpicker_input_onchange();
}

function dims_colorpicker_open(inputfield_id, event){
	dims_showpopup('','241',event,'click');
	data = 'dims_op=colorpicker_open&inputfield_id='+inputfield_id+'&colorpicker_value='+escape(dims_getelem(inputfield_id).value);
	colorpickerhtml = dims_xmlhttprequest('admin-light.php',data);
	dims_getelem('dims_popup').innerHTML = colorpickerhtml;
	colorpicker_start();
}

/* DOCUMENTS FUNCTIONS */

function dims_documents_openfolder(currentfolder, documentsfolder_id, event)
{
	dims_showpopup('',300,event,'click');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_openfolder&currentfolder='+currentfolder+'&documentsfolder_id='+documentsfolder_id,'','dims_popup');
}

function dims_documents_openfile(currentfolder, documentsfile_id, event)
{
	dims_showpopup('',380,event,'click');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_openfile&currentfolder='+currentfolder+'&documentsfile_id='+documentsfile_id,'','dims_popup');
}

function dims_documents_deletefile(currentfolder, documents_id, documentsfile_id)
{
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_deletefile&currentfolder='+currentfolder+'&documentsfile_id='+documentsfile_id,'','dimsdocuments_'+documents_id);
}

function dims_documents_deletefolder(currentfolder, documents_id, documentsfolder_id)
{
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_deletefolder&currentfolder='+currentfolder+'&documentsfolder_id='+documentsfolder_id,'','dimsdocuments_'+documents_id);
}

function dims_documents_browser(currentfolder, documents_id)
{
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_browser&currentfolder='+currentfolder,'','dimsdocuments_'+documents_id);
}

function dims_documents_validate(form)
{
	if (dims_validatefield('Fichier',form.documentsfile_file,"string"))
	if (dims_validatefield('Libell�',form.documentsfile_label,"string"))
		return true;

	return false;
}

function autofitIframe(){
    try {
		if (document.getElementById || !window.opera && !document.mimeType && document.all && document.getElementById) {
			hauteur = this.document.body.scrollHeight + 40;

			if (hauteur<650) hauteur=650;
			parent.document.getElementById('wce_frame_editor').style.height=hauteur+"px";
		}
	}
	catch (e) {
		hauteur =this.document.body.offsetHeight + 40;
		if (hauteur<650) hauteur=650;
		parent.document.getElementById('wce_frame_editor').style.height=hauteur+"px";
	}
}

function addElement(parentId, elementTag, elementId, html,xdeb,ydeb,xfin,yfin) {
    // Adds an element to the document
    var p = document.getElementById(parentId);

    var newElement = document.createElement(elementTag);
    newElement.setAttribute('id', elementId);

    var width=Math.abs(xfin-xdeb);
    var height=Math.abs(yfin-ydeb);

    if (width==0) width=1;
    if ( height==0) height=1;

    // definition de la taille du canvas
    if (width!=null) newElement.setAttribute('width', width);
    if (height!=null) newElement.setAttribute('height',height);

    //Calcul du plus petit top et left, pour largeur et hauteur : valeur absolue
    if (xfin<xdeb) {
    	minx=xfin;
    	xfin=0;
    	xdeb=width;
    }
    else {
    	minx=xdeb;
    	xdeb=0;
    	xfin=width;
    }

    if (yfin<ydeb) {
    	miny=yfin;
    	yfin=0;
    	ydeb=height;
    }
    else {
    	miny=ydeb;
    	ydeb=0;
    	yfin=height;
    }

	newElement.style.left=minx+'px';
	newElement.style.top=miny+'px';
	newElement.style.position='absolute';
	p.appendChild(newElement);

	if(!window.innerWidth) {
		newElement=G_vmlCanvasManager.initElement(newElement);
	}

	p.appendChild(newElement);
	newElement.innerHTML = html;

	if (elementTag=="canvas") draw(elementId,xdeb,ydeb,xfin,yfin,1);
}

function removeElement(elementId) {
// Removes an element from the document
var element = document.getElementById(elementId);
element.parentNode.removeChild(element);
}

function draw(elem,xdep,ydep,xfin,yfin,sens){
	if (sens==null) sens=0;

	var canvas = document.getElementById(elem);
	if (canvas.getContext){

	  	var ctx = canvas.getContext('2d');
	  	ctx.save();
	  	ctx.lineWidth = 1;
		ctx.strokeStyle = '#BCBCBC';
	  	ctx.beginPath();
	  	ctx.moveTo(xdep,ydep);
	  	// si sens = 0 => horizontal sinon vertical
	  	if (sens==0) {
	  		dist=(xfin-xdep)*1/3;
			ctx.bezierCurveTo(dist,ydep,xfin-(dist/2),yfin,xfin,yfin);
		}
		else {
			dist=(yfin-ydep)*1;
			ctx.bezierCurveTo(xdep,dist,xfin,yfin-(dist/2),xfin,yfin);
		}
		ctx.stroke();
		ctx.restore();
	}
}

function dims_print_r(theObj){
	if(theObj.constructor == Array ||
	   theObj.constructor == Object){
		document.write("<ul>")
		for(var p in theObj){
			if(theObj[p].constructor == Array||
			   theObj[p].constructor == Object){
				document.write("<li>["+p+"] => "+typeof(theObj)+"</li>");
				document.write("<ul>")
				print_r(theObj[p]);
				document.write("</ul>")
			}
			else {
				document.write("<li>["+p+"] => "+theObj[p]+"</li>");
			}
		}
		document.write("</ul>")
	}
}

/* GESTION DES FORMULAIRES (CYRIL 25/05/2011) -------------------------------------------------------------------- */
//var full_error;
function valideField()
{
	var email =    /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;//adresse email
	var number = /^[-]?\d*\.?\d*$/; // Nombre
	var color = /^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/; //couleur
	var date_frslashes = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/; //date française au format jj/mm/aaaa
	var heure_doublepoint = /^[0-9]{2}:[0-9]{2}$/; //heure au format hh:mm
	var heure_part = /^[0-9]{2}$/; //soit hh soit mm

	error =false;
	target= null;
	message = null;

	if(dims_form_submitted){
		if($(this).tagName()!='select'){
			if( $(this).val() == '' && $(this).attr('rel')=='requis'){
				error = true;
				message = "Ce champ est obligatoire";
			}
		}
		else{
			error =true;
			$(this).each(function () {
				if($(this).attr('value')!='dims_nan' && $(this).attr('value')!=''){
					error = false;
					//return false; //équivalent du break dans le each
				}
             });
			 if(error)message = "Ce champ est obligatoire";
		}
	}


	if(!error)
	{
		if($(this).attr("rev") == 'email' )
		{
			if($(this).val() != '' && !$(this).val().match(email)){
				 error = true;
				 message = 'Le format de cette adresse email est incorrect';

			}
		}

		else if($(this).attr("rev") == 'number')
		{
			if( $(this).val() != '' && !$(this).val().match(number)){
				 error = true;
				 message = 'Le format de ce nombre est incorrect';
			}
		}

		else if($(this).attr("rev") == 'color')
		{
			if( $(this).val() != '' && !$(this).val().match(color)){
				 error = true;
				 message = 'La couleur passée doit être au format HTML. Ex : #34F3DF';
			}
		}

		else if($(this).attr("rev") == 'dims_login')
		{
			if( $(this).val() != '' && $(this).val() != $('form.dims_form_controlled #dims_login_reference').val() && !isUniqueLogin($(this).val())){
				error = true;
				message = 'Ce login est déjà existant, vous ne pouvez pas l\'utiliser';
			}
		}

		else if($(this).attr("rev") == 'dims_pwd'){
			target = "#def_"+$('form.dims_form_controlled #userx_passwordconfirm').attr('id');
			if($(this).val() !='' && $('form.dims_form_controlled #userx_passwordconfirm').val() !='' && $(this).val() != $('form.dims_form_controlled #userx_passwordconfirm').val()){
				error = true;
				message = 'Les deux mots de passe ne correspondent pas';
			}
		}
		else if($(this).attr("rev") == 'dims_pwd_confirm'){
			if($(this).val() !='' && $('form.dims_form_controlled #userx_password').val() !='' && $(this).val() != $('form.dims_form_controlled #userx_password').val()){
				error = true;
				message = 'Les deux mots de passe ne correspondent pas';
			}
		}

		else if($(this).attr("rev") == 'date_jj/mm/yyyy'){
			if( $(this).val() != '' && !$(this).val().match(date_frslashes)){
				 error = true;
				 message = 'Le format de la date est incorrect';
			}
		}

		else if($(this).attr("rev") == 'heure_hh:mm'){
			if( $(this).val() != '' && !$(this).val().match(heure_doublepoint)){
				error = true;
				message = 'Le format de l\'heure est incorrect (hh:mm)';
			}
			else if($(this).val() != '' && $(this).val().match(heure_doublepoint))
			{
				var tabH = $(this).val().split(':');
				var h = parseInt(tabH[0]);
				var m = parseInt(tabH[1]);
				if(!(h >= 0 && h<24 && m >= 0 && h<60) )
				{
					error = true;
					message = 'Le format de l\'heure est incorrect (hh:mm)';
				}
			}
		}

		else if($(this).attr("rev") == 'heure_hh'){
			if( $(this).val() != '' && !$(this).val().match(heure_part)){
				error = true;
				message = 'Le format de l\'heure est incorrect (hh)';
			}
			else if($(this).val() != '' && $(this).val().match(heure_part))
			{
				var h = $(this).val();
				if(!(h >= 0 && h<24) )
				{
					error = true;
					message = 'Le format de l\'heure est incorrect (hh)';
				}
			}
		}

		else if($(this).attr("rev") == 'heure_mm'){
			if( $(this).val() != '' && !$(this).val().match(heure_part)){
				error = true;
				message = 'Le format des minutes est incorrect (mm)';
			}
			else if($(this).val() != '' && $(this).val().match(heure_part))
			{
				var m = $(this).val();
				if(!(m >= 0 && m<60) )
				{
					error = true;
					message = 'Le format des minutes est incorrect (mm)';
				}
			}
		}
		else if($(this).attr("rev") !=null && $(this).attr("rev").substring(0,8) == 'compare:'){//mode comparaison pour deux inputs
			if($(this).tagName()=='input'){
				//alert("'"+$(this).attr("rev").substring(8,$(this).attr("rev").length)+"'");
				var cmpTo = $(this).attr("rev").substring(8,$(this).attr("rev").length);
				var cmpToValue = $('#'+cmpTo).val();
				if($(this).val() != '' && cmpToValue != '' && $(this).val() != cmpToValue){
					error = true;
					message = 'Les deux adresses email ne correspondent pas';
				}
			}
		}

		else if($(this).attr("rev") !=null && $(this).attr("rev").substring(0,4) == 'ext:'){//mode comparaison pour input type file ou autre sur l'extension de la valeur
			if($(this).tagName()=='input'){
				var extension = $(this).attr("rev").substring(4,$(this).attr("rev").length).toLowerCase();
				var file_type = $(this).val().substring($(this).val().length - extension.length,$(this).val().length).toLowerCase();
				if(file_type != '' && extension != '' && file_type != extension){
					error = true;
					message = 'L\'extension du fichier est incorrecte. Extension attendue : '+extension;
				}
			}
		}

		else if($(this).attr("rev") !=null && $(this).attr("rev").substring(0,8) == 'gr_check'){//mode comparaison pour savoir si au moins une checkbox du groupe est cochée
			if($(this).tagName()=='input' && $(this).attr('type')=='checkbox'){
				//alert("ici");
				var gr_name = $(this).attr('name').substring(0, $(this).attr('name').length - 2);//assume que le groupe est nommé avec [] à la fin
				//recherche du nom dans la liste
				var idx = -1;
				for(var i=0; i<chgroup_names.length;i++){
					if(chgroup_names[i] == gr_name){
						idx = i;
					}
				}

				if(idx == -1){//si le groupe n'est pas encore stocké
					chgroup_names[chgroup_names.length] = gr_name;
					idx = chgroup_names.length -1;
					gr_checkbox_error[idx] = false;
				}

				gr_checkbox_error[idx] = gr_checkbox_error[idx] || $(this).attr('checked')=='checked';
			}
		}
	}
	if(error)
	{
	   if(target==null){
		   $("#def_"+$(this).attr("id")).show();
		   $("#def_"+$(this).attr("id")).text(message);
	   }
	   else{
		   $(target).show();
		   $(target).text(message);
	   }
	}
	else{
		if(target==null){
			$("#def_"+$(this).attr("id")).hide();
		}
		else{
			$(target).hide();
		}
	}
	full_error = full_error || error;
}

//fonction de contrôle d'un champs d'un formulaire dims après perte du focus -----
if (document.getElementById('form.dims_form_controlled')!=null)
$('form.dims_form_controlled').ready(function(){
		dims_form_submitted = false;
		full_error = false;
    $('form.dims_form_controlled input').blur(valideField);
});

//fonction de contrôle des valeurs d'un formulaire au moment du submit
function dims_controlform(form_name, div_globalerror, message)
{
	dims_form_submitted = true;
	full_error = false;
	gr_checkbox_error = new Array();
	chgroup_names = new Array();

	$("form#"+form_name+" input[rel='requis']").each(valideField);
	$("form#"+form_name+" select[rel='requis']").each(valideField);
	$("form#"+form_name+" textarea[rel='requis']").each(valideField);
	dims_form_submitted = false;

	//gestion à postériori des groupes de checkbox
	for( var i=0;i< gr_checkbox_error.length ; i++){

		if(!gr_checkbox_error[i])//dans ce cas aucune n'est cochée, erreur
		{
			full_error = true;
			$('#def_'+chgroup_names[i]).show();
			$('#def_'+chgroup_names[i]).text('Au moins un élément doit être sélectionné');
		}
		else//il faut masquer l'erreur si elle a été affichée quelque part
		{
			$('#def_'+chgroup_names[i]).hide();
		}
	}

	if(full_error){
		$('#'+div_globalerror).text(message);
		$('#'+div_globalerror).show();
	}
	else $('#'+div_globalerror).hide();

	return !full_error;
}

/*fonction permettant de déterminer si le login passé en paramètre est unique ou non*/
function isUniqueLogin(login){
	 var unicite =  dims_xmlhttprequest('admin.php', 'dims_op=login_unique&login='+login);
	 return unicite==1;
}

//déclaration d'une fonction pour les élément jquery peremettant de fournir le nom de la balise courante
if (document.getElementById('fn.tagName')==null)
$.fn.tagName = function() {
   return this.get(0).tagName.toLowerCase();
}

/* FIN GESTION DES FORMULAIRES ******************************************************************* */

function dims_getUniqId() {
	var uniqueID = new Date();
	return uniqueID.getTime();
}

// fonction pour l'ajout de sous catégorie
function addCateg(id){
    dims_showcenteredpopup('',200,500,'dims_popup');
    dims_xmlhttprequest_todiv('admin.php','dims_op=addSubCateg&id='+id,'','dims_popup');
}

/* -------------- DIMS ACTION LOG MANAGEMENT INITIALIZATION --------------------------- */

//initialisation des balises DOM à contrôler
function initALM(){
	$(document).ready(function(){
		$(document).find('a').each(function(){
			fixRelatedAction($(this));
		});

		$(document).find('input').each(function(){
			fixRelatedAction($(this));
		});

		$(document).find('form').each(function(){
			fixRelatedAction($(this));
		});
	});
}

//méthode permettant de fixer l'évenement qui déclenchera le log au moment du clic sur l'élément IHM
function fixRelatedAction(object)
{
	var rel = object.attr('rel');
	if( rel != undefined && rel.substring(0,5) == '#ALM_' ){
		var ids_string = rel.substring(5, rel.length);
		var ids = ids_string.split(':');
		//alert(ids[0] +' --> '+ids[1]);
		//logAction(ids[0], ids[1]);
		var ok = false;
		var attribut = '';
		switch(object.tagName()){
			case 'a':
			case 'input':
				if(object.tagName() == 'a' || (object.tagName() == 'input' && (object.attr('type') == 'button' || object.attr('type') == 'submit'))){
					attribut = 'onclick';
					ok = true;
				}
			break;

			case 'form':
				attribut = 'onsubmit';
				ok = true;
			break;
		}
		var currentVAL = object.attr(attribut);

		if (currentVAL == undefined) currentVAL ="javascript:";
		else if(currentVAL != undefined && currentVAL.indexOf("logAction("+ids[0]+",") > -1){
			return;//inutile de continuer on est déjà passé sur cet élément pour cette action_definition
		}

		if(currentVAL != undefined && currentVAL != "javascript:" && currentVAL.length > 0 && currentVAL.substring(currentVAL.length - 1, currentVAL.length) != ';')currentVAL += ';';
		object.attr(attribut,  currentVAL + 'logAction('+ids[0]+','+ids[1]+');');
	}
}

//function qui log l'action
function logAction(action, id_object){
	var target = document.URL;
	var posInterro = target.indexOf('?');
	if(posInterro != -1) target = target.substring(0, posInterro);
	$.ajax({
		type: "POST",
		url: target,
		async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
		data: {
			'dims_op' : 'alm_log',
			'action': action,
			'object': id_object
		},

		dataType: "text",
		success: function(data){
			//alert(data);
		},

		error: function(data){
			//rien à faire
		}
	});
}
//initALM();

/*
 * fonction permettant d'adapter une ou plusieurs image à une taille carrée fournie par max_size
 * @param : jq_selector : le selecteur jquery_en texte (ex : "img.avatar")
 * @param : each_elem : indique si c'est pour chaque élément de la page qui matche avec le selecteur ou pas
 * @param : max_size : la taille du carré dans lequel doit rentrer la photo
 */

function adapteImage(jq_selector, each_elem, max_size){
	if(each_elem){
		$(jq_selector).each(function(i) {
			adapteLight($(this), max_size);
		});
	}
	else adapteLight($(this), max_size);
}

/*
 * fonction permettant d'adapter une image à un cadre carré et centre l'image à l'intérieur
 * @param : jelem : un élément déjà jquery-isé
 * @param : each_elem : indique si c'est pour chaque élément de la page qui matche avec le selecteur ou pas
 * @param : max_size : la taille du carré dans lequel doit rentrer la photo
 */
function adapteLight(elem, max_size){
	if (elem.height() > elem.width()) {
		var h = max_size;
		var w = Math.ceil(elem.width() / elem.height() * max_size);
	  } else {
		var w = max_size;
		var h = Math.ceil(elem.height() / elem.width() * max_size);
	  }

	  //repositionnement
	  var margin_left = Math.ceil((max_size - w) / 2);
	  var margin_top = Math.ceil((max_size - h) / 2);

	  elem.css({ 'height': h, 'width': w, 'margin-left': margin_left, 'margin-top': margin_top });
}

//desktop Search

var issearching=false;
var etype;
var keytype;
var word_timer;
var resultsearch="";
var sizeresult=65;
var current_elem_id=0;

function updateTypeWordSearch(k,type) {
	dims_xmlhttprequest_tofunction('admin.php','dims_op=updateTypeWordSearch&k='+k+"&type="+type,actualizeSearchExec);
}

function updateOperatorWordSearch(k,operator) {
	dims_xmlhttprequest_tofunction('admin.php','dims_op=updateOperatorWordSearch&k='+k+"&operator="+operator,actualizeSearchExec);
}

function updateSearchTag(idtag) {
	dims_xmlhttprequest_tofunction('admin.php','dims_op=addTagSearch&id_tag='+idtag,actualizeSearchExec);
}

function deleteWordSearch(k) {
	dims_xmlhttprequest_tofunction('admin.php','dims_op=deleteWordSearch&k='+k,actualizeSearchExec);
}

function updateDimsSearch(k,idmodule,idobj,idmetafield,sens) {
	var word = document.getElementById('searchBar_obj_bar').value;
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=search2&word='+word+'&kword='+k+'&idmodule='+idmodule+'&idobj='+idobj+'&idmetafield='+idmetafield+"&sens="+sens,'','result_content');
}

function searchWord() {
	var word = document.getElementById('searchBar_obj_bar').value;
	var deb=word.length-1;
	var c=word.substring(deb);
	if (c!=' ') {
		document.getElementById('searchBar_obj_bar').value+=" ";
	}

        word=word.replace('&','[et]');
        dims_xmlhttprequest_todivpost('admin-light.php','dims_op=search2&word='+word,'','result_content');

        if ($('searchBar_obj_bar')!=null) {
                $('#zonebuttondelete').css('visibility','visible');
                var elem=document.getElementById('searchBar_obj_bar');
                elem.focus();

        }
}

function actualizeSearch(k,word,type) {
	dims_xmlhttprequest_tofunction('admin.php','dims_op=actualizeSearch&k='+k+"&word="+word+"&type="+type,actualizeSearchExec);

}

function actualizeSearchExec(result) {
	document.getElementById('searchBar_obj_bar').value=result;
	if (result.length==0) window.location.href='/admin.php?dims_op=reset_selection';
	else searchWord();
}

function dims_word_keyup(e) {
	if (document.getElementById('searchBar_obj_bar').value=='') {
		// on reinit
		dims_xmlhttprequest('admin-light.php','dims_op=initSearchWord');
		deleteSelected();
	}

}

function dims_word_keyupExec(e) {
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field

	switch(e.keyCode) {
		case 13:
			searchWord();
		default:
			break;
		break;
	}
}

function deleteSelected() {
	/* hide dims_popup*/
	dims_getelem('dims_popup').style.visibility='hidden';
	currentcampaign=0;
	window.location.href='/admin.php?dims_op=reset_selection';
	//dims_xmlhttprequest_tofunction('admin-light.php','dims_op=word_deleteselected',deleteSelectedExec);
	//var word = document.getElementById('searchBar_obj_bar');
	//word.value="";
}

//Cyril 08/03/2012 - fonction JQUERY de préchargement d'images
(function($) {
    var imgList = [];
    $.extend({
        preload: function(imgArr, option) {
            var setting = $.extend({
                init: function(loaded, total) {},
                loaded: function(img, loaded, total) {},
                loaded_all: function(loaded, total) {}
            }, option);
            var total = imgArr.length;
            var loaded = 0;

            setting.init(0, total);
            for(var i in imgArr) {
                imgList.push($("<img />")
                    .attr("src", imgArr[i])
                    .load(function() {
                        loaded++;
                        setting.loaded(this, loaded, total);
                        if(loaded == total) {
                            setting.loaded_all(loaded, total);
                        }
                    })
                );
            }

        }
    });
})(jQuery);


