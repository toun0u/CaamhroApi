<script language="JavaScript" type="text/JavaScript">
	function show_com(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}
	function affiche_div(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}

	function modComment(idcmt, id_ct, from) {
		//alert("ici");
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=modcmtpers&id_cmt="+idcmt+"&id_cont="+id_ct+"&from="+from,"", 'dims_popup');
		dims_showpopup("popup", "450", "300", '',"dims_popup", 200, 100);
	}

	function modCommentbyAuthor(idcmt, id_ct, from) {
		//alert("ici");
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=modcmtpers_author&id_cmt="+idcmt+"&id_cont="+id_ct+"&from="+from,"", 'dims_popup');
		dims_showpopup("popup", "450", "300", '',"dims_popup", 200, 100);
	}

	function supr_comm(id_comm) {
		dims_xmlhttprequest('admin.php',"dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=supprcomm&id_cmt="+id_comm);
		location.reload();
	}
</script>
<?
	$url='';
	//echo $skin->open_simplebloc(_DIMS_LABEL_TOOLBARNEWS.' : ','100%');
	if(!empty($ent_id))		{
		$ct = $ent_id;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_NEWS."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct;
	}
	if(!empty($contact_id)) {
		$ct = $contact_id;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_NEWS."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct;

		//recherche du dernier commentaire generique
		$sql_g = "	SELECT		*
					FROM		dims_mod_business_commentaire
					WHERE		id_contact = $ct
					AND			com_level = 1
					AND			id_object = :idobject
					ORDER BY	date_create DESC
					LIMIT 0,1";
		$res_g = $db->query($sql_g, array(
			':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
		));
		if ($tab_comg = $db->fetchrow($res_g)) {
			$ct_create_g = new contact();
			$ct_create_g->open($tab_comg['id_user_ct']);
		}

		//recherche du dernier commentaire metier
		$sql_m = "	SELECT		*
					FROM		dims_mod_business_commentaire
					WHERE		id_contact = :ct
					AND			com_level = 2
					AND			id_workspace = :idworkspace
					AND			id_object = :idobject
					ORDER BY	date_create DESC
					LIMIT 0,1";

		$res_m = $db->query($sql_m, array(
			':ct' => $ct,
			':idworkspace' => $_SESSION['dims']['workspaceid'],
			':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
		));
		if ($tab_comm = $db->fetchrow($res_m)) {
			$ct_create_m = new contact();
			$ct_create_m->open($tab_comm['id_user_ct']);
		}
		else {

		}
	}

//dims_print_r($tab_comm);
	echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_COMMENTS'], 'width:100%;', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_comment.png','26px', '26px', '-17px', '-5px', $url, '', '');

	?>
    <table width="100%" cellpadding="1" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
        <tbody>
		<tr class="trl1">
			<td colspan="4" style="text-align:right">
				<a href="javascript:void(0);" onclick="javascript:modComment('<? if(isset($tab_com['metier']['current']['id'])) echo $tab_com['metier']['current']['id']; else echo ''; ?>', '<? echo $contact->fields['id']; ?>', '<? if(empty($tab_com['metier']['current']['id'])) echo '2'; else echo ''; ?>');">
					<img src="./common/img/add.gif" title="<?php echo $_DIMS['cste']['_DIMS_COMMENTS']?>"/><?php echo $_DIMS['cste']['_DIMS_COMMENTS']?>
				</a>
			</td>
		</tr>
            <tr class="trl1">
                <td style="width: 5%;">Niv</td>
                <td style="width: 50%;"><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?></td>
                <td style="width: 30%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
				<td></td>
            </tr>
			<? if(isset($ct_create_g->fields['lastname']) && isset($ct_create_g->fields['firstname'])) { ?>
            <tr class="trl2">
                <td><img src="./common/img/workspace.png"/></td>
                <td><? echo substr($tab_comg['commentaire'], 0, 30) ?>...</td>
                <td>
					<a href="admin.php?cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACT_NEWS; ?>&part=<? echo _BUSINESS_TAB_CONTACT_IDENTITE; ?>&contact_id=<? echo $tab_comg['id_user_ct']; ?>">
						<? echo $ct_create_g->fields['firstname'].' '.$ct_create_g->fields['lastname'] ?>
					</a>
				</td>
				<td><a href="javascript:void(0);" onclick="javascript:show_com('comg');"><img src="./common/img/view.png"/></a></td>
            </tr>
			<tr class="trl2">
				<td colspan="4">
					<div id="comg" style="display:none;width:100%;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<? echo $tab_comg['commentaire']; ?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<?	}
				else {
					echo '<tr class="trl2"><td>'.substr($_DIMS['cste']['_DIMS_LABEL_LFB_GEN'],0,3).'.</td><td colspan="3">'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'</td></tr>';
				}
				if(isset($ct_create_m->fields['lastname']) && isset($ct_create_m->fields['firstname'])) {
			?>
			<tr class="trl1">
                <td><img src="./common/img/users.png"/></td>
                <td><? echo substr($tab_comm['commentaire'], 0, 30) ?>...</td>
                <td>
					<a href="admin.php?cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACT_NEWS; ?>&part=<? echo _BUSINESS_TAB_CONTACT_IDENTITE; ?>&contact_id=<? echo $tab_comm['id_user_ct']; ?>">
						<? echo $ct_create_m->fields['firstname'].' '.$ct_create_m->fields['lastname'] ?>
					</a>
				</td>
				<td><a href="javascript:void(0);" onclick="javascript:show_com('comm');"><img src="./common/img/view.png"/></a></td>
            </tr>
			<tr class="trl1">
				<td colspan="4">
					<div id="comm" style="display:none;width:100%;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<? echo $tab_comm['commentaire']; ?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<?	}
				else {
					echo '<tr class="trl1"><td>'.$_DIMS['cste']['_DIMS_LABEL_COM_MET'].'.</td><td colspan="3">'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'</td></tr>';
				}
			?>
        </tbody>
    </table>
<? echo $skin->close_widgetbloc(); ?>
