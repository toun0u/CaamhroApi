<script language="javascript">
	var timersearch;

	function upKeysearchLink(type) {
		clearTimeout(timersearch);
		timersearch = setTimeout("execSearchLink(\'"+type+"\')", 500);
	}

	function execSearchLink(type) {
		clearTimeout(timersearch);

		if(type == 'ent_pers') {
			var nomsearch = dims_getelem('search_pers').value;
			var divtoaffich = dims_getelem('dispres_searchp');

			if(nomsearch.length>=2) {
				dims_xmlhttprequest_todiv("admin.php", "op=search_linktoadd&action=<? echo _BUSINESS_TAB_CONTACTSTIERS;?>&search_name="+nomsearch+"&type_search="+type+"&id_ent=<? echo $ent_id; ?>", "", "dispres_searchp");
				divtoaffich.style.display = "block";
			}
		}
		else {
			var nomsearch = dims_getelem('search_tiers').value;
			var divtoaffich = dims_getelem('dispres_searcht');

			if(nomsearch.length>=2) {
				dims_xmlhttprequest_todiv("admin.php", "op=search_linktoadd&action=<? echo _BUSINESS_TAB_CONTACTSTIERS;?>&search_name="+nomsearch+"&type_search="+type+"&id_ent=<? echo $ent_id; ?>", "", "dispres_searcht");
				divtoaffich.style.display = "block";
			}
		}
	}

	function affiche_block_gen2() {
        var div_to_open = dims_getelem('affiche_link_gen');
        div_to_open.style.display="block";
		var div_to_close1 = dims_getelem('affiche_link_met');
		div_to_close1.style.display="none";
    }

	function affiche_block_met2() {
        var div_to_open = dims_getelem('affiche_link_met');
        div_to_open.style.display="block";
		var div_to_close1 = dims_getelem('affiche_link_gen');
		div_to_close1.style.display="none";
    }

</script>
<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ADDLINK'], "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkadd');", ""); ?>
<div id="lkadd" style="display:block;">
<table width="100%" cellpadding="0" cellspacing="3">
	<tr>
		<td width="33%">
			<table style="width:100%;" cellpadding="0" cellspacing="0">
				<tr onclick="javascript:affiche_block_gen2();document.getElementById('search_pers').focus();">
					<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
					<td class="midb20" style="font-size:13px;">
					<? echo $_DIMS['cste']['_DIMS_LABEL_LINK_PSEARCH']; ?>
					</td>
					<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
				</tr>
			</table>
		</td>
		<td width="33%">
		</td>
		<td width="33%">&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div id="affiche_link_gen" style="display:block;width:100%;">
				<form action="" method="post"></form>
				<? // truc dont je ne trouve pas d'autre solution ... ce formulaire vide doit être là pour que le formulaire reel fonctionne ... ?>
				<form name="form_inscript_link_pers" id="form_inscript_link_pers" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("op",				"save_link_intelligence");
					$token->field("action",			_BUSINESS_TAB_CONTACTSTIERS);
					$token->field("type",			"ent_pers");
					$token->field("search_pers");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<input type="hidden" name="op" value="save_link_intelligence"/>
				<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>"/>
				<? if(!empty($contact_id)) {  ?>
				<input type="hidden" name="id_pers_from" value="<? echo $contact_id ?>"/>
				<input type="hidden" name="id_object" value="<? echo dims_const::_SYSTEM_OBJECT_CONTACT ?>"/>
				<?
					$token->field("id_pers_from",	$contact_id);
					$token->field("id_object",		dims_const::_SYSTEM_OBJECT_CONTACT);
				?>
				<? } elseif(!empty($ent_id)) { ?>
				<input type="hidden" name="id_ent_from" value="<? echo $ent_id ?>"/>
				<input type="hidden" name="id_object" value="<? echo dims_const::_SYSTEM_OBJECT_TIERS ?>"/>
				<?
					$token->field("id_ent_from",	$ent_id);
					$token->field("id_object",		dims_const::_SYSTEM_OBJECT_TIERS);
				?>
				<? } ?>
				<input type="hidden" name="type" value="ent_pers"/>
					<table width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr>
							<td align="right" width="40%">
								<? echo $_DIMS['cste']['_DIMS_LABEL_SEARCH_LPERS']; ?>&nbsp;
							</td>
							<td align="left">
								<input type="text" value="" onkeyup="javascript:upKeysearchLink('ent_pers');" id="search_pers" name="search_pers"/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div id="dispres_searchp" style="display:none;width:100%">
								</div>
							</td>
						</tr>
					</table>
				</form>

			</div>

			<div id="affiche_link_met" style="display:none;width:100%;">
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<?
			echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/undo.gif',"javascript:affiche_div('zone_add');affiche_div('button_add');document.getElementById('dispres_searchp').innerHTML='';document.getElementById('dispres_searcht').innerHTML='';",'','');
			?>
		</td>
	</tr>
</table>
</div>
<? echo $skin->close_simplebloc(); ?>
