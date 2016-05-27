<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');

$content = '';
$add_g = '';

//gestion de l'enregistrement du formulaire
$valaddct = dims_load_securvalue('add_contact', dims_const::_DIMS_NUM_INPUT, false, true, true);
$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, false, true, true);
$cttoadd = dims_load_securvalue('cttoadd', dims_const::_DIMS_NUM_INPUT, false, true, true);
if($valaddct == 1) {
	$ctgrl = new tag_index();
	$ctgrl->init_description();
	$ctgrl->fields['id_record'] = $id_contact;
	$ctgrl->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
	$ctgrl->fields['id_tag'] = $cttoadd;
	$ctgrl->fields['id_module'] = 1;
	$ctgrl->fields['id_module_type'] = 1;
 //dims_print_r($ctgrl->fields);
	$ctgrl->save();;
}

//selection des groupe dont fait parti le contact
$sqlg = "SELECT			t.id,
						t.tag as label,
						t.private,
						t.id_user as id_user_create,
						t.id_workspace,
						l.id as id_link,
						l.id_record,
						l.id_tag
			FROM		dims_tag as t
			INNER JOIN	dims_tag_index as l
			ON			l.id_tag = t.id
			AND			l.id_module=1
			AND			l.id_tag = t.id
		AND				l.id_record = :idrecord
		AND				l.id_object= :idobject
		WHERE			t.type=1 and ((t.id_workspace = :idworkspace and private=0) OR (t.id_user = :iduser and private=1))";

$resg = $db->query($sqlg, array(
	':idrecord' 	=> $contact_id,
	':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT,
	':idworkspace' 	=> $_SESSION['dims']['workspaceid'],
	':iduser' 		=> $_SESSION['dims']['userid']
));

if($db->numrows($resg) > 0) {
	$content .= '<table width="100%">';
	while($tab_group = $db->fetchrow($resg)) {
		if(($tab_group['private'] == 1 && $tab_group['id_user_create'] == $_SESSION['dims']['userid']) || $tab_group['private'] == 0) {
			if($tab_group['private'] == 1) $content .= '<tr><td><img src="./common/img/user.png"/>&nbsp;'.$tab_group['label'].'</td></tr>';
			else $content .= '<tr><td><img src="./common/img/users.png"/>'.$tab_group['label'].'</td></tr>';
		}
	}
	//$content .= implode(',',$tabg);
	$content .= '</table>';
}
else {
	$content .= $_DIMS['cste']['_DIMS_LABEL_NO_GROUP_ATTACHED'];
}

//selection des groupes appartenant au user en vu d'un rattachement
$sqlu = "SELECT		t.tag as label, t.id
		FROM		dims_tag as t
		WHERE			t.type=1 and ((t.id_workspace = :idworkspace and private=0) OR (t.id_user = :iduser and private=1))";

$resu = $db->query($sqlu, array(
	':idworkspace' 	=> $_SESSION['dims']['workspaceid'],
	':iduser' 		=> $_SESSION['dims']['userid']
));
if($db->numrows($resu) > 0) {

	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("id_contact",	$contact_id);
	$token->field("add_contact","1");
	$token->field("cttoadd");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
	$add_g .=	'<form id="add_ct_g" name="add_ct_g" method="post" action="">
				<input type="hidden" name="id_contact" value="'.$contact_id.'">
				<input type="hidden" name="add_contact" value="1">
				<table width="100%">
					<tr>
						<td>'.$_DIMS['cste']['_DIMS_LABEL_ADD_CT_GROUP'].'</td>
						<td>
							<select id="cttoadd" name="cttoadd" style="width:150px;">
								<option value="">--</option>';
							while($tab_u = $db->fetchrow($resu)) {
								$add_g .= '<option value="'.$tab_u['id'].'">'.$tab_u['label'].'</option>';
							}
	$add_g .= '			</td>
					</tr>
					<tr>
						<td colspan="2">'.dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'./common/img/save.gif','document.add_ct_g.submit();').'</td>
					</tr>
				</table>
				</form>';
}

echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_CONTACT_GOUPS'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
?>
<table>
	<tr>
		<td>
			<?php echo $content; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $add_g; ?>
		</td>
	</tr>
</table>
<?php
echo $skin->close_simplebloc();
?>
