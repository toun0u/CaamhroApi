<?php
// if (!isset($_SESSION['training']['planning_weekadd'])) $_SESSION['training']['planning_weekadd'] = 0; // semaine courante

$viewmode = dims_load_securvalue('viewmode',dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['dims']['planning']['viewmode']);
?>

<div id="xmlplanning"></div>

<script language="javascript">
	function affiche_planning(params) {
		if( window.innerWidth) {
			x = window.innerWidth;
		}
		else {
			x=document.body.offsetWidth;
		}

		x = x*0.8;

		if (x<=1024) x-=25;
		// on soustrait la taille du menu de droite (100px) - 20 px pour l'ascenseur �ventuel
		x=x-190-(x/10);

		// on a la taille que l'on doit diviser en 7 en arrondissant
		x=x/7;
		x=x-2;

		params+="&wscreen="+x;
			if(action_ajax_method == "all")
				dims_xmlhttprequest_todiv("index-light.php","op=xml_planning"+params,'','xmlplanning');
			else
				dims_xmlhttprequest_todiv("index-light.php","op=xml_planning"+params+"&ajax=1",'','block_calendar');
	}

	function refresh_planning() {
		affiche_planning('&cat=-1');
		return(true);
	}

	function affiche_planning_delayed(params) {
		setTimeout("affiche_planning('"+params+"')",50);
		return(true);
	}

	var affiche_planning_closure;
	function createClosures() {
		affiche_planning_closure = affiche_planning_delayed;
	}

	window.onload = createClosures;

	window.onresize = function (){
		document.getElementById("xmlplanning").style.visibility="hidden";
		refresh_planning();
		document.getElementById("xmlplanning").style.visibility="visible";
	}

	function dims_action_open(url,data){
	  var retour = dims_xmlhttprequest(url, data);
	  if(retour != ""){
		  dims_hidepopup();
		  action_ajax_method = "ajax";
		  dims_getelem('block_ajax_calendar').innerHTML = retour;
		  dims_getelem('block_calendar').style.display = "none";
		  dims_getelem('block_ajax_calendar').style.display = "block";
	  }else{
		  action_ajax_method = "all";
		  dims_getelem('block_calendar').style.display = "block";
		  dims_getelem('block_ajax_calendar').style.display = "none";
	  }
	}

	function dims_action_close(){
		action_ajax_method = "all";
		  dims_getelem('block_calendar').style.display = "block";
		  dims_getelem('block_ajax_calendar').style.display = "none";
	}

	var action_ajax_method = "all";
	refresh_planning();
	setInterval("refresh_planning()",120000);

	function business_verif_action(form) {
		var perso = (form.action_interne.checked || form.action_personnel.checked || form.action_conges.checked);
		document.form_action.submit();
		return true;
	}

	var timerdisplayresult;

	function searchUserActionPlanning() {
		clearTimeout(timerdisplayresult);
		timerdisplayresult = setTimeout("searchUserActionPlanningExec()", 300);
	}

	function searchUserActionPlanningExec() {
		var nomsearch=document.getElementById("nomsearchplanning").value;
		dims_xmlhttprequest_todiv('admin.php','op=search_action_contact_planning&nomsearch='+nomsearch,'||',"lst_planningtempuser","selectedusers");
	}

	function searchPartnerActionPlanning() {
		clearTimeout(timerdisplayresult);
		timerdisplayresult = setTimeout("searchPartnerActionPlanningExec()", 300);
	}

	function searchPartnerActionPlanningExec() {
		var partsearch=document.getElementById("partsearchplanning").value;
		dims_xmlhttprequest_todiv('admin.php','op=search_action_partner_planning&nomsearch='+partsearch,'||',"lst_planningtemppartner","selectedpartner");
	}

	function updateUserActionFromSelectedPlanning(op,id_user,input) {
		if(input)
		{
			var statPartcip = document.getElementById(input).value;
			dims_xmlhttprequest_tofunction('admin-light.php','op='+op+'&id_user='+id_user+'&stat_particip='+statPartcip,searchUserActionPlanning);
		}
		else
			dims_xmlhttprequest_tofunction('admin-light.php','op='+op+'&id_user='+id_user,searchUserActionPlanning);

	}
	function updatePartnerActionFromSelectedPlanning(op,id_part) {
		dims_xmlhttprequest_tofunction('admin-light.php','op='+op+'&id_part='+id_part,searchPartnerActionPlanning);
	}

	function compareDates(dt1, dt2){
		var dateRegEx = /^([0123]?\d)[\.\-\/\s]?([012]?\d)[\.\-\/\s]?(\d{4})$/;
		var result1 = dt1.match(dateRegEx);
		var result2 = dt2.match(dateRegEx);

		if(result1 != null){
			var month1 = result1[2];
			var day1 = result1[1];
			var year1 = result1[3];
		}
		if(result2 != null){
			var month2 = result2[2];
			var day2 = result2[1];
			var year2 = result2[3];
		}
		if(result1 && result2){
			var datedeb =new Date(year1, month1-1, day1);
			var datefin =new Date(year2, month2-1, day2);
			var one_day=1000*60*60*24;

			dif=Math.ceil((datefin.getTime()-datedeb.getTime())/(one_day));
			return dif;
		}

		return 0;
	}

	function updateDate() {
		var elemdeb=document.getElementById("action_datejour").value;
		var elem=document.getElementById("datefin").value;

		if (elemdeb.length==10 && elem.length==10) {
			nbjour=compareDates(elemdeb,elem);
			var dateRegEx = /^([0123]?\d)[\.\-\/\s]?([012]?\d)[\.\-\/\s]?(\d{4})$/;
			var result1 = elemdeb.match(dateRegEx);
			var today =new Date(result1[3], result1[2]-1, result1[1]);
			joursem=today.getDay();
			// on reinit
			for (i=0;i<=6;i++) {
				document.getElementById("day"+i).checked=false;
			}
			// current day
			document.getElementById("day"+joursem).checked=true;

			// on boucle pour valider les elements de cochage
			if (nbjour>=0) {
				for (i=joursem;i<=(joursem+nbjour);i++) {
					j=i%7;
					var elem=document.getElementById("day"+j);
					elem.checked = true;
				}
			}

		}
	}

	function delete_doc(id_doc) {
		dims_xmlhttprequest('admin.php','op=delete_fileannonce&id_doc='+id_doc);
		document.location.reload();
	}
	var cur_action=-1;
	function displayActionDetail(event,id_action,decal) {
		if (cur_action!=id_action) {
				cur_action=id_action;
				dims_showpopup('',350,event,'click','dims_popup',0,0,decal);
				dims_xmlhttprequest_todiv('admin.php','op=detail_action_planning&id_action='+id_action,'','dims_popup');
		}
	}

</script>
