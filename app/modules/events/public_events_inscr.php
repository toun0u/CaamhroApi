<script language="JavaScript" type="text/JavaScript">
	function closeInscription(id_evt) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=close_inscription&id_evt="+id_evt);
		document.location.reload();
	}

	function openInscription(id_evt) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=open_inscription&id_evt="+id_evt);
		document.location.reload();
	}

    function viewDetailsEvt(id_evt) {
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=view_details_evt&id_evt="+id_evt, '', 'view_details');

        document.getElementById('view_details').style.display="block";
	}
</script>
<?php

    $id_evt = null;
    $nb_evt = 0;

    $id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true,true);
    $ftype_insc = dims_load_securvalue('type_insc', dims_const::_DIMS_CHAR_INPUT, true, true);

    //Verification qu'il y a bien un id_evt
    if($id_evt != null AND !empty($id_evt)) {

		$action = new action();
		$action->open($id_evt);
		$tablabelfield=array();

		for ($ii=1;$ii<=10;$ii++) {
			if (isset($action->fields['labelfield_'.$ii]) && $action->fields['labelfield_'.$ii]!='') {
				$tablabelfield[$ii]=$ii; // on stoke les colonnes utilisées
			}
		}

        $sql    = null;
        $tab_evt= array();
        $tab_ins= array();
        $option = '';
        switch($ftype_insc) {
            case 'wait':
                $option = ' AND ei.validate = 0 ';
                $s1 = 'selected="selected"';
                $s2 = '';
                $s3 = '';
                $s4 = '';
                break;
            case 'step1':
                $option = ' AND ei.validate = 1 ';
                $s1 = '';
                $s2 = 'selected="selected"';
                $s3 = '';
                $s4 = '';
                break;
            case 'valid':
                $option = ' AND ei.validate = 2 ';
                $s1 = '';
                $s2 = '';
                $s3 = 'selected="selected"';
                $s4 = '';
                break;
            case 'cancelled':
                $option = ' AND ei.validate = -1 ';
                $s1 = '';
                $s2 = '';
                $s3 = '';
                $s4 = 'selected="selected"';
                break;
            default:
                $option='';
                $s1 = '';
                $s2 = '';
                $s3 = '';
                $s4 = '';
                break;
        }

        //Recherche de l'evt + infos insc liées (verification que l'evt appartient bien a l'user)
        $sql = 'SELECT
                    a.id AS id_evt,
                    a.typeaction,
                    a.libelle,
                    a.description,
                    a.datejour,
                    a.heuredeb,
                    a.heurefin,
                    a.timestp_modify,
                    a.timestamp_release,
                    a.supportrelease,
                    a.rub_nl,
                    a.allow_fo,
                    a.target,
                    a.teaser,
                    a.lieu,
                    a.prix,
                    a.conditions,
					a.close,
                    a.niveau,
					a.alert_modif,
					ei.field_1,
					ei.field_2,
					ei.field_3,
					ei.field_4,
					ei.field_5,
					ei.field_6,
					ei.field_7,
					ei.field_8,
					ei.field_9,
					ei.field_10,
                    ei.id AS id_insc,
                    ei.id_contact,
                    ei.validate,
                    ei.lastname,
                    ei.firstname,
                    ei.address,
                    ei.city,
                    ei.postalcode,
                    ei.country,
                    ei.phone,
                    ei.email,
                    ei.company,
                    ei.function
                FROM
                    dims_mod_business_action a
                INNER JOIN
                    dims_user u
                    ON
                        u.id = a.id_user
                LEFT JOIN
                    dims_mod_business_event_inscription ei
                    ON
                        ei.id_action = a.id
                    '.$option.'
                WHERE
                    a.id = :idevt ORDER BY
                    ei.validate DESC';
		/*
                AND
                    (
                        u.id = '.$_SESSION['dims']['userid'].'
                    OR
                        a.id_organizer = '.$_SESSION['dims']['user']['id_contact'].'
                    OR
                        a.id_responsible = '.$_SESSION['dims']['user']['id_contact'].'
                    )
                ORDER BY
                    ei.validate DESC';
					*/

        $ressource  = $db->query($sql, array(':idevt' => $id_evt) );
		if(!isset($_SESSION['business']['event_export_insc'])) $_SESSION['business']['event_export_insc'] = '';
		$_SESSION['business']['event_export_insc'] = $sql;
		$_SESSION['business']['exportdata'] = array();

        //Si on a un evt [+ infos users]
        if($db->numrows($ressource) > 0) {
            //utilise pour condition de l'affiche (Comprend id_evt bon et evt existant)
            $nb_evt = 1;
			$nb_niv2 = 0;
            while($info = $db->fetchrow($ressource)) {
                //Construction du tableau récpitulatif de l'evt
                $tab_evt['id_evt']              = $info['id_evt'];
                $tab_evt['libelle']             = $info['libelle'];
                $tab_evt['typeaction']          = $info['typeaction'];
                $tab_evt['description']         = $info['description'];
                $tab_evt['datejour']            = $info['datejour'];
                $tab_evt['heuredeb']            = $info['heuredeb'];
                $tab_evt['heurefin']            = $info['heurefin'];
                $tab_evt['timestp_modify']      = $info['timestp_modify'];
                $tab_evt['timestamp_release']   = $info['timestamp_release'];
                $tab_evt['supportrelease']      = $info['supportrelease'];
                $tab_evt['rub_nl']              = $info['rub_nl'];
                $tab_evt['allow_fo']            = $info['allow_fo'];
                $tab_evt['target']              = $info['target'];
                $tab_evt['teaser']              = $info['teaser'];
                $tab_evt['lieu']                = $info['lieu'];
                $tab_evt['prix']                = $info['prix'];
                $tab_evt['conditions']          = $info['conditions'];
                $tab_evt['niveau']              = $info['niveau'];
				$tab_evt['close']				= $info['close'];
				$tab_evt['alert_modif']			= $info['alert_modif'];


				if($info['niveau'] == 2) $nb_niv2++;
                //Si on a une inscription (ou plus) sur l'evt
                if(isset($info['id_insc']) && !empty($info['id_insc']))
                {
                    //Construction du tableau des inscriptions (Id_ins en clé premier niveau)
                    $tab_ins[$info['id_insc']]['id_insc']   = $info['id_insc'];
                    $tab_ins[$info['id_insc']]['id_contact']= $info['id_contact'];
                    $tab_ins[$info['id_insc']]['validate']  = $info['validate'];
                    $tab_ins[$info['id_insc']]['lastname']  = $info['lastname'];
                    $tab_ins[$info['id_insc']]['firstname'] = $info['firstname'];
                    $tab_ins[$info['id_insc']]['address']   = $info['address'];
                    $tab_ins[$info['id_insc']]['city']      = $info['city'];
                    $tab_ins[$info['id_insc']]['postalcode']= $info['postalcode'];
                    $tab_ins[$info['id_insc']]['country']   = $info['country'];
                    $tab_ins[$info['id_insc']]['phone']     = $info['phone'];
                    $tab_ins[$info['id_insc']]['email']     = $info['email'];
                    $tab_ins[$info['id_insc']]['company']   = $info['company'];
                    $tab_ins[$info['id_insc']]['function']  = $info['function'];
					$tab_ins[$info['id_insc']]['field_1']	= $info['field_1'];
					$tab_ins[$info['id_insc']]['field_2']	= $info['field_2'];
					$tab_ins[$info['id_insc']]['field_3']	= $info['field_3'];
					$tab_ins[$info['id_insc']]['field_4']	= $info['field_4'];
					$tab_ins[$info['id_insc']]['field_5']	= $info['field_5'];
					$tab_ins[$info['id_insc']]['field_6']	= $info['field_6'];
					$tab_ins[$info['id_insc']]['field_7']	= $info['field_7'];
					$tab_ins[$info['id_insc']]['field_8']	= $info['field_8'];
					$tab_ins[$info['id_insc']]['field_9']	= $info['field_9'];
					$tab_ins[$info['id_insc']]['field_10']	= $info['field_10'];

                }
            }
			$_SESSION['business']['exportdata'] = $tab_ins;
        }
    }

        //on verifie que l'event ait bien des etapes
		$sql_ee = "SELECT id FROM dims_mod_business_event_etap WHERE id_action = :idaction";
		$res_ee = $db->query($sql_ee, array(':idaction' => $tab_evt['id_evt']) );
		$event_etap_ok = 0;
		if(($db->numrows($res_ee) > 0) || ($tab_evt['niveau'] == 1)) {
			$event_etap_ok = 1;
		}

		//Type d'inscription; 1 : formulaire avec 1 niveau, 2 : formulaire a 2 niveau
        $form_niv = $tab_evt['niveau'];

        /** Mise en forme date */
        $tab_date_rel = dims_timestamp2local($tab_evt['timestamp_release']);
        $date_rel = $tab_date_rel['date'];

        $tab_date_mod = dims_timestamp2local($tab_evt['timestp_modify']);
        $date_mod = $tab_date_mod['date'];

        $date_evt = array();
        //ereg('^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$',$tab_evt['datejour'],$date_evt);
		preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$tab_evt['datejour'],$date_evt);

//dims_print_r($tab_evt);

        /***********************/
        echo '<div id="view_details" style="clear:both;display:none;position:absolute;left:50px;top:10px;width:450px;height:150px;"></div>';
        echo '<div>
                    <div style="width:100%;float:left;">';

                echo '<table style="margin: 10px 5px; border-collapse: collapse; width: 100%; text-align: justify;">
                            <tr><th>'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</th><td>'.$tab_evt['libelle'].'</td><th>'.$_DIMS['cste']['_DIMS_DATE'].'</th><td>'.$date_evt[3].'/'.$date_evt[2].'/'.$date_evt[1].'</td></tr>
                            <tr><th>'.$_DIMS['cste']['_TYPE'].'</th><td>'.$_DIMS['cste'][$tab_evt['typeaction']].'</td><th>'.$_DIMS['cste']['_FORM_TASK_DURATION'].'</th><td>'.substr($tab_evt['heuredeb'],0,5).' &agrave; '.substr($tab_evt['heurefin'],0,5).'</td></tr>
                            <tr><th>'.$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].'</th><td>'.substr(strip_tags($tab_evt['description']),0,50).'...</td><th>'.$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM'].'</th><td>'.$date_mod.'</td></tr>';

							$link = dims_urlencode('http://'.$http_host.'/index.php?id_event='.$tab_evt['id_evt'].'');
							$viewinf = '<a href="'.$link.'" target="_blank">'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'</a>';

                    echo '<tr><th></th><td>'.$viewinf.'</td>';
                echo '</table>';
        echo '  </div>
                <div style="width:100%;float:left;">';


                        // affichage lien planning + calcul du mois de d�calage
						  $day1="1";
						  $month1=date("n");
						  $year1=date("Y");
						  $date2=$date_evt[3].'/'.$date_evt[2].'/'.$date_evt[1];
						   list($day2,$month2,$year2) = explode("/",$date2);
						   $nbmonth = (($year2-$year1)*12 + ($month2-$month1));

                          if($tab_evt['close'] == 0 || $event_etap_ok != 0) $mtop = "100px";
                          else $mtop = "60px";

                    echo '<table style="margin: 0 5px 0 0; border-collapse: collapse; width: 100%; text-align: justify;">';
                   if($tab_evt['close'] == 1) {
                   echo '     <tr>
                                    <td colspan="5" align="left" style="padding-right:20px;font-size:14px;font-weight:bold;color:#ff0000;">
                                            '.$_DIMS['cste']['_DIMS_LABEL_CLOSED_EVT'].'
                                    </td>
                               </tr>
                               <tr>
                                    <td colspan="5" align="left">
                                          &nbsp;
                                    </td>
                               </tr>';
                   }
                   if($event_etap_ok == 0) {
                                echo '<tr><td colspan="5" style="font-size:14px;color:#ff0000;" align="center">
                                            '.$_DIMS['cste']['_DIMS_TEXT_EVT_NO_ETAPS'].'.
                                      </td></tr>';
                    }
                    echo '          <tr>
                                        <td align="right" style="padding-right:20px;">';
                                            echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_IMPORT_INSCRIPT'],'./common/img/go-down.png',"javascript:location.href='admin.php?dims_mainmenu=events&submenu=8&action=import_insc&id_evt=".$tab_evt['id_evt']."';");
                            echo '      </td>';
                            if($tab_evt['close'] == 0) {
                                echo '
                                        <td align="right" style="padding-right:20px;">';
                                            //<input type="button" onclick="javascript:closeInscription(\''.$tab_evt['id_evt'].'\');" value="'.$_DIMS['cste']['_DIMS_LABEL_CLOSE_INSCRIPTION'].'"/>
                                            echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CLOSE_INSCRIPTION'],'./common/img/close.png','javascript:closeInscription(\''.$tab_evt['id_evt'].'\');');
                                echo '</td>';
                            }
                            else {
                                echo '<td align="right" style="padding-right:20px;">';
                                            //<input type="button" onclick="javascript:openInscription(\''.$tab_evt['id_evt'].'\');" value="'.$_DIMS['cste']['_DIMS_LABEL_OPEN_INSCRIPTION'].'"/>
                                            echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_OPEN_INSCRIPTION'],'./common/img/icon_add.gif','javascript:openInscription(\''.$tab_evt['id_evt'].'\');');
                                echo '</td>';
                            }
							if($tab_evt['alert_modif'] == 1) {
								echo '<td align="right" style="padding-right:20px;">';
                                            echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ALERT_MODIF'],'./common/img/mail_toconfirm.png',"javascript:location.href='admin.php?dims_mainmenu=events&submenu=8&action=alert_modif&id_evt=".$tab_evt['id_evt']."';");
                                echo '</td>';
							}

							echo '<td align="right" style="padding-right: 20px;">
										'.dims_create_button($_DIMS['cste']['_DIMS_LABEL_EDIT_EVT'],'./common/img/btn_edit.png',"javascript:location.href='admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&action=add_evt&id=".$tab_evt['id_evt']."';").'
									</td>
                                    <td align="right" style="padding-right: 20px;">
										'.dims_create_button($_DIMS['cste']['_PLANNING'],'./common/modules/system/img/planning_month.png',"javascript:location.href='admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_desktop=block&dims_action=public&cat=-1&viewmode=month&monthadd=".$nbmonth."';").'
									</td>
									<td align="right" style="padding-right: 20px;">
										'.dims_create_button($_DIMS['cste']['_DELETE'],'./common/img/delete.png',"javascript:dims_confirmlink('admin.php?dims_mainmenu=events&submenu=8&op=xml_planning_delete_action&action_id=".$tab_evt['id_evt']."', '".$_DIMS['cste']['_DIMS_CONFIRM']."');").'
									</td><td align="right" style="padding-right: 20px;">';
        echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],'./common/img/undo.gif',"location.href='admin.php'",'cancel1','width:90px;float:left;');
        echo '                   </td></tr>';
        echo '          </table>
                </div>';
        echo '<div style="clear:both;">';
            echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S']);


                echo '<table style="width: 100%; text-align: center;border-style:1px filled #300000;">
                        <tr>
                            <td colspan="11">
                                <form name="filter_insc" method="post" action="admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$id_evt.'">
                                    <table width="100%" style="margin:10px;">
                                        <tr>
                                            <td align="left">
                                               '.$_DIMS['cste']['_DIMS_FILTER'].' :
                                               <select id="type_insc" name="type_insc" onchange="javascript:document.filter_insc.submit();">
                                                    <option value="">--</option>
                                                    <option value="wait" style="color:#999999;" '.$s1.'>'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'</option>
                                                    <option value="step1" style="color:#FFCD7D" '.$s2.'>'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_LV2'].'</option>
                                                    <option value="valid" style="color:#8BD98B" '.$s3.'>'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'</option>
                                                    <option value="cancelled" style="color:#FF0000" '.$s4.'>'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'].'</option>
                                               </select>
                                            </td>
											<td align="right">
											'.dims_create_button($_DIMS['cste']['_FORMS_DATA_EXPORT'],'./common/img/export.png',"javascript:location.href='admin.php?dims_mainmenu=events&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&action=export_insc_evt&id_evt=".$id_evt."';").'
											</td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>';
                if(count($tab_ins) > 0) {
                    echo   '<tr class="trl1">
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</th>
                                <th>'.$_DIMS['cste']['_FIRSTNAME'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_CITY'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_CP'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_COUNTRY'].'</th>
                                <th>'.$_DIMS['cste']['_PHONE'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_CONT_ENTACT'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'</th>
                                <th>'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</th>';
					// test si on a des champs en plus
					if (!empty($tablabelfield)) {
						foreach ($tablabelfield as $ind) {
							echo '<th align="center" style="padding-right:12px;">';
							if (isset($action->fields['labelfield_'.$ind])) {
								echo "Opt ".$ind;
							}
							echo '</th>';
						}
					}
                    echo '            <th>'.$_DIMS['cste']['_INFOS_STATE'].'</th>';
                    echo '  </tr>';
				}

                    $class = 'trl2';

                    //Verif inscription existante sur l'evt
                    if(count($tab_ins) == 0)
                        echo '<tr><td colspan="11" style="padding-top:25px;font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_NO_REGISTRATION'].'</td></tr>';
                    else {

                        foreach($tab_ins as $inscrit) {
                            $onclick = '';
                            if($event_etap_ok == 1) $onclick = 'onclick="location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_insc&id_evt='.$tab_evt['id_evt'] .'&id_insc='.$inscrit['id_insc'].'\'"';
                            echo '<tr class="'.$class.'" '.$onclick.'>';
                                echo '<td>';
                                    echo (!empty($inscrit['lastname'])) ? $inscrit['lastname'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['firstname'])) ? $inscrit['firstname'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['address'])) ? $inscrit['address'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['city'])) ? $inscrit['city'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['postalcode'])) ? $inscrit['postalcode'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['country'])) ? $inscrit['country'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['phone'])) ? $inscrit['phone'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['company'])) ? $inscrit['company'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['function'])) ? $inscrit['function'] : 'n/a';
                                echo '</td>';
                                echo '<td>';
                                    echo (!empty($inscrit['email'])) ? $inscrit['email'] : 'n/a';
                                echo '</td>';

								if (!empty($tablabelfield)) {
									foreach ($tablabelfield as $ind) {
										echo '<td align="center">';
										if (isset($inscrit['field_'.$ind]) && $inscrit['field_'.$ind]==1) {
											echo "<img src=\"./common/img/checkdo.png\">";
										}
										echo '</td>';
									}
								}

								echo '<td>';
                                    if($inscrit['validate'] == -1)
                                    {
                                        //Invalide
                                        echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'" src="./common/modules/system/img/ico_point_red.gif" />';
                                    }
                                    elseif($inscrit['validate'] == 2)
                                    {
                                        //Valide totalement
                                        echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'" src="./common/modules/system/img/ico_point_green.gif" />';
                                    }
                                    else
                                    {
                                        if($form_niv == 1)
                                        {
                                            //en attente de validation
                                            echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
                                        }
                                        elseif($form_niv == 2)
                                        {
                                            if($inscrit['validate'] == 0)
                                            {
                                                //en attente niv1 (et niv2)
                                                echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
                                            }
                                            elseif($inscrit['validate'] == 1)
                                            {
                                                //niv1 valide, en attente niv2
                                                echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'].'" src="./common/modules/system/img/ico_point_orange.gif" />';
                                            }
                                        }
                                    }
                                echo '</td>';
                                echo '<td>';
                                    if(!empty($inscrit['id_contact']))
                                    {
                                        echo '<img border="0" src="./common/img/user.png" alt="Fiche contact"/>';
                                    }
                                echo '</td>';
                            echo '</tr>';
                            $class = ($class == 'trl2') ? 'trl1' : 'trl2';
                        }
                    }
					echo '</table>';

                echo '<div style="float:left;width:50%;displayblock;">
                    <p>
                        '.$_DIMS['cste']['_DIMS_LEGEND'].' :
                    </p>
                    <p >
                        <ul>
                            <li>
                                <img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />
                                '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'
                            </li>';

				/*if($nb_niv2 > 0) {
                     echo   '<li>
                                <img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'].'" src="./common/modules/system/img/ico_point_orange.gif" />
                                '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'].'
                            </li>';
				}*/
                echo 		'<li>
                                <img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'" src="./common/modules/system/img/ico_point_green.gif" />
                                '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'
                            </li>
                            <li>
                                <img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'" src="./common/modules/system/img/ico_point_red.gif" />
                                '.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'
                            </li>
                        </ul>
                    </p></div>';
				echo '<div style="float:left;width:50%;displayblock;">';
				if (!empty($tablabelfield)) {
					echo '<p><ul>';
					foreach ($tablabelfield as $ind) {
						echo '<li>';
						echo '<b>Opt '.$ind.' </b>:'.$action->fields['labelfield_'.$ind];
						echo '</li>';
					}
					echo '</ul></p>';
				}
				echo '</div>';

            echo $skin->close_simplebloc();
        echo '</div>&nbsp;</div>';


?>
