<?php
class cata_context {

	const _EMPTY = -1;
	const _OK = 0;
	const _ERROR_NO_GROUP = 1;
	const _ERROR_ACCOUNT_DISABLED = 2;

	// CHARGEMENT DE L'ENVIRONNEMENT DE L'UTILISATEUR
	public static function loadUserEnvironment($oCatalogue) {
        require_once DIMS_APP_PATH."modules/catalogue/include/class_const.php";
        require_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";
        require_once DIMS_APP_PATH."modules/catalogue/include/class_config.php";
        require_once DIMS_APP_PATH."modules/catalogue/include/class_param.php";

		$a_return = array('response' => self::_EMPTY);

		$db = dims::getInstance()->getDb();

        $user = new user();
        $user->open($_SESSION['dims']['userid']);
        $a_return['user'] = $user;

        $b2c = $oCatalogue->getParams('cata_mode_B2C');

        // Désactivation du "mode light" - Ben - 15/10/2014
        // Quel intéret de supprimer la possibilité d'avoir plusieurs utilisateurs sur un compte client ?
        // Il faudrait au moins tester si le client est un pro ou un particulier non ?

        // if( ! $b2c)
        if (true) {
    		$group = new group();
    		$groups = $user->getgroups(true);

    		// l'id_group est force pour l'admin car il a un compte multi-clients
    		if (isset($_SESSION['catalogue']['selected_groupid'])) {
    			$group->open($_SESSION['catalogue']['selected_groupid']);
    		}
    		else {
    			$group->open(key($groups));
    		}
            $a_return['group'] = $group;

            //recherche des groupes enfants
            $lst_group = implode(',', array_merge($groups, $group->getgroupchildrenlite()));

            // on verifie que l'utilisateur est rattaché a un groupe
            if ($lst_group == '') {
            	$a_return['response'] = self::_ERROR_NO_GROUP;
            	return $a_return;
            }

            // adminlevel
            $_SESSION['session_adminlevel'] = $user->getgroupadminlevel($groups);

            // recuperation du client
            $sql = '
                SELECT  c.id_client,
                        c.code_client,
                        c.id_company,
                        c.dims_group AS id_group,
                        c.bloque,
                        c.limite_budget,
                        c.budget_reconduction,
                        c.budget_date_reconduction,
                        c.type,
                        c.code_tarif_1,
                        c.code_tarif_2,
                        c.code_market,
                        c.escompte,
                        c.enr_certiphyto,
                        gu.adminlevel,
                        g.parents,
                        wg.id_group AS groupe_racine,
                        cc.tarif,
                        cc.ccat,
                        cc.remgen,
                        cc.rem0,
                        cc.remgen,
                        cc.rem1,
                        cc.seuilrem1,
                        cc.pourcrem1,
                        cc.rem2,
                        cc.seuilrem2,
                        cc.pourcrem2,
                        cc.remcum,
                        cc.tarspe

                FROM    dims_mod_cata_client c

                INNER JOIN  dims_group_user gu
                ON          gu.id_user = '.$_SESSION['dims']['userid'].'
                AND         gu.id_group = c.dims_group

                INNER JOIN  dims_group g
                ON          g.id = gu.id_group

                INNER JOIN  dims_workspace_group wg
                ON          wg.id_workspace = '.$_SESSION['dims']['workspaceid'].'

                LEFT JOIN   dims_mod_cata_client_cplmt cc
                ON          cc.id_client = c.id_client

                WHERE   c.dims_group IN ('.$lst_group.')
                AND     c.code_client != \'INTERNET\'

                GROUP BY c.id_client';
            $rs = $db->query($sql);
            if ($db->numrows($rs)) {
                $_SESSION['catalogue'] = array();
                $_SESSION['catalogue']['groupid'] = $group->fields['id'];

                if ($db->numrows($rs) > 1) {
                    // Si il s'agit d'un compte qui a plrs comptes client, on en prend que 1 (le dernier),
                    // et conserve la liste complete des codes client en session
                    $_SESSION['catalogue']['liste_clients'] = array();
                    while ($row = $db->fetchrow()) {
                    	if ( in_array($row['groupe_racine'], explode(';', $row['parents'])) && !in_array($row['code_client'], $_SESSION['catalogue']['liste_clients']) ) {
                    		$_SESSION['catalogue']['liste_clients'][] = $row['code_client'];
                    	}
                    }
                    mysql_data_seek($rs, 0);
                }

                $row = $db->fetchrow($rs);

                $limite_budget = $row['limite_budget'];
                $budget_reconduction = $row['budget_reconduction'];
                $budget_date_reconduction = $row['budget_date_reconduction'];

                // Si le compte est bloqué, on empêche l'utilisateur de se logger
                if($row['bloque']) {
    				$a_return['response'] = self::_ERROR_ACCOUNT_DISABLED;
    				return $a_return;
                }

                $_SESSION['catalogue']['code_client']           = $row['code_client'];
                $_SESSION['catalogue']['client_id']             = $row['id_client'];
                $_SESSION['catalogue']['id_company']            = $row['id_company'];
                $_SESSION['catalogue']['clientpro']             = ($row['type'] == client::TYPE_PROFESSIONAL) ? true : false;
                $_SESSION['catalogue']['tarif']                 = $row['tarif'];
                $_SESSION['catalogue']['ccat']                  = $row['ccat'];
                $_SESSION['catalogue']['code_tarif_1']          = $row['code_tarif_1'];
                $_SESSION['catalogue']['code_tarif_2']          = $row['code_tarif_2'];
                $_SESSION['catalogue']['escompte']              = $row['escompte'];
                $_SESSION['catalogue']['enr_certiphyto']        = $row['enr_certiphyto'];

                $_SESSION['catalogue']['remcum']                = (strtolower($row['remcum']) == "oui") ? true : false;
                $_SESSION['catalogue']['rem0']                  = $row['rem0'];
                $_SESSION['catalogue']['remgen']                = $row['remgen'];
                $_SESSION['catalogue']['rem1']                  = $row['rem1'];
                $_SESSION['catalogue']['seuilrem1']             = $row['seuilrem1'];
                $_SESSION['catalogue']['pourcrem1']             = $row['pourcrem1'];
                $_SESSION['catalogue']['rem2']                  = $row['rem2'];
                $_SESSION['catalogue']['seuilrem2']             = $row['seuilrem2'];
                $_SESSION['catalogue']['pourcrem2']             = $row['pourcrem2'];

                // Recherche d'un marché en cours
                $market = cata_market::getByCode($row['code_market']);
                if ($market !== null) {
                    $_SESSION['catalogue']['market'] = $market->fields;
                }

                // le code_client est force pour l'admin car il a un compte multi-clients
                if (isset($_SESSION['catalogue']['selected_cref'])) {
                    $_SESSION['catalogue']['code_client'] = $_SESSION['catalogue']['selected_cref'];
                }

                $_SESSION['catalogue']['root_group'] = $row['id_group'];

                $client = new client();
                $client->open($row['id_client']);
                $a_return['client'] = $client;

                if($client->fields['cata_restreint']) $_SESSION['catalogue']['familys'] = array();

                $_SESSION['catalogue']['CNOM'] = $client->fields['nom'];
                $_SESSION['catalogue']['tar'] = $client->fields['code_tarif_1'];

                $_SESSION['catalogue']['afficher_prix']         = ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP || $client->fields['afficher_prix']);
                $_SESSION['catalogue']['cata_restreint']        = $client->fields['cata_restreint'];
                $_SESSION['catalogue']['budget_non_bloquant']   = $client->fields['budget_non_bloquant'];
                $_SESSION['catalogue']['budget_reconduction']   = $client->fields['budget_reconduction'];
                $_SESSION['catalogue']['limite_budget']         = $client->fields['limite_budget'];
                $_SESSION['catalogue']['change_livraison']      = $client->fields['change_livraison'];
                $_SESSION['catalogue']['hors_catalogue']        = $client->fields['hors_catalogue'];
                $_SESSION['catalogue']['utiliser_selection']    = $client->fields['utiliser_selection'];
                $_SESSION['catalogue']['imprimer_selection']    = $client->fields['imprimer_selection'];
                $_SESSION['catalogue']['statistiques']          = $client->fields['statistiques'];
                $_SESSION['catalogue']['export_catalogue']      = $client->fields['export_catalogue'];
                $_SESSION['catalogue']['ttc']                   = $client->fields['ttc'];
                $_SESSION['catalogue']['retours']               = $client->fields['retours'];
                $_SESSION['catalogue']['ref_cde_oblig']         = $client->fields['ref_cde_oblig'];
                // $_SESSION['catalogue']['relance_auto']          = $client->fields['relance_auto'];
            }

            if ($_SESSION['session_adminlevel'] <= cata_const::_DIMS_ID_LEVEL_STATISTICS) {
                $ensnames = array();
                $sql = "
                    SELECT      u.id, u.firstname, u.lastname, gu.id_group, MAX(gu.adminlevel) as adminlevel
                    FROM        dims_group_user gu
                    INNER JOIN  dims_user u
                    ON          u.id = gu.id_user
                    WHERE       gu.id_group IN (".implode(',', array_merge($group->getparentslite(), array($group->fields['id']))).")
                    GROUP BY    u.id
                    ORDER BY    id_group DESC, adminlevel DESC";
                $rs_users = $db->query($sql);
                while ($row = $db->fetchrow($rs_users)) {
                    if ($row['id'] == $_SESSION['dims']['userid']) {
                        $_SESSION['catalogue']['client_firstname'] = $row['firstname'];
                        $_SESSION['catalogue']['client_lastname'] = $row['lastname'];

                        // On affiche le message de connexion
                        if (!(isset($_SESSION['session_anonymous']) && $_SESSION['session_anonymous'])) {
                            $nb_cmd = 0;
                            $_SESSION['catalogue']['nb_cmd_val'] = $nb_cmd;

                            $message = "Bienvenue {$row['firstname']} {$row['lastname']}.";
                            if ($nb_cmd > 0) {
                                if ($nb_cmd == 1) {
                                    $message .= "<br><br>Il vous reste une commande à valider.";
                                } else {
                                    $message .= "<br><br>Il vous reste $nb_cmd commandes à valider.";
                                }
                            }
                        }
                    }

                    $ensnames[$row['id_group']][$row['adminlevel']]['id'] = $row['id'];
                    $ensnames[$row['id_group']][$row['adminlevel']]['firstname'] = $row['firstname'];
                    $ensnames[$row['id_group']][$row['adminlevel']]['lastname'] = $row['lastname'];
                }
                krsort($ensnames);

                // Recherche d'un responsable des achats ou plus
                $isgood = false;
                foreach ($ensnames as $idgroup => $users) {
                    foreach ($users as $idlevel => $user) {
                        if ($isgood == false) {
                            if (in_array($idlevel, array(cata_const::_DIMS_ID_LEVEL_PURCHASERESP, dims_const::_DIMS_ID_LEVEL_GROUPMANAGER, cata_const::_DIMS_ID_LEVEL_STATISTICS))) {
                                $purchaseresp = $idlevel;
                                $purchase_id = $idgroup;
                                $isgood = true;
                            }
                        }
                    }
                }
                if (!$isgood) { // Si pas de responsable des achats ou plus
                    $purchaseresp = -1;
                    $purchase_id = -1;
                }

                // Recherche d'un responsable de service
                $isgood = false;
                foreach ($ensnames as $idgroup => $users) {
                    foreach ($users as $idlevel => $user) {
                        if ($isgood == false) {
                            if ($idlevel == cata_const::_DIMS_ID_LEVEL_SERVICERESP) {
                                $serviceresp = $idlevel;
                                $service_id = $idgroup;
                                $isgood = true;
                            }
                        }
                    }
                }
                if (!$isgood) { // Si pas de responsable de service
                    $serviceresp = -1;
                    $service_id = -1;
                }

                if (isset($ensnames[$purchase_id][$purchaseresp])) {
                    $_SESSION['catalogue']['achat_id']          = $ensnames[$purchase_id][$purchaseresp]['id'];
                    $_SESSION['catalogue']['achat_lastname']    = $ensnames[$purchase_id][$purchaseresp]['lastname'];
                    $_SESSION['catalogue']['achat_firstname']   = $ensnames[$purchase_id][$purchaseresp]['firstname'];
                }
                if (isset($ensnames[$service_id][$serviceresp])) {
                    $_SESSION['catalogue']['service_id']        = $ensnames[$service_id][$serviceresp]['id'];
                    $_SESSION['catalogue']['service_lastname']  = $ensnames[$service_id][$serviceresp]['lastname'];
                    $_SESSION['catalogue']['service_firstname'] = $ensnames[$service_id][$serviceresp]['firstname'];
                }

                // Recherche d'un validateur forcé (responsable de service)
    			$rsv = $db->query('
    				SELECT  s.id, s.firstname, s.lastname
    				FROM    dims_user u
    				INNER JOIN  dims_user s
    				ON          s.id = u.id_ldap
    				WHERE   u.id = '.$_SESSION['dims']['userid']);
    			$row = $db->fetchrow($rsv);
    			if (!empty($row['id'])) {
    				$_SESSION['catalogue']['service_id']        = $row['id'];
    				$_SESSION['catalogue']['service_lastname']  = $row['lastname'];
    				$_SESSION['catalogue']['service_firstname'] = $row['firstname'];
    			}

                // forcage du responsable des achats
    			// if ($oCatalogue->getParams('default_validator_id') != '') {
       //              $_SESSION['catalogue']['achat_id']          = $oCatalogue->getParams('default_validator_id');
       //              $_SESSION['catalogue']['achat_lastname']    = $oCatalogue->getParams('default_validator_lastname');
       //              $_SESSION['catalogue']['achat_firstname']   = $oCatalogue->getParams('default_validator_firstname');
    			// }

                if (!isset($_SESSION['catalogue']['service_id']))           $_SESSION['catalogue']['service_id'] = -1;
                if (!isset($_SESSION['catalogue']['service_firstname']))    $_SESSION['catalogue']['service_firstname'] = '';
                if (!isset($_SESSION['catalogue']['service_lastname']))     $_SESSION['catalogue']['service_lastname'] = '';
                if (!isset($_SESSION['catalogue']['achat_id']))             $_SESSION['catalogue']['achat_id'] = -1;
                if (!isset($_SESSION['catalogue']['achat_firstname']))      $_SESSION['catalogue']['achat_firstname'] = '';
                if (!isset($_SESSION['catalogue']['achat_lastname']))       $_SESSION['catalogue']['achat_lastname'] = '';

            }
            else {
                $sql = "
                    SELECT u.id, u.firstname, u.lastname
                    FROM dims_user u
                    WHERE u.id = {$_SESSION['dims']['userid']}";
                $db->query($sql);
                while ($row = $db->fetchrow()) {
                    $_SESSION['catalogue']['client_firstname'] = $row['firstname'];
                    $_SESSION['catalogue']['client_lastname'] = $row['lastname'];
                }
            }

            $a_return['response'] = self::_OK;

        }//FIN SI B2B
        else{//B2C
            $client = client::find_by(array('dims_user' => $user->get('id')), null, 1);
            if( ! empty($client) && ! $client->isNew() ){
                $_SESSION['catalogue']['code_client'] = $client->get('code_client');
                $_SESSION['catalogue']['client_id'] = $client->get('id_client');
                $_SESSION['session_adminlevel'] = cata_const::_DIMS_ID_LEVEL_PURCHASERESP;
            }
            else dims_redirect(dims::getInstance()->getScriptEnv().'?dims_logout=1');
        }

        return $a_return;
   	}


    // CHARGEMENT DE L'ENVIRONNEMENT DE L'UTILISATEUR
    public static function loadDefaultEnvironment() {
        require_once DIMS_APP_PATH."modules/catalogue/include/class_const.php";
        require_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";

        $a_return = array('response' => self::_EMPTY);

        $db = dims::getInstance()->getDb();

        // adminlevel
        $_SESSION['session_adminlevel'] = 0;

        $_SESSION['catalogue'] = array();
        $_SESSION['catalogue']['ccat']          = 1;
        $_SESSION['catalogue']['code_tarif_1']  = 0;
        $_SESSION['catalogue']['code_tarif_2']  = 0;
        $_SESSION['catalogue']['escompte']      = 0;

        $_SESSION['catalogue']['remcum']        = false;
        $_SESSION['catalogue']['rem0']          = 0;
        $_SESSION['catalogue']['remgen']        = 0;
        $_SESSION['catalogue']['rem1']          = 0;
        $_SESSION['catalogue']['seuilrem1']     = 0;
        $_SESSION['catalogue']['pourcrem1']     = 0;
        $_SESSION['catalogue']['rem2']          = 0;
        $_SESSION['catalogue']['seuilrem2']     = 0;
        $_SESSION['catalogue']['pourcrem2']     = 0;

        //$_SESSION['catalogue']['tar'] = $client->fields['code_tarif_1'];

        $a_return['response'] = self::_OK;
        $_SESSION['catalogue']['context_loaded'] = true;
        return $a_return;
    }

}
