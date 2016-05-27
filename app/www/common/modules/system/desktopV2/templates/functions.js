function popupAddSector(){
    var id_popup = dims_openOverlayedPopup(300,100);
    dims_xmlhttprequest_todiv("admin.php","dims_op=desktopv2&action=inet_add_sector&id_popup="+id_popup,"","p"+id_popup);
}

function popupAddSectorValue(id_popup) {
    $.post('admin.php', {dims_op: 'desktopv2', action: 'inet_add_sector_value', label: $("#new_sector_label").val()}, function(data) {
        sector = dims_getelem('sector_id');
        sector.options[sector.length] = new Option(data.label, data.id, false, true);
        dims_closeOverlayedPopup(id_popup);
    }, 'json');
}

function popupAddType(){
    var id_popup = dims_openOverlayedPopup(300,100);
    dims_xmlhttprequest_todiv("admin.php","dims_op=desktopv2&action=inet_add_type&id_popup="+id_popup,"","p"+id_popup);
}

function popupAddTypeValue(id_popup) {
    $.post('admin.php', {dims_op: 'desktopv2', action: 'inet_add_type_value', label: $("#new_type_label").val()}, function(data) {
        type = dims_getelem('type_id');
        type.options[type.length] = new Option(data.label, data.id, false, true);
        dims_closeOverlayedPopup(id_popup);
    }, 'json');
}

function popupAddFunction(){
    var id_popup = dims_openOverlayedPopup(250,50);
    dims_xmlhttprequest_todiv("admin.php","dims_op=desktopv2&action=inet_add_function&id_popup="+id_popup,"","p"+id_popup);
}

function flipFlopConnexion(){
    if ($('div.more_connexions').is(':visible')){
        $('div.recent_connexions_ligne_see_more a').html('See more ...');
    }else{
        $('div.recent_connexions_ligne_see_more a').html('See less ...');
    }
    $('div.more_connexions').slideToggle('normal');
};

function flipFlopRecherche(path, label_map, label_as){
    $('div.map_search').hide();
    $('div.filtre_advanced span.map_advanced').html(label_map).css('color', '#222222');
    var keep_it = true;
	if ($('div.cadre_advanced_search').is(':visible')){
        $('div.filtre_advanced span.close_advanced').html(label_as).css('color', '#222222');
		keep_it = false;
    }else{
        $('div.filtre_advanced span.close_advanced').html('<img src="'+path+'/gfx/common/close.png"> '+label_as).css('color', '#DF1D31');
		keep_it = true;
    }
    $('div.cadre_advanced_search').slideToggle('fast', function(){
		$(window).trigger('resize');//Cyril hack permettant de repositionner les divs de preview dans la recherche
	});
	$.ajax({
		type: "POST",
		url: 'admin.php',
		async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
		data: {
			'dims_op' : 'desktopv2',
			'action': 'as_keep_opened',
			'val': (keep_it==true)?'yes':'no'
		},
		dataType: "text"
	});

}
function flipFlopRechMap(path, label_map, label_as){
    $('div.cadre_advanced_search').hide();
    $('div.filtre_advanced span.close_advanced').html(label_as).css('color', '#222222');
    if ($('div.map_search').is(':visible')){
        $('div.filtre_advanced span.map_advanced').html(label_map).css('color', '#222222');
    }else{
        $('div.filtre_advanced span.map_advanced').html('<img src="'+path+'/gfx/common/close.png"> '+label_map).css('color', '#DF1D31');
		initCurrentMap();
    }
    $('div.map_search').slideToggle('fast', function(){
		$(window).trigger('resize');//Cyril hack permettant de repositionner les divs de preview dans la recherche
	});
}

function flip_flop(elem1, elem2, path){
    if (elem1.is(':visible'))
        elem2.attr('src',path+'/gfx/common/deplier_menu.png');
    else
        elem2.attr('src',path+'/gfx/common/replier_menu.png');

	$.ajax({
		type: "POST",
		url: 'admin.php',
		data: {
			'dims_op' : 'desktopv2',
			'action': 'toggle_content_right',
			'bloc': elem1.attr('class'),
			'visible': (!elem1.is(':visible'))?1:0
		}
	});
}

function adapteSearchBlocksPreview(){
	alert('ici');
	$('div.search_result').each(function(){
		//fonctions d'adaptation des positions des blocs de prévisualisation dans les résultats de recherche
		//source : modules/system/desktopV2/templates/result_search/result_search.tpl.php tout à la fin
		adaptePreviewSelector($(this));
		adaptePositionFullPreview($(this), $(this).find('div.full_preview'));
	});
}

function selectTag(elem,path){ // TODO : ajouter la fonction permettant la recherche
    dims_xmlhttprequest('/admin.php','dims_op=desktopv2&action=selectTag&id='+elem.substr(7));
    if ($("div.zone_tag div.tag a."+elem+" img").attr('src') == path+'/gfx/common/tag_vide.png')
        $("div.zone_tag div.tag a."+elem+" img").attr('src', path+'/gfx/common/tag_plein.png');
    else
        $("div.zone_tag div.tag a."+elem+" img").attr('src', path+'/gfx/common/tag_vide.png');
}
function seeMoreGenericTags(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayMoreGenericTags','','zone_generic');
}
function seeLessGenericTags(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayLessGenericTags','','zone_generic');
}
function seeMoreRecentlyTags(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayMoreRecentlyTags','','zone_recently');
}
function seeLessRecentlyTags(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayLessRecentlyTags','','zone_recently');
}
function seeMoreSearchTags(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayMoreSearchTags','','zone_tags');
}
function seeLessSearchTags(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayLessSearchTags','','zone_tags');
}
function searchTags(label){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displaySearchTags&label='+label,'','zone_tags');
}
function searchTagsConcept(label, id_fiche, type_fiche){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displaySearchTagsConcept&label='+label+'&id_fiche='+id_fiche+'&type_fiche='+type_fiche,'','zone_tags');
}
function displayMoreNewsletter(){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayMoreNewsletter','','zone_newsletters');
}

var intervalRefresh = null;
function addressBookSelect(id,type,path,elem){
    if (intervalRefresh == null){
        $("td.icon_fiche_nav_AB img").attr('src',path+'/gfx/common/item_fleche.png');
        $("td.icon_fiche_nav_AB img",elem).attr('src',path+'/gfx/common/selected_item_fleche.png');
        dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayAddressBookContact&id='+id+'&type='+type,'','ab_colonne_3');
        intervalRefresh = setInterval("refreshMenuBasDetailAB()",1000);
    }
}
function refreshMenuBasDetailAB(){
    clearInterval(intervalRefresh);
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=refresh_address_book_menu_bas','','ab_colonne_3_bas');
    intervalRefresh = null
}
function exportVcard(id,type){
    document.location.href='?dims_op=desktopv2&action=export_vcard&id='+id+'&type='+type;
}
function sendVcard(id,type){
    document.location.href='?dims_op=desktopv2&action=send_vcard&id='+id+'&type='+type;
}
function addToFavoriteAB(idgb,refreshLst){
    if (intervalRefresh == null){
        dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=add_to_favorite&id='+idgb,'','ab_favoris');
        intervalRefresh = setInterval("refreshAddressBookLst("+refreshLst+")",1000);
    }
}
function refreshAddressBookLst(refreshLst){
    clearInterval(intervalRefresh);
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=refresh_address_book_groups','','ab_dynamic_groups');
    if (refreshLst)
        dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=refresh_address_book_lst','','ab_colonne_2');
    intervalRefresh = null
}

function addNewContactsGroup(){
    var id_popup = dims_openOverlayedPopup(250,95);
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=add_contacts_group&id_popup='+id_popup,'',"p"+id_popup);
}
function displayContactsGroups(event,id_gb,type){
    dims_showpopup('',350,event,'click','dims_popup',0,0,0);
    dims_xmlhttprequest_todiv('admin.php','dims_op=desktopv2&action=display_group_for_contact&id_gb='+id_gb+"&type="+type,'','dims_popup');
}

function checkGroupForContact(id_gb,type,gr,refrechLst){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=add_contact_to_list&id='+id_gb+"&type="+type+"&id_gr="+gr,'','ab_nb_groups');
    if (intervalRefresh == null){
        intervalRefresh = setInterval("refreshAddressBookYourLst("+refrechLst+")",500);
    }
}
function refreshAddressBookYourLst(refrechLst){
    clearInterval(intervalRefresh);
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=refresh_address_book_your_groups','','ab_your_groups');
    if (refrechLst)
        dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=refresh_address_book_lst','','ab_colonne_2');
    intervalRefresh = null
}

function detachContactAB(id,type, label){
    dims_confirmlink('/admin.php?action=detach_contact_ab&id='+id+"&type="+type,label);
}
function popupChooseTypeContact(submenu, id_from, type_from){
    var id_popup = dims_openOverlayedPopup(190,100);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=choose_type_create_fiche&id_from="+id_from+"&type_from="+type_from+"&id_popup="+id_popup+"&submenu="+submenu,"","p"+id_popup);
}
function popupLinkEntity(submenu, id_go) {
	var id_popup = dims_openOverlayedPopup(1200,8000);
	dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=link_entity&id_from="+id_go+"&id_popup="+id_popup+"&submenu="+submenu,"","p"+id_popup);
}

/* fonction permettant de gérer les ajouts d'e contact et ou'une entreprise */
function addTiers(id_from, type_from) {
    var id_popup = dims_openOverlayedPopup(700,500);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=initAddTiers&id_from='+id_from+'&type_from='+type_from+'&id_popup='+id_popup,'','p'+id_popup);
}

function addContact(id_from, type_from) {
    var id_popup = dims_openOverlayedPopup(700,650);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=initAddContact&id_from='+id_from+'&type_from='+type_from+'&id_popup='+id_popup,'','p'+id_popup);
}

function linkContact(id_from, type_from) {
    var id_popup = dims_openOverlayedPopup(700,650);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=link_contact_popup&id_from='+id_from+'&type_from='+type_from+'&id_popup='+id_popup,'','p'+id_popup);
}

function linkTier(id_from, type_from) {
    var id_popup = dims_openOverlayedPopup(700,650);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=link_tier_popup&id_from='+id_from+'&type_from='+type_from+'&id_popup='+id_popup,'','p'+id_popup);
}

function addCommentConcepts(event,id){
    if (id == null) id = 0;
    //var id_popup = dims_openOverlayedPopup(250,150);
    dims_showpopup('',350,event,'click','dims_popup',0,-140,0);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=add_comment_concepts&id_parent="+id,"","dims_popup");
}
function addCommentAB(event,id){
    if (id == null) id = 0;
    dims_showpopup('',350,event,'click','dims_popup',0,-140,0);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=add_comment_address_books&id_parent="+id,"","dims_popup");
}

function addDocumentConcepts(event,id){
    if (id == null) id = 0;
    dims_showpopup('',350,event,'click','dims_popup',0,0,0);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=add_document_concepts&id_parent="+id,"","dims_popup");
}

var tmpSearchOpp = null;
var tmpSearchOpp2 = null;
function oppSearchVcard(val){
    if (tmpSearchOpp2 != null){
        clearInterval(tmpSearchOpp2);
        tmpSearchOpp2 = null;
    }
    tmpSearchOpp2 = setInterval("execOppSearchVcard('"+val+"')", 1000);
}
function execOppSearchVcard(val){
    clearInterval(tmpSearchOpp2);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_search_vcard&label="+val,"","vcard_results");
    tmpSearchOpp2 = null;
}

function filterEventActivity() {
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    if (document.getElementById('filtereventlabel').value != '') {
        tmpSearchOpp = setInterval("execfilterEventActivity('"+document.getElementById('filtereventlabel').value+"', '"+$("input[type=radio]:checked").val()+"')", 450);
    }
    else {
        $("#div_list_searchevent").hide('fast');
    }

}

function filterEventOpportunity() {
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    if (document.getElementById('filtereventlabel').value != '') {
        tmpSearchOpp = setInterval("execfilterEventOpportunity('"+document.getElementById('filtereventlabel').value+"', '"+$("input[type=radio]:checked").val()+"')", 450);
    }
    else {
        $("#div_list_searchevent").hide('fast');
    }
}

function execfilterEventActivity(label, type_event) {
    clearInterval(tmpSearchOpp);
    //dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_search_event&label="+label,"","div_list_searchevent");
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'activity_search_event',
            label: label,
            type_event: type_event
        },
        success: function(data) {
            if (data != '') {
                $("#div_list_searchevent").html(data);
                $("#div_list_searchevent").show('fast');
            }
            else {
                $("#div_list_searchevent").hide('fast');
            }
        }
    })
}

function execfilterEventOpportunity(label, type_event) {
    clearInterval(tmpSearchOpp);
    //dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_search_event&label="+label,"","div_list_searchevent");
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'opportunity_search_event',
            label: label,
            type_event: type_event
        },
        success: function(data) {
            if (data != '') {
                $("#div_list_searchevent").html(data);
                $("#div_list_searchevent").show('fast');
            }
            else {
                $("#div_list_searchevent").hide('fast');
            }
        }
    })
}

function addEventInActivity(idevent) {
    if (idevent != '') {
        $.ajax({
            url: 'admin.php',
            data: {
                dims_op: 'desktopv2',
                action: 'activity_select_event',
                id_event: idevent
            },
            dataType: 'json',
            success: function(data) {
                if (data.date[2] > 0) {
                    $("#datestart_day").val(data.date[2]);
                    // $("#datestart_day").attr('disabled', 'disabled');
                }
                if (data.date[1] > 0) {
                    $("#datestart_month").val(data.date[1]);
                    // $("#datestart_month").attr('disabled', 'disabled');
                }
                if (data.date[0] > 0) {
                    $("#datestart_year").val(data.date[0]);
                    // $("#datestart_year").attr('disabled', 'disabled');
                    // $("#activity_date_from a").attr('onclick', 'javascript:return false;');
                    //javascript:dims_calendar_open_3('datestart_year', 'datestart_month', 'datestart_day', event);
                }
                if (data.id_country > 0) {
                    $("#activity_country").val(data.id_country);
                    // $("#activity_country").attr('disabled', 'disabled');
                    $("#activity_country").trigger("liszt:updated");
                    // $('#city_activity').removeAttr('disabled');
                    $("#city_activity").trigger("liszt:updated");
                }
                else {
                    $("#activity_country").val('');
                    // $('#activity_country').removeAttr('disabled');
                    $("#activity_country").trigger("liszt:updated");
                    $('#city_activity').val('');
                    // $('#city_activity').removeAttr('disabled');
                    $("#city_activity").trigger("liszt:updated");
                }
            }
        })
    }
    else {
        removeEventFromActivity();
    }
}

function addEventInOpportunity(idevent) {
    if (idevent != '') {
        $.ajax({
            url: 'admin.php',
            data: {
                dims_op: 'desktopv2',
                action: 'opportunity_select_event',
                id_event: idevent
            },
            dataType: 'json',
            success: function(data) {
                if (data.date[2] > 0) {
                    $("#datestart_day").val(data.date[2]);
                    // $("#datestart_day").attr('disabled', 'disabled');
                }
                if (data.date[1] > 0) {
                    $("#datestart_month").val(data.date[1]);
                    // $("#datestart_month").attr('disabled', 'disabled');
                }
                if (data.date[0] > 0) {
                    $("#datestart_year").val(data.date[0]);
                    // $("#datestart_year").attr('disabled', 'disabled');
                    // $("#opportunity_date_from a").attr('onclick', 'javascript:return false;');
                    //javascript:dims_calendar_open_3('datestart_year', 'datestart_month', 'datestart_day', event);
                }
                if (data.id_country > 0) {
                    $("#opportunity_country").val(data.id_country);
                    // $("#opportunity_country").attr('disabled', 'disabled');
                    $("#opportunity_country").trigger("liszt:updated");
                    // $('#city_opportunity').removeAttr('disabled');
                    $("#city_opportunity").trigger("liszt:updated");
                }
                else {
                    $("#opportunity_country").val('');
                    // $('#opportunity_country').removeAttr('disabled');
                    $("#opportunity_country").trigger("liszt:updated");
                    $('#city_opportunity').val('');
                    // $('#city_opportunity').removeAttr('disabled');
                    $("#city_opportunity").trigger("liszt:updated");
                }
            }
        })
    }
    else {
        removeEventFromOpportunity();
    }
}

function removeEventFromActivity() {
    // $(this).prev().attr('selectedIndex', '-1').children('option:selected').removeAttr('selected');
    $("#link").val('');

/*
    $("#datestart_day").removeAttr('disabled');
    $("#datestart_month").removeAttr('disabled');
    $("#datestart_year").removeAttr('disabled');

    $("#activity_date_from a").attr('onclick', "javascript:dims_calendar_open_3('datestart_year', 'datestart_month', 'datestart_day', event);");

    $('#activity_country').removeAttr('disabled');
    $('#city_activity').removeAttr('disabled');
*/

    $("#activity_country").val('');
    $("#activity_country").trigger("liszt:updated");
    $('#city_activity').val('');
    $("#city_activity").trigger("liszt:updated");
}

function removeEventFromOpportunity() {
    // $(this).prev().attr('selectedIndex', '-1').children('option:selected').removeAttr('selected');
    $("#link").val('');

/*
    $("#datestart_day").removeAttr('disabled');
    $("#datestart_month").removeAttr('disabled');
    $("#datestart_year").removeAttr('disabled');

    $("#opportunity_date_from a").attr('onclick', "javascript:dims_calendar_open_3('datestart_year', 'datestart_month', 'datestart_day', event);");

    $('#opportunity_country').removeAttr('disabled');
    $('#city_opportunity').removeAttr('disabled');
*/

    $("#opportunity_country").val('');
    $("#opportunity_country").trigger("liszt:updated");
    $('#city_opportunity').val('');
    $("#city_opportunity").trigger("liszt:updated");
}

function displayDetailContactInActivity(idct,idtiers) {
    if (document.getElementById('detail_search_ct_'+idct).innerHTML == '')
        dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_edit_existingcontact&id="+idct+'&idtiers='+idtiers,"","detail_search_ct_"+idct);
    else
        document.getElementById('detail_search_ct_'+idct).innerHTML = '';
}

function displayDetailContactInOpportunity(idct,idtiers) {
    if (document.getElementById('detail_search_ct_'+idct).innerHTML == '')
        dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_edit_existingcontact&id="+idct+'&idtiers='+idtiers,"","detail_search_ct_"+idct);
    else
        document.getElementById('detail_search_ct_'+idct).innerHTML = '';
}

function displayDetailContactInActivity2(idct,idtiers) {
	$('.detail_search_ct2').html('');
    if (document.getElementById('detail_search_ct2_'+idct).innerHTML == '')
        dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_edit_existingcontact2&id="+idct+'&idtiers='+idtiers,"","detail_search_ct2_"+idct);
    else
        document.getElementById('detail_search_ct2_'+idct).innerHTML = '';
}

function displayDetailContactInOpportunity2(idct,idtiers) {
	$('.detail_search_ct2').html('');
    if (document.getElementById('detail_search_ct2_'+idct).innerHTML == '')
        dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_edit_existingcontact2&id="+idct+'&idtiers='+idtiers,"","detail_search_ct2_"+idct);
    else
        document.getElementById('detail_search_ct2_'+idct).innerHTML = '';
}

function searchActivityCt(label){
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    tmpSearchOpp = setInterval("execSearchCtActivity('"+label+"')", 1000);
}

function searchOpportunityCt(label){
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    tmpSearchOpp = setInterval("execSearchCtOpportunity('"+label+"')", 1000);
}

function execSearchCtActivity(label){
    clearInterval(tmpSearchOpp);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_search_contact&label="+label,"","div_list_search");
    tmpSearchOpp = null;
}

function execSearchCtOpportunity(label){
    clearInterval(tmpSearchOpp);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_search_contact&label="+label,"","div_list_search");
    tmpSearchOpp = null;
}

function searchLinkCt(label){
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    tmpSearchOpp = setInterval("execSearchLinkCt('"+label+"')", 1000);
}
function execSearchLinkCt(label){
    clearInterval(tmpSearchOpp);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=link_search_contact&label="+label,"","div_list_search");
    tmpSearchOpp = null;
}
function searchLinkCtActivity(label){
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    tmpSearchOpp = setInterval("execSearchLinkCtActivity('"+label+"')", 1000);
}

function searchLinkCtOpportunity(label){
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    tmpSearchOpp = setInterval("execSearchLinkCtOpportunity('"+label+"')", 1000);
}

function execSearchLinkCtActivity(label){
    clearInterval(tmpSearchOpp);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=link_search_contact_activity&label="+label,"","div_list_search");
    tmpSearchOpp = null;
}

function execSearchLinkCtOpportunity(label){
    clearInterval(tmpSearchOpp);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=link_search_contact_opportunity&label="+label,"","div_list_search");
    tmpSearchOpp = null;
}

function searchLinkTier(label){
    if (tmpSearchOpp != null){
        clearInterval(tmpSearchOpp);
        tmpSearchOpp = null;
    }
    tmpSearchOpp = setInterval("execSearchLinkTier('"+label+"')", 1000);
}
function execSearchLinkTier(label){
    clearInterval(tmpSearchOpp);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=link_search_tier&label="+label,"","div_list_search");
    tmpSearchOpp = null;
}
function linkExistingContact(id_contact, id_tiers) {
    $.get('/admin.php', { dims_op: 'desktopv2', action: 'link_existing_contact', id_contact: id_contact, id_tiers: id_tiers }, function(data) {
        $("#div_list_added").html(data);
        selectCompanyOpp(id_tiers);
    });
}

function updateEventsList(type_event, selected) {
    if (selected == null) selected = 0;
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'opportunity_get_all_events',
            type_event: type_event
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $("#link").empty();
            $("#link").append('<option value=""></option>');
            for (i = 0; i < data.length; i++) {
                if (data[i].id == selected)
                    $("#link").append('<option value="'+data[i].id+'" selected=true>'+data[i].datejour+' - '+data[i].libelle+'</option>');
                else
                    $("#link").append('<option value="'+data[i].id+'">'+data[i].datejour+' - '+data[i].libelle+'</option>');
            }
            $("select#link").change();
        }
    })
}

function addGroupInActivity(id){
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'add_group_in_activity',
            id: id,
        },
        async: false,
        success: function(data) {
            $("#div_list_added").html(data);
            // ben - 12/11/2012 - #5085
            // selectCompanyOpp(id_tiers);
        }
    })
    /*if (document.getElementById('editbox_search_contact').value != '')
        searchActivityCt(document.getElementById('editbox_search_contact').value);*/
}

function addContactInActivity(id,id_tiers){
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'add_contact_in_activity',
            id: id,
            idtiers: id_tiers
        },
        async: false,
        success: function(data) {
            $("#div_list_added").html(data);
            // ben - 12/11/2012 - #5085
            // selectCompanyOpp(id_tiers);
        }
    })
    /*if (document.getElementById('editbox_search_contact').value != '')
        searchActivityCt(document.getElementById('editbox_search_contact').value);*/
}

function addContactInOpportunity(id,id_tiers){
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'add_contact_in_opportunity',
            id: id,
            idtiers: id_tiers
        },
        async: false,
        success: function(data) {
            $("#div_list_added").html(data);
            if (id > 0)
                selectCompanyOpp(id_tiers);
        }
    })
    /*if (document.getElementById('editbox_search_contact').value != '')
        searchOpportunityCt(document.getElementById('editbox_search_contact').value);*/
}

function delContactInActivity(id,id_tiers){
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'del_contact_in_activity',
            id: id
        },
        async: false,
        success: function(data) {
            $("#div_list_added").html(data);
            selectCompanyOpp(id_tiers);
        }
    });

    /*if (document.getElementById('editbox_search_contact').value != '')
        searchActivityCt(document.getElementById('editbox_search_contact').value);*/
    if (document.getElementById('editbox_search_vcard') != null)
        oppSearchVcard(document.getElementById('editbox_search_vcard').value);
}

function delContactInOpportunity(id,id_tiers){
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'del_contact_in_opportunity',
            id: id
        },
        async: false,
        success: function(data) {
            $("#div_list_added").html(data);
            selectCompanyOpp(id_tiers);
        }
    });

    /*if (document.getElementById('editbox_search_contact').value != '')
        searchOpportunityCt(document.getElementById('editbox_search_contact').value);*/
    if (document.getElementById('editbox_search_vcard') != null)
        oppSearchVcard(document.getElementById('editbox_search_vcard').value);
}

var tmpSearchOpp2 = null;
function searchActivityTiersContact(label, path){
    if (tmpSearchOpp2 != null){
        clearInterval(tmpSearchOpp2);
        tmpSearchOpp2 = null;
    }
    tmpSearchOpp2 = setInterval("execSearchTiersContactActivity('"+label+"','"+path+"')", 500);
}

function searchOpportunityTiersContact(label, path){
    if (tmpSearchOpp2 != null){
        clearInterval(tmpSearchOpp2);
        tmpSearchOpp2 = null;
    }
    tmpSearchOpp2 = setInterval("execSearchTiersContactOpportunity('"+label+"','"+path+"')", 500);
}

function execSearchTiersContactActivity(default_value, path){
    clearInterval(tmpSearchOpp2);
	var label = $('input#editbox_search_company').val();
	if(label != default_value){
		var type_search = $('input:radio[name=typesearch]:checked').val();
		//loader d'attente
		$('div#new_company_result').html('<div style="width: 100%;text-align: center;"><img src="'+path+'/gfx/common/ajax-loader.gif"/></div>');
		dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_search_companycontact&label="+label+"&type="+type_search,"","new_company_result");
		tmpSearchOpp2 = null;
	}
}

function execSearchTiersContactOpportunity(default_value, path){
    clearInterval(tmpSearchOpp2);
	var label = $('input#editbox_search_company').val();
	if(label != default_value){
		var type_search = $('input:radio[name=typesearch]:checked').val();
		//loader d'attente
		$('div#new_company_result').html('<div style="width: 100%;text-align: center;"><img src="'+path+'/gfx/common/ajax-loader.gif"/></div>');
		dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_search_companycontact&label="+label+"&type="+type_search,"","new_company_result");
		tmpSearchOpp2 = null;
	}
}

function detailCompanyOpp(id_tiers) {
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_company_default_form&id_tiers="+id_tiers,"",'zone_form_selected_company');

    $('#zone_form_selected_company').show('fast');
    $('#new_company_form').hide('fast');
    $('#new_company_result').hide('fast');
}

function show_new_company_form() {
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_company_default_form","",'zone_form_selected_company');

    $('#zone_form_selected_company').show('fast');
    $('#new_company_form').hide('fast');
    $('#new_company_result').hide('fast');
    // Reprise du terme de recherche
    $('#company_intitule').val($('#editbox_search_company').val());
}
function show_new_contact_form(show_full_form) {
    // show_full_form sert uniquement lorsqu'on crée un utilisateur qui est pas rattaché à une entreprise
    $.ajax({
        url: '/admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'opportunity_switch_to_form',
            reset_company: show_full_form
        },
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#zone_form_selected_company").html(data);
            $('#zone_form_selected_company').show('fast');
            $('#new_company_form').hide('fast');
            $('#new_company_result').hide('fast');
        }
    })

    if (show_full_form) {
        $('#new_company_contact').show('fast');
    }
}
function hide_new_company_form() {
    $('#zone_form_selected_company').hide('fast');
    $('#new_company_form').show('fast');
    $('#new_company_result').show('fast');

}
function createCompanyOpp(){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_create_company","","new_company_result");
}

function addNewTag(classDiv){
    var val = $("."+classDiv+" li.search-field input").val();
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'add_new_tag',
            'val': val
        },
        dataType: "json",
        success: function(data){
            if(data != null){
                $("."+classDiv+" select").append('<option value="'+data['id']+'" selected=true>'+data['tag']+'</option>').trigger("liszt:updated");
            }
        },
        error: function(data){}
    });
}
function selTag(classSel,id){
    dims_xmlhttprequest('/admin.php','dims_op=desktopv2&action=selecte_tag_opp&class='+classSel+"&id="+id);
}

function addNewCity(idSelect,idCountry){
    var mId = document.getElementById(idCountry).options[document.getElementById(idCountry).selectedIndex].value;
    var val = $("."+idSelect+" div.chzn-search input").val();
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'add_new_city',
            'val': val,
            'id': mId
        },
        dataType: "json",
        success: function(data){
            if(data != null){
                $("."+idSelect+" select").append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
            }
        },
        error: function(data){}
    });
}
function addNewSector(idSelect){
    var val = $("#"+idSelect+" div.chzn-search input").val();
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'add_new_sector',
            'val': val
        },
        dataType: "json",
        success: function(data){
            if(data != null){
                $("#"+idSelect+" select").append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
            }
        },
        error: function(data){}
    });
}
function addNewType(idSelect){
    var val = $("#"+idSelect+" div.chzn-search input").val();
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'add_new_type',
            'val': val
        },
        dataType: "json",
        success: function(data){
            if(data != null){
                $("#"+idSelect+" select").append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
            }
        },
        error: function(data){}
    });
}
function refreshCityOfCountry(id,idCity,sel){
    if(tmpSearchOpp != null) clearInterval(tmpSearchOpp);
    if(sel == null) sel = 0;
    tmpSearchOpp = null;
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_refresh_city&id="+id+"&ref="+idCity+"&sel="+sel,"",idCity);
}
function saveCompanyActivity(){
    var allInput = '';
    $("div.new_company div.zone_new_company input").each(function(){
        if ($(this).attr("name") != null)
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });
    $("div.new_company div.zone_new_company select").each(function(){
        if ($(this).attr("name") != null)
            allInput += "&"+$(this).attr("name")+"="+$(this).val();
    });

    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_save_company"+allInput,"",'new_company_result');
}

function saveCompanyOpportunity(){
    var allInput = '';
    $("div.new_company div.zone_new_company input").each(function(){
        if ($(this).attr("name") != null)
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });
    $("div.new_company div.zone_new_company select").each(function(){
        if ($(this).attr("name") != null)
            allInput += "&"+$(this).attr("name")+"="+$(this).val();
    });

    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_save_company"+allInput,"",'new_company_result');
}

function saveOppModifyContact(divType, id, id_tiers) {
    var allInput = '';
    $("div#"+divType+id+" input").each(function(){
        if ($(this).attr("name") != null)
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });
    $("div#"+divType+id+" select").each(function(){
        if ($(this).attr("name") != null)
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_save_contact_existing"+allInput+"&id="+id+"&id_tiers="+id_tiers,"",divType+id);
}

function saveOppNewContact(){
    var allInput = '';
    $("div.new_company_contact div.zone_new_company_contact input").each(function(){
        if ($(this).attr("name") != null && $(this).val() != '')
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });
    $("div.new_company_contact div.zone_new_company_contact select").each(function(){
        if ($(this).attr("name") != "tags" && $(this).attr("name") != null && $(this).val() != '')
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });
    $("input#editbox_search_company").val("");
    // dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=null","","new_company_result");
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_save_contact"+allInput,"",'div_list_added');
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_company_default_form","",'zone_form_selected_company');
    hide_new_company_form();
}

function selectCompanyOpp(id){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_select_company&id="+id,"",'new_company_result');
    addContactInOpportunity(0,id);
}
function ununselectCompanyOpp(id){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_ununselect_company&id="+id,"",'div_list_added');
    selectCompanyOpp(0);
}
function keepCompany(id){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=keepCompany&id="+id,"",'div_list_added');
    selectCompanyOpp(0);
}
function DontkeepCompany(id){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=DontkeepCompany&id="+id,"",'div_list_added');
    selectCompanyOpp(0);
}
function unselCompanyOpp(id){
    dims_xmlhttprequest("/admin.php","dims_op=desktopv2&action=opportunity_unselect_company&id="+id);
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_company_default_form","",'zone_form_selected_company');

}
function activityCtSwitchVcard(){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_switch_to_vcard","",'zone_form_selected_company');
}
function opportunityCtSwitchVcard(){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_to_vcard","",'zone_form_selected_company');
}
function activityCtSwitchForm(){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_switch_to_form","",'zone_form_selected_company');
}
function opportunityCtSwitchForm(){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_to_form","",'zone_form_selected_company');
}

function opportunityDisplaySearchVcard(type){
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_vcard_form&type="+type,"",'display_vcard_search');
    dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_vcard_form2&type="+type,"",'computer_vcard');
}

function initCurrentMap(){
	//get_current_mapmode
	$.ajax({
		type: "POST",
		url: "/admin.php",
		data: {
			'dims_op' : 'desktopv2',
			'action' : 'get_current_mapmode'
		},
		dataType: "text",
		success: function(data){
			renderGeographic(data);
		},
		error: function(data){}
	});
}

function renderGeographic(mode) {
	$('div.map_search div.time p a').each(function(){
		if($(this).hasClass('selected')) $(this).removeClass('selected');
	});

	$('div.map_search div.time p #'+mode).addClass('selected');
	$('div.map_search div.time p select').val(-1);

	$.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'view_geographic',
            'mode': mode
        },
        dataType: "json",
        success: function(data){
            if(data != null){
				initWorld();
				var ref = 'DF1D31';
				var r = parseInt(ref.substring(0,2), 16);
				var g = parseInt(ref.substring(2,4), 16);
				var b = parseInt(ref.substring(4,6), 16);

				var hsl = rgbToHsl(r, g, b);


                for(var iso in data) {
					var tab = data[iso];

					percent = tab['total'];
					if(percent < 15) percent = 15;//sueil pour quand même y voir quelque chose

					var ref50 = (percent * (1-hsl[2])) / 100;
					var lumi = 1 - ref50;

					var rgb = hslToRgb(hsl[0],hsl[1], lumi);

					var color = '#'+rgb[0].toString(16)+rgb[1].toString(16)+rgb[2].toString(16);
					selectWorld(iso,color,tab['label'],tab['id']);
				}
            }
        },
        error: function(data){}
    });
}
//fonction qui permet de décoloriser la map avant de la recoloriser
function initWorld(){
	init_in_progress = true;
	var carte_svg = document.getElementById('carte').getSVGDocument();
	if(carte_svg != null){
		var x = carte_svg.getElementsByTagName("g");

		for(var i=0;i<x.length;i++) {
			var e=carte_svg.getElementById(x[i].id);
			if(e!= null && e.hasAttribute('style')){
				e.removeAttribute('style');
				e.removeAttribute('title');
				e.removeAttribute('onclick');

				var r = e.getElementsByTagName("path");
				for(var j=0;j<r.length;j++) {
					var z=carte_svg.getElementById(r[j].id);
					if(z != null && z.hasAttribute('style')){
						z.removeAttribute('style');
						z.removeAttribute('title');
						z.removeAttribute('onclick');
					}
				}
			}
		}
	}
	init_in_progress = false;
}

function selectWorld(classname, color, label, id) {
	var activated = false;
	var carte_svg = document.getElementById('carte').getSVGDocument();
	if(carte_svg != null){
		var x = carte_svg.getElementsByTagName("g");
		for(var i=0;i<x.length;i++) {
			var e=carte_svg.getElementById(x[i].id);
			if (x[i].id==classname) {
			//if ( re.test(e.getAttribute('class')) ) {
				e.setAttribute('style','cursor:pointer;fill:'+color);
				e.setAttribute('title',label);
				e.setAttribute('onclick', "javascript:parent.location.href='/admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=country&val="+id+"';");
				var r = e.getElementsByTagName("path");
				activated = true;

				for(var j=0;j<r.length;j++) {
					var z=carte_svg.getElementById(r[j].id);

					z.setAttribute('style','cursor:pointer;fill:'+color);
					z.setAttribute('title',label);
					z.setAttribute('onclick', "javascript:parent.location.href='/admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=country&val="+id+"';");
					activated = true;
				}
			}
		}
		if(!activated){//alors on est peut-être sur un path qui n'est pas entouré d'un g
			var x = carte_svg.getElementsByTagName("path");
			for(var i=0;i<x.length;i++) {
				var e=carte_svg.getElementById(x[i].id);
				if (x[i].id==classname) {
					e.setAttribute('style','cursor:pointer;fill:'+color);
					e.setAttribute('title',label);
					e.setAttribute('onclick', "javascript:parent.location.href='/admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=country&val="+id+"';");
				}
			}
		}
	}
}
/**
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes r, g, and b are contained in the set [0, 255] and
 * returns h, s, and l in the set [0, 1].
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
 */
function rgbToHsl(r, g, b){
    r /= 255, g /= 255, b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min){
        h = s = 0; // achromatic
    }else{
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max){
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }

    return [h, s, l];
}

/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes h, s, and l are contained in the set [0, 1] and
 * returns r, g, and b in the set [0, 255].
 *
 * @param   Number  h       The hue
 * @param   Number  s       The saturation
 * @param   Number  l       The lightness
 * @return  Array           The RGB representation
 */
function hslToRgb(h, s, l){
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return [Math.ceil(r * 255), Math.ceil(g * 255), Math.ceil(b * 255)];
}

function openContactBloc(id, type, bloc_id) {
	dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayAddressBookContact&id='+id+'&type='+type,'',bloc_id);
}

function openLinkBloc(id, type,linkid, bloc_id) {
	dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=displayLinkInfo&id='+id+'&type='+type+'&idlink='+linkid,'',bloc_id);
}

// fonction permettant de désactiver le submit auto lors de l'appui sur "entrer" dans un champ input
function desactiveEnterSubmit(id){
	$("input#"+id).keypress(function(event){

		if (event.keyCode == 13)
			event.preventDefault();
	});
}
function desactiveClicSubmit(id){
	$("input#"+id).click(function(event){ event.preventDefault(); });
}

// fonctions d'import de contacts depuis une vcard
// importée depuis le pc
function mergeVcardWithContact(path,num){
    var id_ct = $("div#import_vcard_"+num+" input:checked").val();
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=opp_pc_merge_vcard_with_ct&id_ct='+id_ct+'&file='+path+'&num='+num,'',"import_vcard_"+num);
}
function createContactFromVcardPc(path,num){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=opp_pc_merge_vcard_with_ct&file='+path+'&num='+num,'',"import_vcard_"+num);
}
// déjà importée dans Dims
function openDisplayVcardExisting(id,num){
	var id_popup = dims_openOverlayedPopup(700,400);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=displayInfoFromVcfExisting&id='+id+'&num='+num+'&id_popup='+id_popup,'','p'+id_popup);
}
function createContactFromVcardExisting(id,num){
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=opp_existing_merge_vcard_with_ct&id='+id+'&num='+num,'',"import_vcard_"+num);
}
function mergeVcardExistingWithContact(id,num){
    var id_ct = $("div#import_vcard_"+num+" input:checked").val();
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=opp_existing_merge_vcard_with_ct&id_ct='+id_ct+'&id='+id+'&num='+num,'',"import_vcard_"+num);
}

function chooseCategSelection(id){
	var id_popup = dims_openOverlayedPopup(400,200);
	if(id.length != null) {
		dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=choose_global_selection&id[]='+id.join('&id[]=')+"&id_popup="+id_popup,'','p'+id_popup);
	}
	else {
		dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=choose_global_selection&id='+id+"&id_popup="+id_popup,'','p'+id_popup);
	}
}
function addGlobalSelection(id,id_categ,label){
	if (id_categ > 0)
		dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=add_global_selection&id='+id+"&id_categ="+id_categ,'','my_selections');
	else
		dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=add_global_selection&id='+id+"&label="+label,'','my_selections');
}
function link_entity(idelem_go) {
	dims_xmlhttprequest('/admin.php','dims_op=desktopv2&action=save_link_entity&id_from='+idelem_go);
	document.location.href='admin.php';
}


/************   Fonctions de gestion des suivis (intranet)   ************/

function addSuiviConcepts() {
    document.getElementById('dims_popup').innerHTML="";
    var idpopup = dims_openOverlayedPopup(950, 600);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=ajouter_suivi&id_popup='+idpopup,'','p'+idpopup);
}

function openSuivi(id_suivi) {
    document.getElementById('dims_popup').innerHTML="";
    var idpopup = dims_openOverlayedPopup(950, 600);
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=editer_suivi&id_suivi='+id_suivi+'&id_popup='+idpopup,'','p'+idpopup);
}

function newSuiviDetail(id_suivi) {
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=editer_suivi_detail&id_suivi='+id_suivi,'','suiviDetail');
}

function editSuiviDetail(id_suivi, suivi_detail_id) {
    dims_xmlhttprequest_todiv('admin.php', 'dims_op=desktopv2&action=editer_suivi_detail&id_suivi='+id_suivi+'&suivi_detail_id='+suivi_detail_id, '', 'suiviDetail');
}


/************   Fonctions de gestion des suivis (intranet)   ************/

function changeViewMode(view){
  document.location.href = '?viewmode='+view;
}

function addTodo(id_record, id_object){
    var id_popup = dims_openOverlayedPopup(300,260);
    dims_xmlhttprequest_todiv('/admin.php','dims_op=display_add_todo&id_popup='+id_popup+'&id_record='+id_record+'&id_object='+id_object,'',"p"+id_popup);
}

/************   Fonctions de gestion des activités (intranet)   ************/
// gestion des contacts
var timeoutIdActivity;

function searchArticleCatalogueKey() {
    searchString=$('#suivi_detail_code').val();

    window.clearTimeout(timeoutIdActivity);
    timeoutIdActivity=window.setTimeout("searchArticleCatalogue('"+searchString+"')",300);
}

function searchArticleCatalogue() {
    if (searchString.length>=2) {

        $('#dyncontentArticle').empty();
        $('#dyncontentArticle').append('<img src="./common/img/loading.gif" alt="">')
        $.ajax({
            type: "GET",
            url: "admin.php",
            data: {
                'dims_op' : 'desktopv2',
                'action' : 'suivi_search_article',
                'searchString' : searchString
            },
            dataType: "json",
            async: false,
            success: function(data){
                $('#dyncontentArticle').empty();

                if (data.length) {
                    for (i = 0; i < data.length; i++) {
                        $('#dyncontentArticle').append(
                            '<p id="search_art_' + data[i].id_article + '" style="border-bottom: 1px solid #D6D6D6; padding: 1px 0;">' + data[i].label  + ' ' + data[i].putarif_1 + ' HT' +
                            '<a href="javascript:void(0);" onclick="javascript:AddArticleSuivi(\'' + data[i].reference + '\',' + data[i].putarif_1 + ',\'' + data[i].labelencoded + '\'' + ',' + data[i].valuetva +');" title="Ajouter cet article"><img style="float: right;" src="./common/img/add.gif" /></a></p>'
                            );
                    }

                }
                else {
                    $('#dyncontentArticle').append('<p>Aucun résultat.</p>');
                }
            }
        });
    }
}

function AddArticleSuivi(id_art,prix,libelle,tva) {
    $('#suivi_detail_code').val(id_art);
    $('#suivi_detail_pu').val(prix);
    $('textarea#suivi_detail_libelle').val(libelle);
    $('#suivi_detail_tauxtva').val(tva);
    $('#dyncontentArticle').empty();
    $('#suivi_detail_qte').focus();
}

function activitySearchContactKey(searchString, tpl_path) {
    window.clearTimeout(timeoutIdActivity);
    timeoutIdActivity=window.setTimeout("activitySearchContact('"+searchString+"','"+tpl_path+"')",300);
}

function activitySearchContact(searchString, tpl_path) {
    if (searchString.length>=2) {
        $.ajax({
            type: "GET",
            url: "admin.php",
            data: {
                'dims_op' : 'desktopv2',
                'action' : 'activity_search_contact',
                'searchString' : searchString
            },
            dataType: "json",
            async: false,
            success: function(data){
                $('#searchContactResults').empty();

                if (data.length) {
                    for (i = 0; i < data.length; i++) {
                        $('#searchContactResults').append(
                            '<p id="search_ct_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].firstname + ' ' + data[i].lastname +
                            '<a href="javascript:void(0);" onclick="javascript:activityAddContact(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter ce contact"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a></p>'
                            );
                    }
                    $('#searchContactResults').append(
                    '<br><a href="javascript:void(0);" onclick="javascript:activityAddNewContact(\'' + tpl_path + '\')"><img src="./common/img/add.gif"> Nouveau</a>');
                }
                else {
                    $('#searchContactResults').append('<p>Aucun résultat.<br><a href="javascript:void(0);" onclick="javascript:activityAddNewContact(\'' + tpl_path + '\')"><img src="./common/img/add.gif"> Nouveau</a></p>');
                }
            }
        });
    }
}

function activityAddUndoNewcontact() {
    $('#contentAddContact').css('visibility',"hidden");
    $('#contentAddContact').css('display',"none");
    $('#contactSearch').focus();
}


function activitySaveNewContact(){
    var allInput = '';

    $("#contentAddContact input, #contentAddContact select").each(function(){
        if ($(this).attr("name") != null && $(this).val() != '')
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });

    $("input#contactSearch").val("");

    dims_xmlhttprequest_tofunction("/admin.php","dims_op=desktopv2&action=activitySaveNewContact"+allInput,executeSuiteAddNewcontact);
}
var tpltemp;
function executeSuiteAddNewcontact(result) {
    contact_id_go=result;
    if (contact_id_go>0) {
        activityAddContact(contact_id_go, tpltemp);
        activityAddUndoNewcontact();
    }
}

function activityAddNewContact(tpl_path) {
    $('#contentAddContact').css('visibility',"visible");
    $('#contentAddContact').css('display',"block");
    $('#lastname').focus();
    tpltemp=tpl_path;
}

function activityAddContact(contact_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_add_contact',
            'contact_id_go' : contact_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_ct_'+contact_id_go).remove();
            $('#contactsList').append(
                '<table id="added_ct_' + data.c.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td class="w20p txtcenter"><img src="' + data.c.photoPath + '" alt="' + data.c.lastname + ' ' + data.c.firstname + '" title="' + data.c.lastname + ' ' + data.c.firstname + '" /></td>' +
                '<td>' + data.c.lastname + ' ' + data.c.firstname + '<br/><em>' + data.t.intitule + '</em></td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveContact(' + data.c.id_globalobject + ');" title="Enlever ce contact"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
        },
        error: function(data) {
        }
    });
}
function activityRemoveContact(contact_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_remove_contact',
            'contact_id_go' : contact_id_go
        },
        async: false,
        success: function() {
            $('#added_ct_'+contact_id_go).remove();
        }
    });
}

// gestion des opportunités
function activitySearchOpportunity(searchString, tpl_path) {
    $.ajax({
        type: "GET",
        url: "admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_search_opportunity',
            'searchString' : searchString
        },
        dataType: "json",
        async: false,
        success: function(data){
            $('#searchOpportunityResults').empty();

            if (data.length) {
                for (i = 0; i < data.length; i++) {
                    $('#searchOpportunityResults').append(
                        '<p id="search_opp_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].libelle +
                        '<a href="javascript:void(0);" onclick="javascript:activityAddOpportunity(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter cette opportunité"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a></p>'
                        );
                }
            }
            else {
                $('#searchOpportunityResults').append('<p>Aucun résultat.</p>');
            }
        }
    });
}
function activityAddOpportunity(opportunity_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_add_opportunity',
            'opportunity_id_go' : opportunity_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_opp_'+opportunity_id_go).remove();
            $('#opportunitiesList').append(
                '<table id="added_opp_' + data.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td>' + data.libelle + '</td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveOpportunity(' + data.id_globalobject + ');" title="Enlever cette opportunité"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
        },
        error: function(data) {
        }
    });
}
function activityRemoveOpportunity(opportunity_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_remove_opportunity',
            'opportunity_id_go' : opportunity_id_go
        },
        async: false,
        success: function() {
            $('#added_opp_'+opportunity_id_go).remove();
        }
    });
}

// gestion des documents
function activitySearchDocument(searchString, tpl_path) {
    $.ajax({
        type: "GET",
        url: "admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_search_document',
            'searchString' : searchString
        },
        dataType: "json",
        async: false,
        success: function(data){
            $('#searchDocumentResults').empty();

            if (data.length) {
                for (i = 0; i < data.length; i++) {
                    $('#searchDocumentResults').append(
                        '<p id="search_doc_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].name +
                        '<a href="javascript:void(0);" onclick="javascript:activityAddDocument(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter ce document"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a>' +
                        '<a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data[i].md5id + '\');" title="Prévisualiser ce document"><img style="float: right;" src="' + tpl_path + '/gfx/common/previsu.png" /></a></p>'
                        );
                }
            }
            else {
                $('#searchDocumentResults').append('<p>Aucun résultat.</p>');
            }
        }
    });
}
function activityAddDocument(doc_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_add_document',
            'doc_id_go' : doc_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_doc_'+doc_id_go).remove();
            $('#documentsList').append(
                '<table id="added_doc_' + data.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td class="w20p txtcenter"><img src="' + tpl_path + '/gfx/common/doc32.png" alt="' + data.name + '" title="' + data.name + '" /></td>' +
                '<td>' + data.name + '</td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data.md5id + '\');" title="Prévisualiser ce document"><img src="' + tpl_path + '/gfx/common/previsu.png" /></a></td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveDocument(' + data.id_globalobject + ');" title="Enlever ce document"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
        },
        error: function(data) {
        }
    });
}
function activityRemoveDocument(doc_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'activity_remove_document',
            'doc_id_go' : doc_id_go
        },
        async: false,
        success: function() {
            $('#added_doc_'+doc_id_go).remove();
        }
    });
}

/************   Fonctions de gestion des opportunités (leads) (intranet)   ************/
// gestion des contacts
function leadSearchContactKey(searchString, tpl_path) {
    window.clearTimeout(timeoutIdActivity);
    timeoutIdActivity=window.setTimeout("leadSearchContact('"+searchString+"','"+tpl_path+"')",300);
}

function leadSearchContact(searchString, tpl_path) {
    $.ajax({
        type: "GET",
        url: "admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'lead_search_contact',
            'searchString' : searchString
        },
        dataType: "json",
        async: false,
        success: function(data){
            $('#searchContactResults').empty();

            if (data.length) {
                for (i = 0; i < data.length; i++) {
                    $('#searchContactResults').append(
                        '<p id="search_ct_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].firstname + ' ' + data[i].lastname +
                        '<a href="javascript:void(0);" onclick="javascript:leadAddContact(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter ce contact"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a></p>'
                        );
                }
            }
            else {
                $('#searchContactResults').append('<p>Aucun résultat.<br><a href="javascript:void(0);" onclick="javascript:leadAddNewContact(\'' + tpl_path + '\')"><img src="./common/img/add.gif"> Nouveau</a></p>');
            }
        }
    });
}

function leadAddUndoNewcontact() {
    $('#contentAddContact').css('visibility',"hidden");
    $('#contentAddContact').css('display',"none");
    $('#contactSearch').focus();
}


function leadSaveNewContact(){
    var allInput = '';

    $("#contentAddContact input, #contentAddContact select").each(function(){
        if ($(this).attr("name") != null && $(this).val() != '')
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });

    $("input#contactSearch").val("");

    dims_xmlhttprequest_tofunction("/admin.php","dims_op=desktopv2&action=leadSaveNewContact"+allInput,leadExecuteSuiteAddNewcontact);
}
// var tpltemp;
function leadExecuteSuiteAddNewcontact(result) {
    contact_id_go=result;
    if (contact_id_go>0) {
        leadAddContact(contact_id_go, tpltemp);
        leadAddUndoNewcontact();
    }
}

function leadAddNewContact(tpl_path) {
    $('#contentAddContact').css('visibility',"visible");
    $('#contentAddContact').css('display',"block");
    $('#lastname').focus();
    tpltemp=tpl_path;
}

function leadAddContact(contact_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'lead_add_contact',
            'contact_id_go' : contact_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_ct_'+contact_id_go).remove();
            $('#contactsList').append(
                '<table id="added_ct_' + data.c.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td class="w20p txtcenter"><img src="' + data.c.photoPath + '" alt="' + data.c.lastname + ' ' + data.c.firstname + '" title="' + data.c.lastname + ' ' + data.c.firstname + '" /></td>' +
                '<td>' + data.c.lastname + ' ' + data.c.firstname + '<br/><em>' + data.t.intitule + '</em></td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveContact(' + data.c.id_globalobject + ');" title="Enlever ce contact"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
        },
        error: function(data) {
        }
    });
}
function leadRemoveContact(contact_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'lead_remove_contact',
            'contact_id_go' : contact_id_go
        },
        async: false,
        success: function() {
            $('#added_ct_'+contact_id_go).remove();
        }
    });
}

// gestion des documents
function leadSearchDocument(searchString, tpl_path) {
    $.ajax({
        type: "GET",
        url: "admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'lead_search_document',
            'searchString' : searchString
        },
        dataType: "json",
        async: false,
        success: function(data){
            $('#searchDocumentResults').empty();

            if (data.length) {
                for (i = 0; i < data.length; i++) {
                    $('#searchDocumentResults').append(
                        '<p id="search_doc_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].name +
                        '<a href="javascript:void(0);" onclick="javascript:leadAddDocument(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter ce document"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a>' +
                        '<a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data[i].md5id + '\');" title="Prévisualiser ce document"><img style="float: right;" src="' + tpl_path + '/gfx/common/previsu.png" /></a></p>'
                        );
                }
            }
            else {
                $('#searchDocumentResults').append('<p>Aucun résultat.</p>');
            }
        }
    });
}
function leadAddDocument(doc_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'lead_add_document',
            'doc_id_go' : doc_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_doc_'+doc_id_go).remove();
            $('#documentsList').append(
                '<table id="added_doc_' + data.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td class="w20p txtcenter"><img src="' + tpl_path + '/gfx/common/doc32.png" alt="' + data.name + '" title="' + data.name + '" /></td>' +
                '<td>' + data.name + '</td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data.md5id + '\');" title="Prévisualiser ce document"><img src="' + tpl_path + '/gfx/common/previsu.png" /></a></td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:leadRemoveDocument(' + data.id_globalobject + ');" title="Enlever ce document"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
        },
        error: function(data) {
        }
    });
}
function leadRemoveDocument(doc_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'lead_remove_document',
            'doc_id_go' : doc_id_go
        },
        async: false,
        success: function() {
            $('#added_doc_'+doc_id_go).remove();
        }
    });
}

nbDocUploadFields = 0;
function addDocUploadField(tpl_path) {
    $('#documentsList').prepend(
        '<table id="upload_'+nbDocUploadFields+'" class="w100 bb1 h40p"><tr>' +
        '<td><input type="file" name="newDocs[]" /></td>' +
        '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:removeDocUploadField('+nbDocUploadFields+')" title="Annuler"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td>' +
        '</tr></table>');
    nbDocUploadFields++;
}
function removeDocUploadField(fieldId) {
    $('#upload_'+fieldId).remove();
}

function refreshCityOfCountry(id,idCity,sel){
    tmpSearchOpp = null;
    if(tmpSearchOpp != null) clearInterval(tmpSearchOpp);
    if(sel == null) sel = 0;
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op': 'desktopv2',
            'action' : 'client_refresh_city',
            'ref': idCity,
            'id': id,
            'sel': sel
        },
        dataType: "text",
        async: false,
        success: function(data){
           $('#'+idCity).html(data).trigger("liszt:updated");
        },
        error: function(data){}
    });
}

var alreadyCity = false;
function addNewCity(idSelect,idCountry){
    if (!alreadyCity){
        alreadyCity = true;
        var mId = document.getElementById(idCountry).options[document.getElementById(idCountry).selectedIndex].value;
        var val = $("#"+idSelect+" div.chzn-search input").val();
        $.ajax({
            type: "POST",
            url: "/admin.php",
            data: {
                'dims_op': 'desktopv2',
                'action' : 'add_new_city',
                'val': val,
                'id': mId
            },
            dataType: "json",
            success: function(data){
                if(data != null){
                    $("#"+idSelect+" select").append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
                }
                alreadyCity = false;
            },
            error: function(data){
                alreadyCity = false;
            }
        });
    }
}


/************   Fonctions de gestion des propositions de rendez-vous (intranet)   ************/
// gestion des contacts
var timeoutIdAppointmentOffer;

function appointmentOfferSearchContactKey(searchString, tpl_path) {
    window.clearTimeout(timeoutIdAppointmentOffer);
    timeoutIdAppointmentOffer=window.setTimeout("appointmentOfferSearchContact('"+searchString+"','"+tpl_path+"')",300);
}

function appointmentOfferSearchContact(searchString, tpl_path) {
    if (searchString.length>=2) {
        $.ajax({
            type: "GET",
            url: "admin.php",
            data: {
                'dims_op' : 'desktopv2',
                'action' : 'appointment_offer_search_contact',
                'searchString' : searchString
            },
            dataType: "json",
            async: false,
            success: function(data){
                $('#searchContactResults').empty();

                if (data.length) {
                    for (i = 0; i < data.length; i++) {
                        $('#searchContactResults').append(
                            '<p id="search_ct_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].firstname + ' ' + data[i].lastname +
                            '<a href="javascript:void(0);" onclick="javascript:appointmentOfferAddContact(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter ce contact"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a></p>'
                            );
                    }
                }
                else {
                    $('#searchContactResults').append('<p>Aucun résultat.</p>');
                }
            }
        });
    }
}

function appointmentOfferAddUndoNewcontact() {
    $('#contentAddContact').css('visibility',"hidden");
    $('#contentAddContact').css('display',"none");
    $('#contactSearch').focus();
}


function appointmentOfferSaveNewContact(){
    if ( $('#lastname').val() == '' || $('#firstname').val() == '' || $('#email').val() == '' ) {
        alert('Vérifiez les champs obligatoires');
        return false;
    }
    else if($('#email').val() != '' && !$($('#email')).val().match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)){
        alert('Le format de cette adresse email est incorrect');
        return false;
    }

    // si tout est ok, on crée le contact
    var allInput = '';

    $("#contentAddContact input, #contentAddContact select").each(function(){
        if ($(this).attr("name") != null && $(this).val() != '')
            allInput += "&"+$(this).attr("name")+"="+Base64.encode($(this).val());
    });

    $("input#contactSearch").val("");

    dims_xmlhttprequest_tofunction("/admin.php","dims_op=desktopv2&action=appointmentOfferSaveNewContact"+allInput,executeSuiteAddNewcontactAO);
}
var tpltemp;
function executeSuiteAddNewcontactAO(result) {
    contact_id_go=result;
    if (contact_id_go>0) {
        appointmentOfferAddContact(contact_id_go, tpltemp);
        appointmentOfferAddUndoNewcontact();
    }
}

function appointmentOfferAddNewContact(tpl_path) {
    $('#contentAddContact').css('visibility',"visible");
    $('#contentAddContact').css('display',"block");
    $('#lastname').focus();
    tpltemp=tpl_path;
}

function appointmentOfferAddContact(contact_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'appointment_offer_add_contact',
            'contact_id_go' : contact_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_ct_'+contact_id_go).remove();
            $('#contactsList').append(
                '<table id="added_ct_' + data.c.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td class="w20p txtcenter"><img src="' + data.c.photoPath + '" alt="' + data.c.lastname + ' ' + data.c.firstname + '" title="' + data.c.lastname + ' ' + data.c.firstname + '" /></td>' +
                '<td>' + data.c.lastname + ' ' + data.c.firstname + '<br/><em>' + data.t.intitule + '</em></td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:appointmentOfferRemoveContact(' + data.c.id_globalobject + ');" title="Enlever ce contact"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
            $('#contactSearch').val('');
            $('#searchContactResults').empty();
        },
        error: function(data) {
        }
    });
}
function appointmentOfferRemoveContact(contact_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'appointment_offer_remove_contact',
            'contact_id_go' : contact_id_go
        },
        async: false,
        success: function() {
            $('#added_ct_'+contact_id_go).remove();
        }
    });
}

// gestion des documents
function appointmentOfferSearchDocument(searchString, tpl_path) {
    $.ajax({
        type: "GET",
        url: "admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'appointment_offer_search_document',
            'searchString' : searchString
        },
        dataType: "json",
        async: false,
        success: function(data){
            $('#searchDocumentResults').empty();

            if (data.length) {
                for (i = 0; i < data.length; i++) {
                    $('#searchDocumentResults').append(
                        '<p id="search_doc_' + data[i].id_globalobject + '" style="border-bottom: 1px solid #D6D6D6; padding: 4px 0;">' + data[i].name +
                        '<a href="javascript:void(0);" onclick="javascript:appointmentOfferAddDocument(' + data[i].id_globalobject + ', \'' + tpl_path + '\');" title="Ajouter ce document"><img style="float: right;" src="' + tpl_path + '/gfx/common/add.png" /></a>' +
                        '<a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data[i].md5id + '\');" title="Prévisualiser ce document"><img style="float: right;" src="' + tpl_path + '/gfx/common/previsu.png" /></a></p>'
                        );
                }
            }
            else {
                $('#searchDocumentResults').append('<p>Aucun résultat.</p>');
            }
        }
    });
}
function appointmentOfferAddDocument(doc_id_go, tpl_path) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'appointment_offer_add_document',
            'doc_id_go' : doc_id_go
        },
        dataType: 'json',
        async: false,
        success: function(data) {
            $('#search_doc_'+doc_id_go).remove();
            $('#documentsList').append(
                '<table id="added_doc_' + data.id_globalobject + '" class="w100 bb1"><tr>' +
                '<td class="w20p txtcenter"><img src="' + tpl_path + '/gfx/common/doc32.png" alt="' + data.name + '" title="' + data.name + '" /></td>' +
                '<td>' + data.name + '</td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data.md5id + '\');" title="Prévisualiser ce document"><img src="' + tpl_path + '/gfx/common/previsu.png" /></a></td>' +
                '<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:appointmentOfferRemoveDocument(' + data.id_globalobject + ');" title="Enlever ce document"><img src="' + tpl_path + '/gfx/common/supprimer20.png" /></a></td></tr></table>');
        },
        error: function(data) {
        }
    });
}
function appointmentOfferRemoveDocument(doc_id_go) {
    $.ajax({
        type: 'GET',
        url: 'admin.php',
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'appointment_offer_remove_document',
            'doc_id_go' : doc_id_go
        },
        async: false,
        success: function() {
            $('#added_doc_'+doc_id_go).remove();
        }
    });
}

function appointmentOfferSelectDay(curdate) {
    $('#overlay').fadeToggle('fast', function(){
        $.ajax({
            url: 'admin.php',
            data: {
                dims_op: 'desktopv2',
                action: 'appointment_offer_get_hours',
                curdate: curdate
            },
            async: false,
            dataType: 'json',
            success: function(data) {
                if (data == null) {
                    var hf = new Array;
                    hf[0] = '08';
                    hf[1] = '00';
                    var ht = new Array;
                    ht[0] = '09';
                    ht[1] = '00';
                }
                else {
                    var hf = data.heuredeb.split(':');
                    var ht = data.heurefin.split(':');
                }

                var html = '<table>';
                html += '<tr><td colspan="2"><h3>Sélection du créneau</h3></td></tr>';
                html += '<tr>';
                html += '<td>Heure de début</td>';
                html += '<td>';
                html += '<select id="hour_from" name="hour_from" onchange="javascript:appointOfferVerifHour();">';
                for (var i = 0; i < 24; i++) {
                    // fi = texte formatté
                    if (i < 10) var fi = '0'+i;
                    else var fi = i;
                    if (i == hf[0]) var sel = ' selected="selected"';
                    else var sel = '';
                    html += '<option value="'+fi+'"'+sel+'>'+fi+'</option>'
                }
                html += '</select> : ';
                html += '<select id="mins_from" name="mins_from" onchange="javascript:appointOfferVerifHour();">';
                for (var i = 0; i < 60; i+=5) {
                    // fi = texte formatté
                    if (i < 10) var fi = '0'+i;
                    else var fi = i;
                    if (i == hf[1]) var sel = ' selected="selected"';
                    else var sel = '';
                    html += '<option value="'+fi+'"'+sel+'>'+fi+'</option>'
                }
                html += '</select>';
                html += '</td>';
                html += '</tr>';
                html += '<tr>';
                html += '<td>Heure de fin</td>';
                html += '<td>';
                html += '<select id="hour_to" name="hour_to" onchange="javascript:appointOfferVerifHour();">';
                for (var i = 0; i < 24; i++) {
                    // fi = texte formatté
                    if (i < 10) var fi = '0'+i;
                    else var fi = i;
                    if (i == ht[0]) var sel = ' selected="selected"';
                    else var sel = '';
                    html += '<option value="'+fi+'"'+sel+'>'+fi+'</option>'
                }
                html += '</select> : ';
                html += '<select id="mins_to" name="mins_to" onchange="javascript:appointOfferVerifHour();">';
                for (var i = 0; i < 60; i+=5) {
                    // fi = texte formatté
                    if (i < 10) var fi = '0'+i;
                    else var fi = i;
                    if (i == ht[1]) var sel = ' selected="selected"';
                    else var sel = '';
                    html += '<option value="'+fi+'"'+sel+'>'+fi+'</option>'
                }
                html += '</select>';
                html += '</td>';
                html += '</tr>';
                //html += '<tr><td colspan="2"><input type="checkbox" id="whole_day" name="whole_day" value="1" onchange="javascript:appointmentOfferToggleWholeDay();" /> <label for="whole_day">Toute la journée</label></td></tr>';
                html += '<tr><td colspan="2">&nbsp;</td></tr>';
                html += '<tr>';
                html += '<td colspan="2" align="right">';
                html += '<input type="button" value="Valider" class="w60p" onclick="javascript:appointmentOfferSelectHours(\''+curdate+'\');" /> ou ';
                html += '<a href="javascript:void(0);" onclick="javascript:$(\'#overlay\').fadeToggle(\'fast\'); $(\'#planning_popup\').fadeToggle(\'fast\');">Annuler</a>';
                html += '</td>';
                html += '</tr>';
                html += '</table>';

                $('#planning_popup').html(html);
                $('#planning_popup').fadeToggle('fast', function() {
                    $('#hour_from').focus();
                });
            }
        })
    });
}
function appointOfferVerifHour(){
    var heure_from=parseInt($('#hour_from').val()*1);
    var heure_to=parseInt($('#hour_to').val()*1);
    var min_from=parseInt($('#mins_from').val()*1);
    var min_to=parseInt($('#mins_to').val()*1);

    if (heure_from>heure_to) {
        if (heure_from==23) {
            // on met a la meme heure
            //$('#hour_to').val($('#hour_from').val());
            $('#hour_to option[value="'+($('#hour_from').val())+'"]').attr('selected', true);
            if (min_from==55) {
                $('#mins_from option[value="50"]').attr('selected', true);
            }
            $('#mins_to option[value="'+($('#mins_from').val()+5)+'"]').attr('selected', true);
        }
        else
            //$('#hour_to').val($('#hour_from').val()+1);
            $('#hour_to option[value="'+($('#hour_from').val()+1)+'"]').attr('selected', true);
    }
    else {

        if (heure_from==heure_to) {

            if (min_from>min_to || min_from==min_to) {
                if (min_from==55) {
                    $('#mins_from option[value="50"]').attr('selected', true);
                }
                if (min_from<10) min_to='05';
                else min_to=min_from+5;
                $('#mins_to option[value="'+min_to+'"]').attr('selected', true);

            }
        }
    }
}

function appointmentOfferSelectHours(curdate) {
    // vérif heure de début != heure de fin
    var heure_from=parseInt($('#hour_from').val()*1);
    var heure_to=parseInt($('#hour_to').val()*1);
    var min_from=parseInt($('#mins_from').val()*1);
    var min_to=parseInt($('#mins_to').val()*1);

    if(heure_from > heure_to ||
        (heure_from == heure_to &&  min_from >= min_to)){
        alert("L'heure de fin ne peux être inférieure ou égale à l'heure de début !");
    }else{
        $.ajax({
            url: '/admin.php',
            data: {
                dims_op: 'desktopv2',
                action: 'appointment_offer_select_day',
                day: curdate,
                hour_from: $('#hour_from').val(),
                mins_from: $('#mins_from').val(),
                hour_to: $('#hour_to').val(),
                mins_to: $('#mins_to').val()
            },
            async: false,
            dataType: 'json',
            success: function(data) {
                if (data != null) {
                    $('#overlay').fadeToggle('fast');
                    $('#planning_popup').fadeToggle('fast');

                    var date = data[0].split('-');
                    newDate = '<tr id="date_'+i+'"><td>- '+date[2]+'/'+date[1]+'/'+date[0];
                    if ( data[1] == '08' && data[2] == '00' && data[3] == '08' && data[4] == '00' ) {
                        newDate += ' Toute la journée';
                    }
                    else {
                        newDate += ' De '+data[1]+':'+data[2]+' à '+data[3]+':'+data[4];
                    }
                    newDate += '</td>';
                    newDate += '<td><a href="javascript:void(0);" onclick="javascript:appointmentOfferRemoveDay(\''+i+'\');" title="Enlever cette date"><img src="/common/modules/system/desktopV2/templates/gfx/common/close.png" alt="Enlever cette date"/></a></td></tr>';
                    $("#appointmentOfferSelectedDays").append(newDate);
                }
            }
        });
    }
}

function appointmentOfferToggleWholeDay() {
    if($('#whole_day').attr('checked')) {
        $('#hour_from').attr('disabled', 'disabled');
        $('#mins_from').attr('disabled', 'disabled');
        $('#hour_to').attr('disabled', 'disabled');
        $('#mins_to').attr('disabled', 'disabled');
    }
    else {
        $('#hour_from').removeAttr('disabled');
        $('#mins_from').removeAttr('disabled');
        $('#hour_to').removeAttr('disabled');
        $('#mins_to').removeAttr('disabled');
    }
}

function appointmentOfferRemoveDay(curdate) {
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'appointment_offer_remove_day',
            day: curdate
        },
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#appointmentOfferSelectedDays tr#date_"+curdate).remove();
        }
    });
}

function appointmentOfferLoadDates(appointmentOfferId, mode) {
    $.ajax({
        url: 'admin.php',
        data: {
            dims_op: 'desktopv2',
            action: 'appointment_offer_load_dates',
            appointment_offer_id: appointmentOfferId
        },
        async: false,
        dataType: 'json',
        success: function(data) {
            if (data.length) {
                var dates = '<table id="app_offer_dates">';
                for (var i in data) {
                    var date = data[i].datefrom.split('-');
                    dates += '<tr id="date_'+i+'"><td>- '+date[2]+'/'+date[1]+'/'+date[0];
                    if ( data[i].heuredeb == '08:00:00' && data[i].heurefin == '08:00:00' ) {
                        dates += ' Toute la journée';
                    }
                    else {
                        var heuredeb = data[i].heuredeb.split(':');
                        var heurefin = data[i].heurefin.split(':');
                        dates += ' De '+heuredeb[0]+':'+heuredeb[1]+' à '+heurefin[0]+':'+heurefin[1];
                    }
                    dates += '</td>';
                    if (mode == 'planning') {
                        dates += '<td><a href="javascript:void(0);" onclick="javascript:appointmentOfferRemoveDay(\''+i+'\');" title="Enlever cette date"><img src="/common/modules/system/desktopV2/templates/gfx/common/close.png" alt="Enlever cette date"/></a></td>';
                    }
                    dates += '</tr>';
                }
                dates += '</table>';
                $('#appointmentOfferSelectedDays').html(dates);
            }
        }
    });
}

function validateAppointment(id){
    var id_popup = dims_openOverlayedPopup(800,100);
    dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=validate_appointement&id_popup='+id_popup+'&id='+id,'',"p"+id_popup);
}

function addNewCompany(idSelect){
    var val = $("#"+idSelect+"_chzn div.chzn-search input").val();
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'add_new_company',
            'val': val
        },
        dataType: "json",
        success: function(data){
            if(data != null){
                $('#'+idSelect).append(new Option(data.intitule, data.id, true, true));
                $('#'+idSelect).trigger("liszt:updated");
            }
        },
        error: function(data){}
    });
}

function addNewProduct(idSelect){
    var val = $("#"+idSelect+"_chzn div.chzn-search input").val();
    $.ajax({
        type: "POST",
        url: "/admin.php",
        data: {
            'dims_op' : 'desktopv2',
            'action' : 'add_new_product',
            'val': val
        },
        dataType: "json",
        success: function(data){
            if(data != null){
                $('#'+idSelect).append(new Option(data.libelle, data.id, true, true));
                $('#'+idSelect).trigger("liszt:updated");
            }
        },
        error: function(data){}
    });
}

// popup de rappel
function showReminderPopup(app_offer_id) {
    $('#overlay').fadeToggle('fast', function(){
        var html = '<form name="f_rappel" action="/admin.php" method="post">';
        html += '<input type="hidden" name="mode" value="appointment_offer" />';
        html += '<input type="hidden" name="action" value="send_reminder" />';
        html += '<input type="hidden" name="app_offer_id" value="'+app_offer_id+'" />';
        html += '<h3>Envoyer un rappel</h3>';
        html += '<p><input type="radio" id="dest_1" name="dest" value="1" /><label for="dest_1">Tous les participants</label></p>';
        html += '<p><input type="radio" id="dest_2" name="dest" value="2" /><label for="dest_2">Ceux qui n\'ont pas répondu</label></p>';
        html += '<p class="right">';
        html += '<input class="w60p" type="submit" value="Envoyer" />';
        html += ' ou <a href="javascript:void(0);" onclick="javascript:$(\'#overlay\').fadeToggle(\'fast\'); $(\'#planning_popup\').fadeToggle(\'fast\');">Annuler</a>';
        html += '</p></form>';

        $('#planning_popup').html(html);
        $('#planning_popup').fadeToggle('fast');
    });
}
