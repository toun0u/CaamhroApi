<?
//echo $skin->open_simplebloc($title, '', '', '');
?>
<script language="JavaScript" type="text/JavaScript">
	function viewDetailsEvt(id_evt) {
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=events&dims_desktop=block&dims_action=public&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=view_details_evt&id_evt="+id_evt, '', 'view_details');

        document.getElementById('view_details').style.display="block";
	}
</script>
<?php

$search_evt= dims_load_securvalue('search_evt', dims_const::_DIMS_CHAR_INPUT, true,true);
$search_sel= dims_load_securvalue('search_sel', dims_const::_DIMS_CHAR_INPUT, true,true);

if ($search_sel=='') $search_sel='coming';

//on regarde d'abord quel droit de partage on a
$cur_wksp = new workspace();
$cur_wksp->open($_SESSION['dims']['workspaceid']);

$in = '0';
//if($cur_wksp->fields['share_info'] == 1) {
//	$resw = $db->query("SELECT id FROM dims_workspace WHERE share_info = 1");
//	while($tabw = $db->fetchrow($resw)) {
//		$in .= ", ".$tabw['id'];
//	}
//}
//else {
	$in = $_SESSION['dims']['workspaceid'];
//}

$tab_event = array();
    //Recherche des evenements a venir
    $param = array();
    $sql = 'SELECT
                a.id AS id_evt,
                a.libelle,
                a.typeaction,
                a.datejour,
                a.heuredeb,
                a.heurefin,
                a.timestp_modify,
                a.description,
                u.id AS id_user,
                u.lastname,
                u.firstname,
                u.id_contact,
				ei.host
            FROM
                dims_mod_business_action a
            INNER JOIN
                dims_user u
                ON
                    u.id = a.id_user
			LEFT JOIN	dims_mod_business_event_inscription as ei
			ON	ei.id_action=a.id
            WHERE
            ';
	/*AND
                a.type = '.dims_const::_PLANNING_ACTION_EVT.'*/
	$sql.='
				a.id_parent = 0
			AND
				a.id_workspace IN ('.$in.')';

	if($enabledAdminEvent && $enableeventsteps == false) {
		$sql .= " AND a.typeaction NOT LIKE '_DIMS_PLANNING_FAIR_STEPS' ";
	}
	elseif($enabledAdminEvent == false && $enableeventsteps) {
		$sql .= " AND a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS' AND ( a.is_model = 0 OR ( a.is_model >= 1 AND a.libelle NOT LIKE '%_model' )) ";
	}
	else {
		$sql .= " AND ( a.is_model = 0 OR ( a.is_model >= 1 AND a.libelle NOT LIKE '%_model' )) ";
	}


	if ($search_evt!='') {
		$search_evt_sql= dims_load_securvalue('search_evt', dims_const::_DIMS_CHAR_INPUT, true,true,true);
		$sql.=" AND ( a.libelle like :searchevt1 OR a.typeaction like :searchevt2 OR a.description like :searchevt3 )";
        $param[':searchevt1'] = "%$search_evt_sql%";
        $param[':searchevt2'] = "%$search_evt_sql%";
        $param[':searchevt3'] = "%$search_evt_sql%";
	}
	if($search_sel != '') {
		switch($search_sel) {
			case 'all':
				break;
			case 'past':
				$sql.=' AND a.datejour <= CURDATE()';
				break;
			case 'coming' :
				$sql.=' AND a.datejour > CURDATE()';
				break;
		}
	}
   $sql.='
            ORDER BY
                a.datejour ASC';

    $ressource = $db->query($sql, $param);

    while($result = $db->fetchrow($ressource)) {
        foreach($result as $key => $value)
            $tab_event[$result['id_evt']][$key] = $value;
    }

    //echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_EVENTS_TO_COME']);
    //echo '<div style="margin-top:15px;">';
	echo '<form name="eventfilter" action="admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=list_events&ssubmenu='._DIMS_VIEW_EVENTS_LIST.'" method="post">';
    $i = 0;
    $class="trl1";
	$sall = '';
	$spast = '';
	$scome = '';
	if($search_sel == "all") $sall = 'selected="selected"';
	if($search_sel == "past") $spast = 'selected="selected"';
	if($search_sel == "coming") $scome = 'selected="selected"';
	echo '  <table width="100%" cellpadding="2" cellspacing="0">
					<tr>
						<td colspan="5">
							'.$_DIMS['cste']['_SEARCH'].'&nbsp;<input type="text" id="search_evt" name="search_evt" value="'.htmlspecialchars($search_evt).'">
							<select name="search_sel">
								<option value="all" '.$sall.'>'.$_DIMS['cste']['_DIMS_LABEL_EVT_ALL'].'</option>
								<option value="past" '.$spast.'>'.$_DIMS['cste']['_DIMS_LABEL_EVT_PAST'].'</option>
								<option value="coming" '.$scome.'>'.$_DIMS['cste']['_DIMS_LABEL_EVT_COMING'].'</option>
							</select>
							<a href="#" onclick="javascript:document.eventfilter.submit();"><img src="./common/img/search.png" border="0"></a>
						</td>
						<td colspan="3">';
	echo '
						</td>
					</tr>
                    <tr class="trl1" style="font-weight:bold;">
                        <td>'.$_DIMS['cste']['_DIMS_LABEL_LABEL'].'</td>
                        <td>'.$_DIMS['cste']['_TYPE'].'</td>
                        <td>'.$_DIMS['cste']['_DIMS_DATE'].'</td>
                        <td>'.$_DIMS['cste']['_DIMS_LABEL_HEUREDEB_SHORT'].'</td>
                        <td>'.$_DIMS['cste']['_DIMS_LABEL_HEUREFIN_SHORT'].'</td>
                        <td>'.$_DIMS['cste']['_INFOS_CREATOR'].'</td>
                    </tr>'; //<td>'.$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM'].'</td>
    if(count($tab_event) > 0){
        foreach($tab_event as $event) {
            $i++;
            if($class == "trl1") $class = "trl2";
            else $class = "trl1";

            /** Mise en forme date */
            $tab_date_mod = dims_timestamp2local($event['timestp_modify']);
            $date_mod = $tab_date_mod['date'];

            $date_evt = array();
            preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$event['datejour'],$date_evt);
            /***********************/

            //type event
            $type = '';
            if(!empty($event['typeaction']))
                $type = $_DIMS['cste'][$event['typeaction']];
            else
                $type = 'n/a';

            //description evt
            $small_desc = '';
            if(!empty($event['description'])) {
                $small_desc = substr(strip_tags($event['description']),0,40).'...';
				$rootpath="";
				if ($event['host']!='') {
						$rootpath=$$event['host'];
				}
				else {
						$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
						$rootpath=$dims->getProtocol().$http_host;
				}
                //$viewinf = '<a href="javascript:void(0);" onclick="javascript:viewDetailsEvt(\''.$event['id_evt'].'\');"><img src="./common/img/view.png"/></a>';
				$link = dims_urlencode($rootpath.'/index.php?id_event='.$event['id_evt'].'');
				$viewinf = '<a href="'.$link.'" target="_blank"><img src="./common/img/view.png"/></a>';
            }
            else {
                $small_desc = '--';
                $viewinf = '';
            }

			echo '  <tr class="'.$class.'">
                        <td onclick="desktopViewDetail(\'event\','.$event['id_evt'].');">'.$event['libelle'].'</td>
                        <td onclick="desktopViewDetail(\'event\','.$event['id_evt'].');">'.$type.'</td>
                        <td onclick="desktopViewDetail(\'event\','.$event['id_evt'].');">'.$date_evt[3].'/'.$date_evt[2].'/'.$date_evt[1].'</td>
                        <td>'.substr($event['heuredeb'],0,5).'</td>
                        <td>'.substr($event['heurefin'],0,5).'</td>
                        <td><a href="javascript:void(0);" onclick="desktopViewDetail(\'contact\','.$event['id_contact'].');">';
                    echo strtoupper(substr($event['firstname'],0,1)).". ".$event['lastname'];
                    echo '</a></td>
                    </tr>';
                    //<td>'.$date_mod.'</td>
        }
    } else {
        echo '<tr class="trl2"><td colspan="6" align="center" style="font-weight:bold;">'.$_DIMS['cste']['_DIMS_LABEL_NO_EVENT'].'</td></tr>';
    }
	echo '  </table>';
    echo '</form>';
	//echo '</div>';
	echo "<script language=\"JavaScript\" type=\"text/JavaScript\">document.getElementById('search_evt').focus();</script>";
  //  echo $skin->close_simplebloc();
?>
