<script language="JavaScript" type="text/JavaScript">
	function affiche_div(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}

	function modComment(idcmt, id_ct, from) {
		//alert("ici");
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=modcmtpers&id_cmt="+idcmt+"&id_ent="+id_ct+"&from="+from,"", 'dims_popup');
		dims_showpopup("popup", "450", "300", '',"dims_popup", 200, 100);
	}

	function modCommentbyAuthor(idcmt, id_ct, from) {
		//alert("ici");
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=modcmtpers_author&id_cmt="+idcmt+"&id_ent="+id_ct+"&from="+from,"", 'dims_popup');
		dims_showpopup("popup", "450", "300", '',"dims_popup", 200, 100);
	}
</script>
<?php

$cat = dims_load_securvalue('cat',dims_const::_DIMS_NUM_INPUT,true,true);

$tabscriptenv = "admin.php?cat=".$cat;

$part = dims_load_securvalue('part',dims_const::_DIMS_NUM_INPUT,true,true);
if(empty($part)) $part= _BUSINESS_TAB_CONTACT_IDENTITE;

$ent_id = dims_load_securvalue('id_ent',dims_const::_DIMS_NUM_INPUT,true,true);
if($ent_id == "") $ent_id = $_SESSION['business']['ent_id'];

$ent= new tiers();
if ($ent_id>0) {
	$ent->open($ent_id);
	$_SESSION['business']['ent_id']=$ent_id;
}


?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="width:25%;vertical-align:top;">
			<?
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_profil.php');
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_network.php');
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_docs.php');
			?>
		</td>
		<td align="center" style="vertical-align:top;padding-left:5px;">
			<table width="100%" cellpadding="0" cellspacing="2" >
				<tr>
					<td style="vertical-align:top;">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="100%" style="vertical-align:top;">
								<? require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_news_comments.php'); ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
