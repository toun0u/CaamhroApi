<script language="JavaScript" type="text/JavaScript">
	var timersearchimp;

	function SaveImport() {
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=import_ent","", 'import_ent');
	}

	function view_detail_imp(div) {
		var div_to_open = dims_getelem(div);
                if(div_to_open.style.display=="block") div_to_open.style.display='none';
		else div_to_open.style.display = 'block';
	}

	function upKeysearch_imp() {
		clearTimeout(timersearchimp);
		timersearchimp = setTimeout('execSearch_imp()', 800);
	}

	function execSearch_imp() {
		clearTimeout(timersearchimp);
		var nomsearch = dims_getelem('search_imp').value;
		var divtoaffich = dims_getelem('dispres_searchimp');
		if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT;?>&op=search_impcreator&imp_name="+nomsearch, "", "dispres_searchimp");
			divtoaffich.style.display = "block";
		}
	}

</script>

<div id="import_ent" style="width:100%;">
<form action="admin.php?cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>" method="Post" enctype="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "import_ent");
	$token->field("srcfileent");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="Hidden" name="op" value="import_ent">
<table cellpadding="2" cellspacing="1" align="center" width="100%">
	<tr>
		<td align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_IMPORTSRC']; ?>&nbsp;*:&nbsp;</td>
		<td align="left"><input class="text" type="File" name="srcfileent"></td>
	</tr>
	<tr>
		<td align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_PERS_CREATE_IMPPORT']; ?>&nbsp;*:&nbsp;</td>
		<td align="left">
			<input type="text" id="search_imp" value="" onkeyup="javascript:upKeysearch_imp();"/>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="dispres_searchimp" style="display:none;width:100%">
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
		<?
			echo dims_create_button($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'],"./common/img/go-down.png","javascript:SaveImport()","","");
		?>
		</td>
	</tr>
<?php
	$sql_exist_imp = "SELECT id FROM dims_mod_business_tiers_import";
	$res_exist = $db->query($sql_exist_imp);
	if($db->numrows($res_exist) > 0) {

?>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<input type="button" class="flatbutton" value="<? echo $_DIMS['cste']['_LABEL_LAST_IMPORT']; ?>" onclick="javascript:location.href='admin.php?cat=<? echo _BUSINESS_CAT_CONTACT ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=import_ent';" />
		</td>
		<td>
			<input type="button" class="flattbutton" value="<? echo $_DIMS['cste']['_LABEL_CLEAN_IMPORT_TABLE']; ?>" onclick="javascript:dims_confirmlink('admin.php?cat=<? echo _BUSINESS_CAT_CONTACT ?>&action=<? echo _BUSINESS_TAB_IMPORT; ?>&part=<? echo _BUSINESS_TAB_IMPORT; ?>&op=droptableimport_ent', 'Etes-vous sur de vouloir vider la table d\'import');"/>
		</td>
	</tr>
<?php
        }
?>
</table>
</form>
</div>
