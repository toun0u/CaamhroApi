<script language="JavaScript" type="text/JavaScript">
	function show_com(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else div_tochange.style.display = 'block';
	}
</script>
<?
	//echo $skin->open_simplebloc(_DIMS_LABEL_TOOLBARNEWS.' : ','100%');
	if(!empty($ent_id))		{
		$ct = $ent_id;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_NEWS."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct;

		//recherche du dernier commentaire generique
		$sql_g = "	SELECT		*
					FROM		dims_mod_business_commentaire
					WHERE		id_contact = :ct
					AND			com_level = 1
					AND			id_object = :idobject
					ORDER BY	date_create DESC
					LIMIT 0,1";
		$res_g = $db->query($sql_g, array(
			':ct'		=> $ct,
			':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
		));
		$tab_comg = mysql_fetch_assoc($res_g);
		if(!empty($tab_comg)) {
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
			':ct'			=> $ct,
			':idworkspace'	=> $_SESSION['dims']['workspaceid'],
			':idobject' 	=> dims_const::_SYSTEM_OBJECT_TIERS
		));
		$tab_comm = mysql_fetch_assoc($res_m);
		if(!empty($tab_comm)) {
			$ct_create_m = new contact();
			$ct_create_m->open($tab_comm['id_user_ct']);
		}

	}

//dims_print_r($tab_comm);
	echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_COMMENTS'], 'width:100%;', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_comment.png','26px', '26px', '-17px', '-5px', $url, '', '');
?>
    <table width="100%" cellpadding="1" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
        <tbody>
            <tr class="trl1">
                <td style="width: 5%;"><? echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?></td>
                <td style="width: 50%;"><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?></td>
                <td style="width: 30%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
				<td></td>
            </tr>
			<? if(!empty($tab_comg)) { ?>
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
					echo '<tr class="trl2"><td><img src="./common/img/workspace.png"/></td><td colspan="3">'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'</td></tr>';
				}
				if(!empty($tab_comm)) {
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
					echo '<tr class="trl1"><td><img src="./common/img/users.png"/></td><td colspan="3">'.$_DIMS['cste']['_DIMS_LABEL_NO_COMMENT'].'</td></tr>';
				}
			?>
        </tbody>
    </table>
<? echo $skin->close_widgetbloc(); ?>
