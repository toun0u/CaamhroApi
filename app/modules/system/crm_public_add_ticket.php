<?php
$id_record = dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true, true);
$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true);
$rubcour = dims_load_securvalue('rubcour', dims_const::_DIMS_NUM_INPUT, true, true);


switch($type) {
	case 1:
		//demande d'informations concernant une personne
		echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_DMD_ENVOI'],'font-weight:bold;width:100%','','');
		//echo $id_record." ".$type." ".$rubcour;
		require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
		$ctcur = new contact();
		$ctcur->open($id_record);

//		$is_enable = array();
//		foreach($_SESSION['contact']['current_view'] as $id_cat => $tab_cat) {
//			//dims_print_r($tab_cat);
//			$is_enable[$id_cat] = 0;
//			if(!empty($tab_cat['list'])) {
//				foreach($tab_cat['list'] as $id_field => $tab_field) {
//					if($tab_field['use'] == 0 && (!empty($tab_field['enabled']))) {
//						$is_enable[$id_cat]++;
//					}
//				}
//			}
//		}
//		dims_print_r($is_enable);

		//recherche du / des destinataires
		$tab_dest = '';
		$tab_id_dest = array();
		foreach($_SESSION['contact']['current_last_modify'] as $id_workspace => $tab_lastmod) {

			if(!isset($tab_id_dest[$tab_lastmod['id_user']])) {
				$tab_id_dest[$tab_lastmod['id_user']] = $tab_lastmod['id_user'];
				$dest = new user();
				$dest->open($tab_lastmod['id_user']);
				$tab_dest .= $dest->fields['firstname']." ".$dest->fields['lastname']."; ";
			}
		}

		echo '<form method="POST" action="">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",			"send_tickets");
		$token->field("id_record",	"$id_record");
		$token->field("action",		_BUSINESS_TAB_CONTACTSSEEK);
		$token->field("type",		$type);
		$token->field("ticket_sujet");
		$token->field("ticket_type_inf3");
		$token->field("ticket_type_inf1");
		$token->field("ticket_type_inf2");
		$token->field("ticket_message");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo '	<input type="hidden" name="op" value="send_tickets"/>
				<input type="hidden" name="id_record" value="'.$id_record.'"/>
				<input type="hidden" name="action" value="'._BUSINESS_TAB_CONTACTSSEEK.'">
				<input type="hidden" name="type" value="'.$type.'"/>

				<table width="100%" cellpadding="0" cellespacing="0" border="0" style="background-color:#FFFFFF;padding-top:5px;">
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_SUBJECT'].' : </td>
						<td align="left">
							<input type="text" value="'.$_DIMS['cste']['_DIMS_LABEL_DMD_INFO'].' '.$ctcur->fields['firstname'].' '.$ctcur->fields['lastname'].'" name="ticket_sujet" id="ticket_sujet" style="width:230px;"/>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_INFO_TYPE'].' : </td>
						<td align="left">
							<table width="100%">
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="Identit&eacute;" name="ticket_type_inf3" id="ticket_type_inf3"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_PERS_IDENTITY'])).'</td>
								</tr>
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="Coordonn&eacute;es" name="ticket_type_inf1" id="ticket_type_inf1"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_PERS_COORD'])).'</td>
								</tr>
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="Informations" name="ticket_type_inf2" id="ticket_type_inf2"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_PERS_INFOS'])).'</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_DEST'].' : </td>
						<td align="left">
							'.$tab_dest.'
						</td>
					</tr>

					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_MESSAGE'].' : </td>
						<td align="left">
							<textarea cols="35" rows="5" id="ticket_message" name="ticket_message"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right" style="padding:10px;">
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>
						</td>
					</tr>
				</table>
				</form>';

		echo $skin->close_widgetbloc();
		break;
	case 2:
		//demande d'informations concernant une entreprise
		//demande d'informations concernant une personne
		echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_DMD_ENVOI'],'font-weight:bold;width:100%','','');
		//echo $id_record." ".$type." ".$rubcour;
		require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
		$entcur = new tiers();
		$entcur->open($id_record);

		$is_enable = array();
		foreach($_SESSION['ent']['current_view'] as $id_cat => $tab_cat) {
			$is_enable[$id_cat] = 0;
			if(!empty($tab_cat['list'])) {
				foreach($tab_cat['list'] as $id_field => $tab_field) {
					if($tab_field['use'] == 0 && (!empty($tab_field['enabled']))) {
						$is_enable[$id_cat]++;
					}
				}
			}
		}
		//dims_print_r($is_enable);

		//recherche du / des destinataires
		$tab_dest = '';
		$tab_id_dest = array();
		foreach($_SESSION['ent']['current_last_modify'] as $id_workspace => $tab_lastmod) {

			if(!isset($tab_id_dest[$tab_lastmod['id_user']])) {
				$tab_id_dest[$tab_lastmod['id_user']] = $tab_lastmod['id_user'];
				$dest = new user();
				$dest->open($tab_lastmod['id_user']);
				$tab_dest .= $dest->fields['firstname']." ".$dest->fields['lastname']."; ";
			}
		}

		echo '<form method="POST" action="">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",			"send_tickets");
		$token->field("id_record",	$id_record);
		$token->field("action",		_BUSINESS_TAB_CONTACTSSEEK);
		$token->field("type",		$type);
		$token->field("ticket_sujet");
		$token->field("ticket_type_inf3");
		$token->field("ticket_type_inf1");
		$token->field("ticket_type_inf2");
		$token->field("ticket_message");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo '	<input type="hidden" name="op" value="send_tickets"/>
				<input type="hidden" name="id_record" value="'.$id_record.'"/>
				<input type="hidden" name="action" value="'._BUSINESS_TAB_CONTACTSSEEK.'">
				<input type="hidden" name="type" value="'.$type.'"/>

				<table width="100%" cellpadding="0" cellespacing="0" border="0" style="background-color:#FFFFFF;padding-top:5px;">
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_SUBJECT'].' : </td>
						<td align="left">
							<input type="text" value="'.$_DIMS['cste']['_DIMS_LABEL_DMD_INFO'].' '.$entcur->fields['intitule'].'" name="ticket_sujet" id="ticket_sujet" style="width:230px;"/>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_INFO_TYPE'].' : </td>
						<td align="left">
							<table width="100%">
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="Identit&eacute;" name="ticket_type_inf3" id="ticket_type_inf3"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_PERS_IDENTITY'])).'</td>
								</tr>
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="Coordonn&eacute;es" name="ticket_type_inf1" id="ticket_type_inf1"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_PERS_COORD'])).'</td>
								</tr>
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="Informations" name="ticket_type_inf2" id="ticket_type_inf2"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_PERS_INFOS'])).'</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_DEST'].' : </td>
						<td align="left">
							'.$tab_dest.'
						</td>
					</tr>

					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_MESSAGE'].' : </td>
						<td align="left">
							<textarea cols="35" rows="5" id="ticket_message" name="ticket_message"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right" style="padding:10px;">
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>
						</td>
					</tr>
				</table>
				</form>';

		echo $skin->close_widgetbloc();
		break;
	case 3 :
		//demande d'informations concernant une personne
		echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_DMD_ENVOI'],'font-weight:bold;width:100%','','');
		//echo $id_record." ".$type." ".$rubcour;
		require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
		$ctcur = new contact();
		$ctcur->open($id_record);

		//recherche du / des destinataires
		$tab_dest = '';
		$tab_id_dest = array();
		foreach($_SESSION['contact']['current_last_modify'] as $id_workspace => $tab_lastmod) {

			if(!isset($tab_id_dest[$tab_lastmod['id_user']])) {
				$tab_id_dest[$tab_lastmod['id_user']] = $tab_lastmod['id_user'];
				$dest = new user();
				$dest->open($tab_lastmod['id_user']);
				$tab_dest .= $dest->fields['firstname']." ".$dest->fields['lastname']."; ";
			}
		}

		echo '<form method="POST" action="">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",			"send_tickets");
		$token->field("id_record",	$id_record);
		$token->field("action",		_BUSINESS_TAB_CONTACTSSEEK);
		$token->field("type",		$type);
		$token->field("ticket_sujet");
		$token->field("ticket_type_inf1");
		$token->field("ticket_message");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo '	<input type="hidden" name="op" value="send_tickets"/>
				<input type="hidden" name="id_record" value="'.$id_record.'"/>
				<input type="hidden" name="action" value="'._BUSINESS_TAB_CONTACTSSEEK.'">
				<input type="hidden" name="type" value="'.$type.'"/>


				<table width="100%" cellpadding="0" cellespacing="0" border="0" style="background-color:#FFFFFF;padding-top:5px;">
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_SUBJECT'].' : </td>
						<td align="left">
							<input type="text" value="'.$_DIMS['cste']['_DIMS_LABEL_DMD_INFO'].' '.$ctcur->fields['firstname'].' '.$ctcur->fields['lastname'].'" name="ticket_sujet" id="ticket_sujet" style="width:230px;"/>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_INFO_TYPE'].' : </td>
						<td align="left">
							<table width="100%">
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="'.$_DIMS['cste']['_INTEL_PLINK'].'" name="ticket_type_inf1" id="ticket_type_inf1"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_INTEL_PLINK'])).'</td>
								</tr>
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="'.$_DIMS['cste']['_DIMS_LABEL_INTEL_PERS_ENTLINK'].'" name="ticket_type_inf1" id="ticket_type_inf1"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_INTEL_PERS_ENTLINK'])).'</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_DEST'].' : </td>
						<td align="left">
							'.$tab_dest.'
						</td>
					</tr>

					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_MESSAGE'].' : </td>
						<td align="left">
							<textarea cols="35" rows="5" id="ticket_message" name="ticket_message"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right" style="padding:10px;">
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>
						</td>
					</tr>
				</table>
				</form>';

		echo $skin->close_widgetbloc();
		break;
	case 4:
		//demande d'informations concernant une entreprise
		//demande d'informations concernant une personne
		echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_DMD_ENVOI'],'font-weight:bold;width:100%','','');
		//echo $id_record." ".$type." ".$rubcour;
		require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
		$entcur = new tiers();
		$entcur->open($id_record);
		//dims_print_r($is_enable);

		//recherche du / des destinataires
		$tab_dest = '';
		$tab_id_dest = array();
		foreach($_SESSION['ent']['current_last_modify'] as $id_workspace => $tab_lastmod) {

			if(!isset($tab_id_dest[$tab_lastmod['id_user']])) {
				$tab_id_dest[$tab_lastmod['id_user']] = $tab_lastmod['id_user'];
				$dest = new user();
				$dest->open($tab_lastmod['id_user']);
				$tab_dest .= $dest->fields['firstname']." ".$dest->fields['lastname']."; ";
			}
		}

		echo '<form method="POST" action="">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",			"send_tickets");
		$token->field("id_record",	$id_record);
		$token->field("action",		_BUSINESS_TAB_CONTACTSSEEK);
		$token->field("type",		$type);
		$token->field("ticket_sujet");
		$token->field("ticket_type_inf1");
		$token->field("ticket_message");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		echo '	<input type="hidden" name="op" value="send_tickets"/>
				<input type="hidden" name="id_record" value="'.$id_record.'"/>
				<input type="hidden" name="action" value="'._BUSINESS_TAB_CONTACTSSEEK.'">
				<input type="hidden" name="type" value="'.$type.'"/>

				<table width="100%" cellpadding="0" cellespacing="0" border="0" style="background-color:#FFFFFF;padding-top:5px;">
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_SUBJECT'].' : </td>
						<td align="left">
							<input type="text" value="'.$_DIMS['cste']['_DIMS_LABEL_DMD_INFO'].' '.$entcur->fields['intitule'].'" name="ticket_sujet" id="ticket_sujet" style="width:230px;"/>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_INFO_TYPE'].' : </td>
						<td align="left">
							<table width="100%">
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="'.$_DIMS['cste']['_INTEL_PLINK'].'" name="ticket_type_inf1" id="ticket_type_inf1"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_INTEL_PLINK'])).'</td>
								</tr>
								<tr>
									<td width="6%" align="right"><input type="checkbox" value="'.$_DIMS['cste']['_INTEL_ENTLINK'].'" name="ticket_type_inf1" id="ticket_type_inf1"/></td>
									<td align="left">'.ucfirst(strtolower($_DIMS['cste']['_INTEL_ENTLINK'])).'</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_DEST'].' : </td>
						<td align="left">
							'.$tab_dest.'
						</td>
					</tr>

					<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_MESSAGE'].' : </td>
						<td align="left">
							<textarea cols="35" rows="5" id="ticket_message" name="ticket_message"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right" style="padding:10px;">
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>
						</td>
					</tr>
				</table>
				</form>';

		echo $skin->close_widgetbloc();
		break;
}
?>
