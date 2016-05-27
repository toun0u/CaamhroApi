<?php
if (!isset($_SESSION['dims']['currentaction'])) $_SESSION['dims']['currentaction']=0;
if (!isset($_SESSION['dims']['subaction'])) $_SESSION['dims']['subaction']=_DIMS_ACTION_MAIN;
if (!isset($_SESSION['dims']['eventstep'])) $_SESSION['dims']['eventstep']=0;

$subaction=dims_load_securvalue('subaction',_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['subaction'],$_SESSION['dims']['subaction']);

$action = new action();
// chargement de la valeur courante et allocation eventuelle si passage de l'id
$id		= dims_load_securvalue('id',_DIMS_NUM_INPUT,true,false,true,$_SESSION['dims']['currentaction']);
$type	= dims_load_securvalue('type',_DIMS_NUM_INPUT,true,false,true,$type,_DIMS_ACTION_MAIN);

if ($id>0) {
	$action->open($id);

	//On verifie si c'est un enfant (id_parent != 0)
	if($action->fields['id_parent'] != 0) {
		//C'est un enfant -> On ouvre le parent
		$id = $action->fields['id_parent'];
		$action->open($id);
	}

	// mise en session de l'action
	$_SESSION['dims']['currentaction']=$action->fields['id'];
	$type = $action->fields['type'];
	$niveau=$action->fields['niveau'];
	$typeaction = $action->fields['typeaction'];
	if($niveau == 1) {
		$_SESSION['dims']['subaction'] = _DIMS_ACTION_MAIN;
	}
}
else {
	$niveau=0;
	// on remet a l'onglet normal si on ouvre une autre action sans etape
	if ($_SESSION['dims']['subaction']==_DIMS_ACTION_ETAP) $_SESSION['dims']['subaction']=_DIMS_ACTION_MAIN;
}

if ($id>0) {
		switch($action->fields['type']) {
			case _PLANNING_ACTION_RDV :
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PLANNING_MODIFY_RDV']." : ".$action->fields['libelle']);
			break;

			case _PLANNING_ACTION_EVT :
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PLANNING_MODIFY_EVT']." : ".$action->fields['libelle']);
			break;

			case _PLANNING_ACTION_RCT :
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PLANNING_MODIFY_RCT']);
			break;
		};
} else {
		switch($type) {
			case _PLANNING_ACTION_RDV :
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PLANNING_ADD_RDV']);
			break;

			case _PLANNING_ACTION_EVT :
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PLANNING_ADD_EVT']);
			break;

			case _PLANNING_ACTION_RCT :
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PLANNING_ADD_RCT']);
			break;
		};

		$datejour=dims_load_securvalue("datejour",_DIMS_CHAR_INPUT,true);

		if ($datejour=="") $datejour=date("d/m/Y");
		$action->init_description();

		// on créé les dates
		$curheure=date("G");
		$curmin=date("i");
		if ($curmin>15) $curheure++;
		// heure de pause
		if ($curheure>=13 && $curheure<=14) $curheure++;
		// délai dépassé
		if ($curheure>=19) $curheure="08";
		$heure_dispo_deb=$curheure.":00";
		$heure_dispo_fin=($curheure+2).":00";

		$dispo_users[]=$_SESSION['dims']['userid'];
}
$_SESSION['dims']['planning']['currenttypeaction'] = $type;

?>
<script type="text/javascript">
	var uploads = new Array();
	var upload_cell, file_name;
	var count = 0;
	var checkCount = 0;
	var check_file_extentions = true;
	var sid = '<?php echo session_id() ; ?>';
	var page_elements = ["toolbar","page_status_bar"];
	var img_path = "../common/img/";
	var path = "";
	var bg_color = false;
	var status;
	var debug = false;
	var param1=<?php echo ($op == 'file_add') ? 'true' : 'false'; ?>;
	var param2=<?php echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
</script>
<script type="text/javascript" language="javascript" src="./include/upload/javascript/uploader.js"></script>
<script type="text/javascript" language="javascript" src="./common/modules/doc/include/javascript.php"></script>
<script language="javascript" type="text/javascript">


	var previous_dossier = '';
	var previous_tiers = '';

	var dossiers_sel_array = new Array();
	var tiers_sel_array = new Array();

	function business_choixtiersdossier(choix) {
		document.form_action.actiondetail_tiers_id.length = 0;
		document.form_action.actiondetail_dossier_id.length = 0;

		document.form_action.rech_tiers.value = '';
		document.form_action.rech_dossier.value = '';

		document.form_action.nouveau_dossier.checked = false;
		document.form_action.nouveau_tiers.checked = false;

		if (choix[0].checked) // clients
		{
			document.form_action.rech_tiers.disabled = false;
			document.form_action.rech_dossier.disabled = true;
			document.form_action.rech_tiers.focus();
		}
		else
		{
			document.form_action.rech_tiers.disabled = true;
			document.form_action.rech_dossier.disabled = false;
			document.form_action.rech_dossier.focus();
		}
	}

	function business_rechercher_tiers(rech,dossier_id) {
		if (rech != previous_tiers	|| rech == '') {
			previous_tiers = rech;

			if (rech.length>=2 || dossier_id != 0) {
				dims_xmlhttprequest_tofunction("index-light.php",'op=xml_planning_rechercher_tiers&rech='+rech+'&dossier_id='+dossier_id, business_traitement_tiers);
			}
		}
	}

	function business_traitement_tiers(result) {
		document.form_action.actiondetail_tiers_id.length = 0;
		var tiers = result.split(";;");
		for (i=0;i<tiers.length;i++) {
			var tiersdetail = tiers[i].split(",,");
			var newOpt	= new Option(tiersdetail[1],tiersdetail[0]);
			document.form_action.actiondetail_tiers_id.options[i] = newOpt;

			for (t=0;t<tiers_sel_array.length;t++) {
				if (tiers_sel_array[t] == tiersdetail[0]) document.form_action.actiondetail_tiers_id.selectedIndex = i;
			}
		}

		if (document.form_action.actiondetail_tiers_id[0].value == 0) {
			document.form_action.nouveau_tiers.checked = true;
		}
		else {
			document.form_action.nouveau_tiers.checked = false;
		}

		if (document.form_action.actiondetail_tiers_id.length == 1) {
			document.form_action.actiondetail_tiers_id.selectedIndex = 0;
			business_update_dossiers('');
		}
	}

	function business_update_tiers(tiers_sel) {
		tiers_sel_array = tiers_sel.split(",");
		if (document.form_action.choix[1].checked && document.form_action.actiondetail_dossier_id.selectedIndex!=-1) {// dossiers
			id_dossier = document.form_action.actiondetail_dossier_id.value
			document.form_action.actiondetail_tiers_id.length = 0;
			business_rechercher_tiers('',id_dossier);

			document.form_action.rech_tiers.disabled = false;
			document.form_action.rech_tiers.value = '';
			//document.form_action.rech_tiers.focus();
		}
	}


	function business_rechercher_dossiers(rech,tiers_id) {
		if (rech != previous_dossier || rech == '') {
			previous_dossier = rech;

			if (rech.length>=2 || tiers_id != 0) {
				dims_xmlhttprequest_tofunction("index-light.php",'op=xml_planning_rechercher_dossiers&rech='+rech+'&tiers_id='+tiers_id, business_traitement_dossiers);
			}
		}
	}

	function business_traitement_dossiers(result) {
		document.form_action.actiondetail_dossier_id.length = 0;
		var dossiers = result.split(";;");
		for (i=0;i<dossiers.length;i++) {
			var dossierdetail = dossiers[i].split(",,");
			var newOpt	= new Option(dossierdetail[1],dossierdetail[0]);
			document.form_action.actiondetail_dossier_id.options[i] = newOpt;

			for (d=0;d<dossiers_sel_array.length;d++) {
				if (dossiers_sel_array[d] == dossierdetail[0]) document.form_action.actiondetail_dossier_id.selectedIndex = i;
			}
		}

		if (document.form_action.actiondetail_dossier_id[0].value == 0) {
			document.form_action.nouveau_dossier.checked = true;
		}
		else {
			document.form_action.nouveau_dossier.checked = false;
		}

		if (document.form_action.actiondetail_dossier_id.length == 1) {
			document.form_action.actiondetail_dossier_id.selectedIndex = 0;
			business_update_tiers('');
		}
	}

	function business_update_dossiers(dossiers_sel) {
		dossiers_sel_array = dossiers_sel.split(",");
		if (document.form_action.choix[0].checked && document.form_action.actiondetail_tiers_id.selectedIndex!=-1) {// clients
			id_tiers = document.form_action.actiondetail_tiers_id.value
			document.form_action.actiondetail_dossier_id.length = 0;
			business_rechercher_dossiers('',id_tiers);

			document.form_action.rech_dossier.disabled = false;
			document.form_action.rech_dossier.value = '';
		}
	}

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
		document.form_action.submit();
	}

	function delete_banner(id_evt) {
		dims_xmlhttprequest('admin.php','op=delete_banner&id_evt='+id_evt);
		document.form_action.submit();
	}

	function delete_match(id_evt) {
		dims_xmlhttprequest('admin.php','op=delete_match&id_evt='+id_evt);
		document.form_action.submit();
	}

	function delete_preview(id_evt) {
		dims_xmlhttprequest('admin.php','op=delete_preview&id_evt='+id_evt);
		document.form_action.submit();
	}

</script>
<script language="javascript" type="text/javascript">
function dims_action_close(){
	action_ajax_method = "all";
	  dims_getelem('block_calendar').style.display = "block";
	  dims_getelem('block_ajax_calendar').style.display = "none";
}

function change_menu(num){
	if(dims_getelem('block_content0'))
		dims_getelem('block_content0').style.display = 'none';
	if(dims_getelem('block_content1'))
		dims_getelem('block_content1').style.display = 'none';
	if(dims_getelem('block_content2'))
		dims_getelem('block_content2').style.display = 'none';
	if(dims_getelem('block_content3'))
		dims_getelem('block_content3').style.display = 'none';
	if(dims_getelem('block_content5'))
		dims_getelem('block_content5').style.display = 'none';
	if(dims_getelem('block_content6'))
		dims_getelem('block_content6').style.display = 'none';

	dims_getelem('block_content'+num).style.display = 'block';

	if(dims_getelem('block0'))
		dims_getelem('block0').className = "onglettoolbar_icon";
	if(dims_getelem('block1'))
		dims_getelem('block1').className = "onglettoolbar_icon";
	if(dims_getelem('block2'))
		dims_getelem('block2').className = "onglettoolbar_icon";
	if(dims_getelem('block3'))
		dims_getelem('block3').className = "onglettoolbar_icon";
	if(dims_getelem('block5'))
		dims_getelem('block5').className = "onglettoolbar_icon";
	if(dims_getelem('block6'))
		dims_getelem('block6').className = "onglettoolbar_icon";

	dims_getelem('block'+num).className = "onglettoolbar_icon_sel";

	if(dims_getelem('step0')){
		dims_getelem('step0').className = "ongletstep";
		if(dims_getelem('step1'))
			dims_getelem('step1').className = "ongletstep";
		if(dims_getelem('step2'))
			dims_getelem('step2').className = "ongletstep";
		if(dims_getelem('step3'))
			dims_getelem('step3').className = "ongletstep";

		dims_getelem('step'+num).className = "ongletstep_sel";
	}
	dims_xmlhttprequest('admin.php', 'op=update_actionetap&step='+num);
}

function deleteEtap(id_actionetap) {
	dims_xmlhttprequest('admin.php', 'op=delete_actionetap&id_actionetap='+id_actionetap);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');
}

function deleteFile(id_etapFile) {
	dims_xmlhttprequest('admin.php', 'op=delete_fileetap&id_fileetap='+id_etapFile);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');
}

function addEtap(id_etap) {
	var label;
	var text;

	label	= dims_getelem('actionetap_label').value;
	text	= dims_getelem('actionetap_description').value;
	condition			= dims_getelem('actionetap_condition').checked;
	condition_content	= dims_getelem('actionetap_condition_content').value;
	date_fin			= dims_getelem('actionetap_date_fin').value;
	condition_label_yes	= dims_getelem('actionetap_condition_label_yes').value;
	condition_label_no	= dims_getelem('actionetap_condition_label_no').value;
	sel_etape			= dims_getelem('actionetap_type_etape').selectedIndex;

	type_etape = dims_getelem('actionetap_type_etape').options[sel_etape].value;

	dims_xmlhttprequest_post('admin.php', 'op=save_actionetap&actionetap_description='+text+'&actionetap_label='+label+'&actionetap_condition='+condition+'&actionetap_condition_content='+condition_content+'&actionetap_date_fin='+date_fin+'&id_metacateg='+id_etap+'&actionetap_condition_label_yes='+condition_label_yes+'&actionetap_condition_label_no='+condition_label_no+'&actionetap_type_etape='+type_etape);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');
}

function moveEtape(id_etap, move) {
	dims_xmlhttprequest_post('admin.php', 'op=saveetap_position&id_etap='+id_etap+'&move='+move);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');
}

function makePayementEtap(id_etap) {
	dims_xmlhttprequest_post('admin.php', 'op=make_payement_etap&id_etap='+id_etap);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');
}

function unmakePayementEtap(id_etap) {
	dims_xmlhttprequest_post('admin.php', 'op=unmake_payement_etap&id_etap='+id_etap);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');
}

function addFile() {
	var id_etap;
	id_etap = dims_getelem('id_etap_file').value;

	dims_xmlhttprequest_post('admin.php', 'op=save_eventfile&id_etap='+id_etap);
	dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction=<?php echo _DIMS_ACTION_ETAP; ?>&id_actionetap=0', '', 'block_content5');

}
</script>
<div style="width:100%;">
	<div style="float:left;width:100%;">
		<div class="onglettoolbar" style="margin-top:5px;margin-left:2px;">
			<div class="onglettoolbar_left">
				<div style="width: 90px;" id="block0" class="onglettoolbar_icon_sel">
					<a onclick="javascript:change_menu(0);" href="javascript:void(0);">
						<div class="onglettoolbar_icon_image"><img border="0" src="./common/img/gantt.png" alt="<?php echo $_DIMS['cste']['_EVENT_DATE_AND_TIME'];?>"/></div>
						<div class="onglettoolbar_icon_title"><?php echo $_DIMS['cste']['_EVENT_DETAILS'];?></div>
					</a>
				</div>

				<div style="width: 120px;" id="block1" class="onglettoolbar_icon">
					<a onclick="javascript:change_menu(1);" href="javascript:void(0);">
						<div class="onglettoolbar_icon_image"><img border="0" src="./common/img/gantt.png" alt="<?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'];?>"/></div>
						<div class="onglettoolbar_icon_title"><?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'];?></div>
					</a>
				</div>
				<?php
					if($typeaction != '_DIMS_PLANNING_FAIR' && $typeaction != '_DIMS_PLANNING_FAIR_STEPS') {
				?>
				<div style="width: 120px;" id="block2" class="onglettoolbar_icon">
					<a onclick="javascript:change_menu(2);" href="javascript:void(0);">
						<div class="onglettoolbar_icon_image">
							<?php
							if($type == _PLANNING_ACTION_EVT) $img_ent="mini_ent.png";
							else $img_ent="contact.png";
							?>
							<img border="0" src="./common/img/<? echo $img_ent; ?>" alt="<?php echo $_DIMS['cste']['_EVENT_DETAILS'];?>"/>
						</div>
						<div class="onglettoolbar_icon_title">
						<?php
							if($type == _PLANNING_ACTION_EVT) {
								echo $_DIMS['cste']['_DIMS_LABEL_PARTNER'];
							}
							else {
								echo $_DIMS['cste']['_DIMS_PARTICIP'];
							}?>
						</div>
					</a>
				</div>
				<?php
					}
				if($type == _PLANNING_ACTION_EVT && $typeaction != '_DIMS_PLANNING_FAIR_STEPS') {
				?>
				<div style="width: 160px;" id="block3" class="onglettoolbar_icon">
					<a onclick="javascript:change_menu(3);" href="javascript:void(0);">
						<div class="onglettoolbar_icon_image"><img border="0" src="./common/img/contact.png" alt="<?php echo $_DIMS['cste']['_DIMS_LABEL_PARTNER'];?>"/></div>
						<div class="onglettoolbar_icon_title">
						<?php echo $_DIMS['cste']['_DIMS_EVT_CONTACT_REGISTER'];?>
						</div>
					</a>
				</div>
				<?php
				}

				if($_SESSION['dims']['currentaction']>0 && $type == _PLANNING_ACTION_EVT)
				{
					if($niveau==2) {
				?>
				<div style="width: 110px;" id="block5" class="onglettoolbar_icon">
					<a onclick="javascript:change_menu(5);" href="javascript:void(0);">
						<div class="onglettoolbar_icon_image"><img border="0" src="./common/img/event.png" alt="<?php echo $_DIMS['cste']['_DIMS_LABEL_VALIDATION'];?>"/></div>
						<div class="onglettoolbar_icon_title"><?php echo $_DIMS['cste']['_DIMS_MILESTONE'];?></div>
					</a>
				</div>
				<?php
					}
					if($typeaction != '_DIMS_PLANNING_FAIR_STEPS') {
				?>
				<div style="width: 180px;" id="block6" class="onglettoolbar_icon">
					<a onclick="javascript:change_menu(6);" href="javascript:void(0);">
						<div class="onglettoolbar_icon_image"><img border="0" src="./common/modules/doc/img/mod16.png" alt="<?php echo $_DIMS['cste']['_EVENT_RESSOURCE_FRONT'];?>"/></div>
						<div class="onglettoolbar_icon_title"><?php echo $_SESSION['cste']['_EVENT_RESSOURCE_FRONT'];?></div>
					</a>
				</div>
				<?php
				require_once(DIMS_APP_PATH . "/modules/wce/include/classes/class_wce_site.php");
				$wcesite = new wce_site($db);
				$urlarticle=$wcesite->getArticleByObject('system','Planning'); // name of moduletype, name of label wce object
				$link = dims_urlencode($urlarticle);
				$link = dims_urlencode($link.'/index.php?id_event='.$action->fields['id']);
				?>
				<div style="width: 135px;" id="block7" class="onglettoolbar_icon">
					<a href="<? echo $link; ?>" target="_blank">
						<div class="onglettoolbar_icon_image"><img border="0" src="./common/img/view.png" alt="<?php echo $_DIMS['cste']['_PREVIEW'];?>"/></div>
						<div class="onglettoolbar_icon_title"><?php echo $_SESSION['cste']['_PREVIEW'];?></div>
					</a>
				</div>

				<?
					}
				}
				if($type == _PLANNING_ACTION_EVT) {
					echo dims_create_button($_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF'],'./common/img/configure.png','javascript:document.location.href=\'admin.php?dims_mainmenu='._DIMS_MENU_HOME.'&submenu='._DIMS_SUBMENU_EVENT.'&dims_desktop=block&dims_action=public&action=view_admin_events\';','','width:150px;float:right;');

					/*
					<div style="width: 180px;" class="onglettoolbar_icon">
						<a href="admin.php?dims_mainmenu=<?php echo _DIMS_MENU_HOME; ?>&submenu=<?php echo _DIMS_SUBMENU_EVENT; ?>&dims_desktop=block&dims_action=public&action=view_admin_events">
							<div class="onglettoolbar_icon_image"><img border="0" src="./common/img/configure.png" alt="<?php echo $_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF'];?>"/></div>
							<div class="onglettoolbar_icon_title"><?php echo $_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF'];?></div>
						</a>
					</div>
					*/

				}
				?>
			</div>
		</div>
		<div class="planning_action_modif_form">

	<?php
	require_once(DIMS_APP_PATH . '/modules/system/xml_planning_modifier_action_detail.php');

	?>
		</div>
	</div>
<? echo $skin->close_simplebloc(); ?>
<script language="javascript" type="text/javascript">

<?php
	/*if(isset($_GET['subtab']) && $_GET['subtab']) {
		switch ($_GET['subtab']) {
			case 'date':
				echo 'change_menu(0);';
				break;
			case 'desc':
				echo 'change_menu(1);';
				break;
			case 'details':
				echo 'change_menu(2);';
				break;
			case 'links':
				echo 'change_menu(3);';
				break;
			case 'etape':
				echo 'change_menu(5);';
				break;
			case 'ress':
				echo 'change_menu(6);';
				break;
		}
	}
	else*/
	if (isset($_SESSION['dims']['eventstep'])) {
		echo 'change_menu('.$_SESSION['dims']['eventstep'].');';
	}
?>
</script>
