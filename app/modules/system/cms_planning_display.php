<?php
if (!isset($_SESSION['business']['planning_weekadd'])) $_SESSION['business']['planning_weekadd'] = 0; // semaine courante

// test if empty
if (!isset($_SESSION['business']['users']) || empty($_SESSION['business']['users'])) $_SESSION['business']['users'][$_SESSION['dims']['userid']]=$_SESSION['dims']['userid'];
?>

<div id="business_xmlplanning"></div>

<script language="javascript" type="text/javascript">


	var previous_dossier = '';
	var previous_tiers = '';

	var dossiers_sel_array = new Array();
	var tiers_sel_array = new Array();


	var cur_action=-1;
	function displayActionDetail(event,id_action,decal) {
		if (cur_action!=id_action) {
				cur_action=id_action;
				dims_showpopup('',350,event,'click','dims_popup',-180,-180,decal);
				dims_xmlhttprequest_todiv('index.php','actioncms=detail_action_planning&id_action='+id_action,'','dims_popup');
		}
	}

</script>
