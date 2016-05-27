<?php

require_once DIMS_APP_PATH.'modules/system/class_action.php';
require_once DIMS_APP_PATH.'modules/system/class_inscription.php';

/*if ($_SESSION['dims']['connected']) {
		// qq conseils :
		// - faut faire attention aux tests si vous etes connectes en backoffice,
		// vous l'etes aussi en frontoffice
		// - faire la != entre un contact et dims_user, la creation d'un compte dims_user doit se faire par l'admin
		//	 au moment ou il valide l'etape 1, le mieux serait d'avoir une zone pour recherche eventuellement un contact existant
		//	 (peut etre aussi faire une recherche sur tel, nom, prenom
		// - si une personne a deja un compte dims_user, on peut remonter la fiche contact et donc ses infos sur l'event
		echo "vous etes connecte sur un compte dims_user";
}
else {
		echo "non connecte";
}*/

/***
 * Gestion du Front' :
 *	- Si ID d'action
 *		-> Ouverture de l'event : Descript, Date ...
 *		-> + #1
 *	- Sinon
 *		-> Affichage du dernier event
 *		-> + #1
 *
 *	#1	{
 *	- Formulaire (niv.1) sur event -> Inscription/Enregistrement -> Validation
 *	- Si validé -> Lien (Includ id_action) -> Ouverture de l'event -> Connexion
 *	- Si compte valide -> Formulaire (niv.2) -> Récapitulatif infos (*Niv.1) + Docs(Formulaires)
 *		}
***/

/* Initialisation variables */
$erreur = false;
$id_action = 0;
$evt = new action();

/* Chargement id de l'event */
if(isset($_SESSION['dims']['events']['id_event']) && !empty($_SESSION['dims']['events']['id_event']))
	$id_action = $_SESSION['dims']['events']['id_event'];
else
	$id_action = dims_load_securvalue('id_event', _DIMS_NUM_INPUT, true, true);

$action = dims_load_securvalue('action', _DIMS_CHAR_INPUT, true, true);

echo '<div id="event_content">';
/* Recherche de l'event */
if(!empty($id_action) && !is_null($id_action)) {
	//Recherche de l'event correspondant (Vérification si allow_fo est bien à 1, type=2)
	// AND allow_fo = 1
	$sql = 'SELECT
				id
			FROM
				dims_mod_business_action
			WHERE
				type = :type
			AND
				allow_fo = 1
			AND
				id = :idaction
			AND
				timestamp_release <= :timestamprelease
			/*AND
				datejour > CURDATE()*/
			LIMIT 1';

	$resource = $db->query($sql, array(
		':type' 			=> _PLANNING_ACTION_EVT,
		':idaction' 		=> $id_action,
		':timestamprelease' => date("Ymd000000")
	));
	if($db->numrows($resource) != 0)
	{
		//Ouverture de l'objet Action correspondant
		$result = $db->fetchrow($resource);
		$evt->open($result['id']);
	}
	else
		$erreur = '<div style="margin-left: 15px;">
				'.$_DIMS['cste']['_DIMS_FRONT_TEXT_NO_EVENT'].'
				</div>';


	if(isset($evt->numrows) && $evt->numrows == 1 && !$erreur) {
		switch($action) {
			case 'valid_niv1':
				/*echo '<table width="100%"><tr><td width="100%" style="font-size:14px;" align="center">
							'.$_DIMS['cste']['_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'].'.
					</td></tr>';
				echo '</table>';*/
				echo '<div id="descript_evt" style="font-size:16px;">';
				echo '<p >'.$_DIMS['cste']['_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'].'</p>';
				echo '</div>';
				break;
			case 'form_niv1':
				require_once(DIMS_APP_PATH.'modules/system/cms_event_form_niv1.php');
				break;
			case 'form_niv1_fair':
				require_once(DIMS_APP_PATH.'modules/system/cms_event_form_niv1_fair.php');
				break;
			default:
				if($_SESSION['dims']['connected']) {
					//La personne est connecté,
					//Vérifions qu'il y a bien une inscription _validée_ sur cet event pour elle
					//attention, tout user doit pouvoir modifier ses infos personnelles même s'il a validé le niveau 1
					$sql = "SELECT id
							FROM dims_mod_business_event_inscription
							WHERE id_contact = :idcontact
							AND id_action = :idaction
							AND validate > 0";

					$res = $db->query($sql, array(
						':idcontact' 	=> $_SESSION['dims']['user']['id_contact'],
						':idaction' 	=> $id_action
					));
					if($db->numrows($res) >0) {

						$idInscrip = $db->fetchrow($res);
						$idInscrip = $idInscrip['id'];

						if($evt->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS')
							require_once DIMS_APP_PATH.'modules/system/cms_event_form_niv2.php';
						else
							require_once DIMS_APP_PATH.'modules/system/cms_event_form_niv2_fair.php';
					}
					else {
						require_once(DIMS_APP_PATH.'modules/system/cms_event_describe.php');
					}
				}
				else
				{
					require_once(DIMS_APP_PATH.'modules/system/cms_event_describe.php');
				}
				break;
		}
	}
	else
		echo $erreur;
}
elseif($_SESSION['dims']['connected']) {
	//on ne selectionne que les events de niveau 2 dont les inscriptions ne sont pas closes
	// petite modif pour vérifier si la personne est admin d'events

	$sql_evt = "SELECT		a.*,
							ei.id_action,
							ei.validate
				FROM		dims_mod_business_event_inscription ei
				INNER JOIN	dims_mod_business_action a
				ON			a.id = ei.id_action
				INNER JOIN	dims_user u
				ON			u.id_contact = ei.id_contact
				AND			u.id = :iduser
				WHERE		a.close != 1
				AND			a.niveau = 2
				ORDER BY	ei.validate DESC";

	$res_evt = $db->query($sql_evt, array(
		':iduser' => $_SESSION['dims']['userid']
	));

	if($db->numrows($res_evt) > 0) {

		echo '<div id="list_evt">';
		echo '<p style="font-size:13px;">'.$_DIMS['cste']['_DIMS_FO_EVT_LIST'].'</p>';
		echo '<table>';
		while($tab_evt = $db->fetchrow($res_evt)) {
			echo '<tr>
						<td>';
			if($tab_evt['validate'] > 0)
				echo '<a href="index.php?id_event='.$tab_evt['id_action'].'">'.$tab_evt['libelle'].'</a>';
			else
				echo $tab_evt['libelle'];
			echo '		</td>
						<td>';
			if($tab_evt['close'] == 1) {
				echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'];
			}
			else {
				switch($tab_evt['validate']) {
					case 0:
							echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'];
						break;
					case 1:
							echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CURRENT'];
						break;
					case 2:
							echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'];
						break;
				}
			}
			echo '		</td>
						<td>';
			if($tab_evt['validate'] > 0)
				echo '<a href="index.php?id_event='.$tab_evt['id_action'].'" style="font-weight:bold;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;font-style:italic;font-size:14px;">'.$_DIMS['cste']['_DIMS_LABEL_GO'].'></a>';
			echo '		</td>
					</tr>';
		}
		echo '</table>';
		echo '</div>';

		//gestion des informations personnelles

		require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
		$ct = new contact();
		$ct->open($_SESSION['dims']['user']['id_contact']);
?>
	<script language="javascript">
	function verif_form(form_id) {
		if(dims_validatefield('<?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>',document.getElementById("ct_lastname"), 'string') &&
			dims_validatefield('<?php echo $_DIMS['cste']['_FIRSTNAME']; ?>',document.getElementById("ct_firstname"), 'string')) {
			document.getElementById(form_id).submit();
		}
		else {
			return false;
		}
	}
	</script>
	<div id="form_1">
		<p style="font-size:13px;"><?php echo $_DIMS['cste']['_DIMS_LABEL_PERSONNAL_INFOS']; ?></p>

		<form action="<?php echo dims_urlencode('index.php?action=modif_ct',true); ?>" method="POST" id="pers_data" name="pers_data">
		<input type="hidden" name="id_ct" value="<?php echo $_SESSION['dims']['user']['id_contact']; ?>"/>
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("id_ct",	$_SESSION['dims']['user']['id_contact']);
			$token->field("ct_email");
			$token->field("ct_phone");
			$token->field("ct_mobile");
			$token->field("ct_address");
			$token->field("ct_city");
			$token->field("ct_postalcode");
			$token->field("ct_country");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<div class="inscriptions">
			<div class="info_oblig">
			<?php
			echo '
				<table width="100%" cellpadding="1" cellspacing="8" border="0">
					<tr>
						<td align="left" width="20%">
							<label for="ct_lastname">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left" style="font-size:13px;">
							'.strtoupper($ct->fields['lastname']).'
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_firstname">'.$_DIMS['cste']['_FIRSTNAME'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left" style="font-size:13px;">
							'.$ct->fields['firstname'].'
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_email">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_email" id="ct_email" value="'.$ct->fields['email'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_phone">'.$_DIMS['cste']['_PHONE'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_phone" id="ct_phone" value="'.$ct->fields['phone'].'" class="content"/>
						</td>
					</tr>
				</table>
			</div>
			<div class="info_compl">
				<table>
					<tr>
						<td align="left">
							<label for="ct_mobile">'.$_DIMS['cste']['_MOBILE'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_mobile" id="ct_mobile" value="'.$ct->fields['mobile'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_address">'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_address" id="ct_address" value="'.$ct->fields['address'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_city">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_city" id="ct_city" value="'.$ct->fields['city'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_postalcode">'.$_DIMS['cste']['_DIMS_LABEL_CP'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_postalcode" id="ct_postalcode" value="'.$ct->fields['postalcode'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_country">'.$_DIMS['cste']['_DIMS_LABEL_COUNTRY'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_country" id="ct_country" value="'.$ct->fields['country'].'" class="content"/>
						</td>
					</tr>
				</table>
				</form>
				<div class="save">
					<input type="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'].' >" class="submit" />
				</div>
			</div>';
	}
	else {
		echo '<table width="100%"><tr><td width="100%" style="font-size:14px;" align="center">
				<div style="margin-left: 15px;">
				'.$_DIMS['cste']['_DIMS_FRONT_TEXT_NO_EVENT'].'
				</div>
				</td></tr></table>';
	}
}
else {
	if (isset($_POST['dims_login']))
		echo '<table width="100%" style="clear: both;"><tr style="background-color: transparent;"><td width="100%" style="background-color: transparent;font-size:14px;" align="left"><div style="margin-left: 15px;">'.$_DIMS['cste']['_DIMS_FRONT_TEXT_NO_LOGGIN'].' </div></td></tr></table>';
	else {
		echo '<table width="100%" style="clear: both;"><tr style="background-color: transparent;"><td width="100%" style="background-color: transparent;font-size:14px;" align="left"><div style="margin-left: 15px;">'.$_DIMS['cste']['_DIMS_FRONT_TEXT_LOGGIN'].' </div></td></tr></table>';
	}
}
echo '</div>';
?>
