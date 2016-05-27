<?php

/*if ($_SESSION['dims']['connected']) {
		// qq conseils :
		// - faut faire attention aux tests si vous etes connectes en backoffice,
		// vous l'etes aussi en frontoffice
		// - faire la != entre un contact et dims_user, la creation d'un compte dims_user doit se faire par l'admin
		//   au moment ou il valide l'etape 1, le mieux serait d'avoir une zone pour recherche eventuellement un contact existant
		//   (peut etre aussi faire une recherche sur tel, nom, prenom
		// - si une personne a deja un compte dims_user, on peut remonter la fiche contact et donc ses infos sur l'event
		echo "vous etes connecte sur un compte dims_user";
}
else {
		echo "non connecte";
}*/

/***
 * Gestion du Front' :
 * 	- Si ID d'action
 * 		-> Ouverture de l'event : Descript, Date ...
 * 		-> + #1
 * 	- Sinon
 * 		-> Affichage du dernier event
 * 		-> + #1
 *
 *	#1	{
 * 	- Formulaire (niv.1) sur event -> Inscription/Enregistrement -> Validation
 * 	- Si validé -> Lien (Includ id_action) -> Ouverture de l'event -> Connexion
 * 	- Si compte valide -> Formulaire (niv.2) -> Récapitulatif infos (*Niv.1) + Docs(Formulaires)
 * 		}
***/

/* Initialisation variables */
$erreur = false;
$id_action = 0;
$evt = new action();

/* Chargement id de l'event */
if(isset($_SESSION['dims']['events']['id_event']) && !empty($_SESSION['dims']['events']['id_event']))
    $id_action = $_SESSION['dims']['events']['id_event'];
else
    $id_action = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true, true);

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
// recuperation de la valeur d'adminevent
$is_dims_user=false;

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
                id = :idaction
            AND
                datejour > CURDATE() limit 1';
            /*AND
                datejour > CURDATE()
            LIMIT 1'; */

    $resource = $db->query($sql, array(':type' => dims_const::_PLANNING_ACTION_EVT, ':idaction' => $id_contact));
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
				// on va recuperer la liste des events rattaches
				$workspace_code=dims_load_securvalue('workspace_code', dims_const::_DIMS_CHAR_INPUT, true,true);
				$front=$dims->getWebWorkspaces();
				$id_workspace_verify=0;

				// verification de l'existence du code
				foreach($front as $id=>$worksp) {
					if ($worksp['code']==$workspace_code) {
						$id_workspace_verify=$worksp['id'];
					}
				}

				if ($id_workspace==0) {
					$back=$dims->getAdminWorkspaces();
					foreach($back as $id=>$worksp) {
						if ($worksp['code']==$workspace_code) {
							$id_workspace_verify=$worksp['id'];
						}
					}
				}
				if ($id_workspace_verify>0) {
					ob_end_clean();
					echo $_DIMS['cste']['_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'];
					ob_flush();
					die();
				}
				else {
					echo '<div id="descript_evt" style="font-size:16px;">';
					echo '<p >'.$_DIMS['cste']['_DIMS_TEXT_REGISTRATION_WAIT_EMAIL'].'</p>';
					echo '</div>';
				}
				break;
			case 'form_niv1':

				require_once(DIMS_APP_PATH.'modules/events/cms_event_form_niv1.php');
				break;
			case 'form_niv1_fair':
				require_once(DIMS_APP_PATH.'modules/events/cms_event_form_niv1_fair.php');
				break;
			case 'form_niv2':

				break;
			default:
				if($_SESSION['dims']['connected']) {

					//La personne est connecte,
					//Verifions qu'il y a bien une inscription _validee_ sur cet event pour elle
					//attention, tout user doit pouvoir modifier ses infos personnelles meme s'il a valide le niveau 1
					$sql = "SELECT id
							FROM dims_mod_business_event_inscription
							WHERE id_contact = :idcontact
							AND id_action = :idaction
							AND validate > 0";

					$res = $db->query($sql, array(':idcontact' => $_SESSION['dims']['user']['id_contact'], ':idaction' => $id_action));
					if($db->numrows($res) >0) {

						$idInscrip = $db->fetchrow($res);
						$idInscrip = $idInscrip['id'];

						if($evt->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') {
							require_once DIMS_APP_PATH.'modules/events/cms_event_form_niv2.php';
						}
						else {
							require_once DIMS_APP_PATH.'modules/events/cms_event_form_niv2_fair.php';
						}
					}
					else {
						require_once(DIMS_APP_PATH.'modules/events/cms_event_describe.php');
					}
				}
				else
				{
					require_once(DIMS_APP_PATH.'modules/events/cms_event_describe.php');
				}
				break;
		}
	}
	else
		echo $erreur;
}
elseif(! $_SESSION['dims']['connected']) {

	if (isset($_POST['dims_login'])) {
		echo '<table width="100%" style="clear: both;"><tr style="background-color: transparent;"><td width="100%" style="background-color: transparent;font-size:14px;" align="left"><div style="margin-left: 15px;">'.$_DIMS['cste']['_DIMS_FRONT_TEXT_NO_LOGGIN'].' </div></td></tr></table>';
	}
}
echo '</div>';
?>
