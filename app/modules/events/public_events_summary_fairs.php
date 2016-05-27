<?
if (!isset($_SESSION['eventsaction'])) {
	$_SESSION['eventsaction']['events']=array();

}
?>
<script language="JavaScript" type="text/JavaScript">

        function controlFieldsEvent() {
            var validate=true;
            if ($('#0_company').val()=="") {
                $('#0_company').css('background-color', '#FFFFDE');

                validate=false;
            }

            if ($('#0_lastname').val()=="") {
                $('#0_lastname').css('background-color', '#FFFFDE');
                validate=false;
            }
            if ($('#0_firstname').val()=="") {
                $('#0_firstname').css('background-color', '#FFFFDE');
                validate=false;
            }

            if ($('#0_function').val()=="") {
                $('#0_function').css('background-color', '#FFFFDE');
                validate=false;
            }

            if ($('#0_email').val()=="") {
                $('#0_email').css('background-color', '#FFFFDE');
                validate=false;
            }

            if (validate) {
                document.form_inscrip_niv1.submit();
            }
        }

	function actualizeSessionEvent(id_event,id_contact,id_etape) {
		dims_xmlhttprequest('admin.php','action=updateEventSession&id_event='+id_event+'&id_contact='+id_contact+'&id_etape='+id_etape);
	}

	function displayEventAllDetailcompany() {

	}


        function displayAddCompany(id_event) {
            dims_showcenteredpopup("",970,600,'dims_popup');
            dims_xmlhttprequest_todiv('admin.php','dims_op=event_subscribe_admin&id_event='+id_event,'','dims_popup');
        }

        var timerevents=0;
        var eventformlocked=0;

        function refreshSearchContactEvent(type) {
            clearTimeout(timerevents);
            timerevents = setTimeout("refreshSearchContactEventExec("+type+")", 400);
        }

        function refreshSearchContactEventExec(type) {
            clearTimeout(timerevents);
            if (type==0 && eventformlocked==0) {
                // recherche pour contact
                // lecture des contacts et entreprises
                var firstname=document.getElementById("0_firstname").value;
                var lastname=document.getElementById("0_lastname").value;
                var company=document.getElementById("0_company").value;

                dims_xmlhttprequest_todiv('admin.php','dims_op=event_subscribe_admin_search&type='+type+'&firstname='+firstname+'&lastname='+lastname+'&company='+company,'','searchcontactevent');
            }
        }

        function eventSelData(idtiers,idcontact) {

            if (idtiers==-1) {
                // nouvelle entreprise
                $('#0_company').css('background-color', '#FFFFDE');
                $('#0_company').removeAttr('disabled');
            }
            else {
                if (idtiers>0) {
                    // on a choisi une entreprise
                    $('#0_company').css('background-color', '#DEDEDE');
                    $('#0_company').attr('disabled', '');
                    //eventformlocked=1;
                }
            }

            if (idcontact>0) {
                // on a selectionne qq
                $('#0_firstname').attr('disabled', '');
                $('#0_lastname').attr('disabled', '');
                $('#0_firstname').css('background-color', '#DEDEDE');
                $('#0_lastname').css('background-color', '#DEDEDE');
                //eventformlocked=1;
            }
            else {
                $('#0_firstname').removeAttr('disabled');
                $('#0_lastname').removeAttr('disabled');
                $('#0_firstname').css('background-color', '#FFFFDE');
                $('#0_lastname').css('background-color', '#FFFFDE');

            }

            dims_xmlhttprequest_todiv('admin.php','dims_op=event_subscribe_admin_search&idtiers='+idtiers+'&idcontact='+idcontact,'','searchcontactevent');
        }

        function annulSelData() {
            eventformlocked=0;
            dims_xmlhttprequest('admin.php','dims_op=event_subscribe_admin_search_init');
            $('#0_firstname').removeAttr('disabled');
            $('#0_lastname').removeAttr('disabled');
            $('#0_firstname').css('background-color', '#FFFFDE');
            $('#0_lastname').css('background-color', '#FFFFDE');
            $('#0_company').css('background-color', '#FFFFDE');
            $('#0_company').removeAttr('disabled');
            refreshSearchContactEventExec(0);
        }
</script>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$lim_view = " AND a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS' ";
$tab_insc = array();
$tab_doc = array();
$id_selected_event=dims_load_securvalue('id_selected_event',dims_const::_DIMS_CHAR_INPUT, true,true);

$selected_events=dims_load_securvalue('date_events',dims_const::_DIMS_CHAR_INPUT, true,true,false);
if ($selected_events==0) $selected_events=2;

$tab_insc_all=array();

$param = array();
$sql_in = "SELECT       ei.*,
						a.id as idaction,
						a.libelle,
						a.datejour,
						a.datefin

		   FROM         dims_mod_business_action as a
		   LEFT JOIN   dims_mod_business_event_inscription as ei
		   ON           a.id = ei.id_action
		   AND          ei.validate != -1
		   WHERE        a.id_workspace = :idworkspace";
$param[':idworkspace'] = $_SESSION['dims']['workspaceid'];


if ($selected_events==2) {
    $sql_in.=" AND          a.datejour >= CURDATE() ";
}
else {
    $sql_in.=" AND          a.datejour < CURDATE() ";
}

$sql_in .=      " AND            a.id_parent=0
                   AND          a.is_model!=1
                   AND libelle <> ''
		   ".$lim_view."";

$res_in = $db->query($sql_in, $param );

if($db->numrows($res_in) > 0) {
	$tab_insc = array();
	while($tab_in = $db->fetchrow($res_in)) {
		$tab_insc_all[$tab_in['idaction']] = array();
		$tab_insc_all[$tab_in['idaction']]['libelle']  = $tab_in['libelle'];
		$tab_insc_all[$tab_in['idaction']]['datejour'] = $tab_in['datejour'];
		$tab_insc_all[$tab_in['idaction']]['datefin']  = $tab_in['datefin'];

		if (!isset($_SESSION['eventsaction']['events'][$tab_in['idaction']])) {
			$_SESSION['eventsaction']['events'][$tab_in['idaction']]=array();
			$_SESSION['eventsaction']['events'][$tab_in['idaction']]['status']=0;
		}
	}
}

if ($id_selected_event>0) {
	$sql_in.=" AND a.id = :idevent";
	$param[':idevent'] = $id_selected_event;
}

$res_in = $db->query($sql_in, $params);

if($db->numrows($res_in) > 0) {
	$tab_insc = array();
	while($tab_in = $db->fetchrow($res_in)) {
		if(!isset($tab_insc[$tab_in['idaction']])) {
			$tab_insc[$tab_in['idaction']] = array();
			$tab_insc[$tab_in['idaction']]['libelle']  = $tab_in['libelle'];
			$tab_insc[$tab_in['idaction']]['datejour'] = $tab_in['datejour'];
			$tab_insc[$tab_in['idaction']]['datefin']  = $tab_in['datefin'];
			$tab_insc[$tab_in['id_action']]['nbinsc'] = 0;
			$tab_insc[$tab_in['id_action']]['nbinsc_tovalid'] = 0;
		}

		//$tab_insc[$tab_in['id_action']]['nbinsc']++;

		if(!isset($tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']])) {
                    if ($tab_in['company']!='') {
                        $tab_insc[$tab_in['id_action']]['nbinsc']++;
			$nb_insc++;
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['validate'] = $tab_in['validate'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['name'] = $tab_in['lastname'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['firstname'] = $tab_in['firstname'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['id_contact'] = $tab_in['id_contact'];

			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['adresse'] = $tab_in['address'].' '.$tab_in['postalcode'].' '.$tab_in['city'].' '.$tab_in['country'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['phone'] = $tab_in['phone'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['email'] = $tab_in['email'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['company'] = $tab_in['company'];
			$tab_insc[$tab_in['idaction']]['contact'][$tab_in['id']]['fonction'] = $tab_in['function'];
			if ($tab_in['validate']==0) {
				$tab_insc[$tab_in['id_action']]['nbinsc_tovalid']++;
			}
                    }
		}
	}
}

// traitement des étapes
$sql_etap= "SELECT      ee.id_action,
						eu.valide_etape,
						ee.id as id_etape,
						ee.label,
						ee.position,
						ee.date_fin,
						c.id

			FROM        dims_mod_business_event_etap_user eu

			INNER JOIN  dims_mod_business_event_etap ee
			ON          ee.id = eu.id_etape

			INNER JOIN  dims_mod_business_action a
			ON          a.id = ee.id_action
			AND         a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS'
			AND         a.id_parent = 0
			AND         a.niveau = 2

			INNER JOIN  dims_mod_business_contact c
			ON          c.id = 	eu.	id_ee_contact

			ORDER BY    a.datejour DESC, c.lastname ASC, c.firstname ASC, ee.position ASC";


$tab_steps=array();
$res_doc = $db->query($sql_etap);
$tab_doc = array();
if($db->numrows($res_doc) > 0) {
	while($tab_evt = $db->fetchrow($res_doc)) {
		//on construit le tableau de resultats
		if(!isset($tab_steps[$tab_evt['id_action']])) {
			$tab_steps[$tab_evt['id_action']] = array();
		}
		$tab_steps[$tab_evt['id_action']][$tab_evt['id']][$tab_evt['id_etape']] = $tab_evt;

	}
}

// traitement des documents à recuperer
//Liste des documents à valider
$sql_doc = "SELECT      fu.id,
						fu.id_etape,
						fu.id_contact,
						fu.id_action,
						fu.id_doc_frontoffice,
						fu.date_reception,
						fu.valide,
						f.name as labelfile,
						a.libelle,
						a.datejour,
						a.datefin,
						ee.label,
						ee.date_fin,
						c.lastname,
						c.firstname

			FROM        dims_mod_business_event_etap_file_user fu

			INNER JOIN  dims_mod_business_action a
			ON          a.id = fu.id_action
			AND         a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS'
			AND         a.id_parent = 0
			AND         a.niveau = 2

			INNER JOIN  dims_mod_business_event_etap ee
			ON          ee.id = fu.id_etape

			INNER JOIN  dims_mod_business_contact c
			ON          c.id = fu.id_contact
			INNER JOIN	dims_mod_doc_file as f
			ON			f.id=fu.id_doc_frontoffice
			WHERE       fu.id_doc_frontoffice != 0
			ORDER BY    a.datejour DESC, c.lastname ASC, c.firstname ASC, ee.position ASC";


$res_doc = $db->query($sql_doc);
$tab_doc = array();
if($db->numrows($res_doc) > 0) {
	while($tab_evt = $db->fetchrow($res_doc)) {
		//on construit le tableau de resultats
		if(!isset($tab_doc[$tab_evt['id_action']])) {
			$tab_doc[$tab_evt['id_action']] = array();
			$tab_doc[$tab_evt['id_action']]['libelle'] = $tab_evt['libelle'];
			$tab_doc[$tab_evt['id_action']]['datejour'] = $tab_evt['datejour'];
			$tab_doc[$tab_evt['id_action']]['datefin'] = $tab_evt['datefin'];
			$tab_doc[$tab_evt['id_action']]['nbdocs'] = 0;
			$tab_doc[$tab_evt['id_action']]['nbinsc_tovalid']=0;
		}

		$tab_doc[$tab_evt['id_action']]['nbinsc']++;

		if ($tab_evt['valide'] == 0) {
			$tab_doc[$tab_evt['id_action']]['nbinsc_tovalid']++;
		}

		if(!isset($tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']])) {
			$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['name'] = $tab_evt['lastname'];
			$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['firstname'] = $tab_evt['firstname'];

		}

		if(!isset($tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']])) $nb_docs++;
		$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['label'] = $tab_evt['label'];
		$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['labelfile'] = $tab_evt['labelfile'];
		$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['valide'] = $tab_evt['valide'];
		$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['date_fin'] = $tab_evt['date_fin'];
		$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['docs'][$tab_evt['id']]['date_recep'] = $tab_evt['date_reception'];
		$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['docs'][$tab_evt['id']]['id_doc'] = $tab_evt['id_doc_frontoffice'];
	}
}

$lstent=array();

?>
<div class="filtre_header_news">
    <form name="list_event" action="" method="get">
    <div style="width:100%;clear:both;">
            <span style="width:25%">
                    Type of fairs
                    <select name="date_events"  onchange="javascript:document.list_event.submit();">
                            <option value="1" <? if ($selected_events==1) echo "selected"; ?>>Past events</option>
                            <option value="2"  <? if ($selected_events==2) echo "selected"; ?>>Upcoming events</option>
                    </select>
            </span>
            <span style="width:25%">
                    Event
                    <select name="id_selected_event" onchange="javascript:document.list_event.submit();">
                            <option value="">-</option>
                    <?
                    foreach ($tab_insc_all as $id_action => $act) {
                            if ($id_selected_event==$id_action) {
                                    $opt="selected";
                            }
                            else {
                                    $opt="";
                            }
                            echo "<option ".$opt." value=\"".$id_action."\">".$act['libelle']."</option>";
                    }
                    ?>
                    </select>
            </span>
    </div>
    </form>
</div>
<div class="table_news_group">

<?

// on boucle sur l'ensemble des events
foreach ($tab_insc as $id => $event) {

	?>
	<div style="margin:0px;padding:5px;font-size:14px;float:left;width:99%">
		<span style="float:left;width:87%;display:block;height:24px;"
			  <a href="javascript:void(0);" onclick="javascript:actualizeSessionEvent(<? echo $id; ?>,0,0);dims_switchdisplay('event<? echo $id; ?>');">
			  <img src="./common/img/event.png">
		<?

		$nbtodo=$event['nbinsc_tovalid'];

		if (isset($tab_doc[$id]['nbinsc_tovalid'])) {
			$nbtodo+=$tab_doc[$id]['nbinsc_tovalid'];
		}
                $datejour=implode("/",array_reverse(explode("-",$event['datejour']),true));
                $datefin=implode("/",array_reverse(explode("-",$event['datefin']),true));

		echo '<span style="width:400px;"><font style="font-weight:bold;font-size:14px;">'.$event['libelle'].'</font></span><font style="font-weight:none;">From '.$datejour." to ".$datefin."</font>";
		// on compte le nb d'entreprise
		echo " - <font style='color:#FF8F00;'>".$event['nbinsc']."</font> company - <font style='color:#FF8F00;'>".($nbtodo)."</font> todo(s)";

		// le nb de todo
		?>
			</a>
		</span>
		<span style="float:left;width:12%;display:block;height:24px;text-align:right;">
			<?
				$chg_disabled = '<img src="./common/img/go-down.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_DISABLED'].'" />';
				$chg_active = '<img src="./common/img/go-up.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_ACTIVE'].'" />';

				//?op=events&action=form_niv1&id_event=
                                $linkopen='displayAddCompany('.$id.');';
				echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'].'" href="javascript: void(0);" onclick="javascript:'.$linkopen.'">';
				echo '<img src="./common/img/add_user.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'].'" />';
				echo '</a>&nbsp;&nbsp;';


				// on affiche le lien vers les personnes
				echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'].'" href="javascript: void(0);" onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$id.'\';">';
				echo '<img src="./common/img/contact.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S'].'" />';
				echo '</a>&nbsp;&nbsp;';

				// on fait le lien en edition direct
				echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_EDIT'].'" href="javascript: void(0);" onclick="javascript:location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&dims_desktop=block&dims_action=public&action=add_evt&id='.$id.'\';">';
				echo '<img src="./common/img/edit.png" alt="'.$_DIMS['cste']['_DIMS_LABEL_EDIT'].'" />';
				echo '</a>&nbsp;&nbsp;';

				$act = 1;

				if($event['close'] == 0) {
					$chg_state = '<img src="./common/img/delete.gif" alt="'.$_DIMS['cste']['_DELETE'].'" />';
				}
				elseif($event['close'] == 1) {
					//en fait, si on remet l'icone ...
					$chg_state = '<img src="./common/img/delete.gif" alt="'.$_DIMS['cste']['_DELETE'].'" />';//'<img src="./common/img/add.gif" alt="" />';
					$act = 0;
				}

				/*if ($act == 1) {
					echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_DISABLED'].'" href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=xml_planning_disabled_action&action_id='.$id.'&retour='.$retour.'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">'.$chg_disabled.'</a>';
					echo '</a>&nbsp;&nbsp;';
				}
				else {
					// on peut reactiver
					echo '<a title="'.$_DIMS['cste']['_DIMS_LABEL_ACTIVE'].'" href="javascript: void(0);" onclick="javascript:dims_confirmlink(\'admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=xml_planning_active_action&action_id='.$id.'&retour='.$retour.'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');">'.$chg_active.'</a>';
					echo '</a>&nbsp;&nbsp;';
				}*/
			?>


			<a href="javascript:void(0);" onclick="javascript:actualizeSessionEvent(<? echo $id; ?>,0,0);dims_switchdisplay('event<? echo $id; ?>');"><img src="./common/img/up2.png"></a>
		</span>
		<?
		$state='display:none;';

		if (isset($_SESSION['eventsaction']['events'][$id]['status']) && $_SESSION['eventsaction']['events'][$id]['status']==1) {
			$state='display:block;';
		}
		?>
		<div id="event<? echo $id; ?>" style="clear:both;<? echo $state; ?>">
			<span style="clear:both;display:block;padding:5px;">
				<span style="clear:both;height:30px;padding:0px;">
					<img  style="width:24px;" src="./common/img/info2.png"><font style="font-size:16px"> To-Do (<? echo $nbtodo; ?>)</font><br><br>
					<font style="color:#FA652B;">Subscription request(s)</font>
				</span>
				<span style="clear:both;display:block;padding:0px;">
					<table style="width:100%;border: #DEDEDE solid 1px;">
						<tr style="text-align:center;font-size:11px;">
							<th class="title_table_news" style="width:15%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_NAME'];?></td>
							<th class="title_table_news" style="width:15%;"><?php echo $_SESSION['cste']['_FIRSTNAME'];?></td>
							<th class="title_table_news" style="width:20%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS'];?></td>
							<th class="title_table_news" style="width:10%;"><?php echo $_SESSION['cste']['_PHONE'];?></td>
							<th class="title_table_news" style="width:20%;"><?php echo $_SESSION['cste']['_BUSINESS_TIER'];?></td>
							<th class="title_table_news" style="width:15%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL'];?></td>
							<th class="title_table_news" style="width:5%;"><?php echo $_SESSION['cste']['_DIMS_ACTIONS'];?></td>
						</tr>
						<?
                                                $class='ligne_2_news';
						foreach ($event['contact'] as $id_insc => $contact) {
                                                    if ($contact['validate']==0) {
                                                            echo '<tr style="text-align:center;" class="'.$class.'"  >
                                                            <td>'.$contact['name'].'</td>
                                                            <td>'.$contact['firstname'].'</td>
                                                            <td>'.$contact['address'].'</td>
                                                            <td>'.$contact['phone'].'</td>
                                                            <td>'.$contact['company'].'</td>
                                                            <td>'.$contact['email'].'</td>
                                                            <td>
                                                            <a href="javascript:void(0);" onclick="javascript:validate_inscription('.$id_insc.', '.$id.');">
                                                                            <img src="./common/img/checkdo.png"/>
                                                                    </a>&nbsp;<a href="admin.php?dims_mainmenu=events&action=cancel_insc&id_evt='.$id.'&id_insc='.$id_insc.'">
                                                                            <img alt="'.$_DIMS['cste']['_DELETE'].'" src="./common/img/delete.png"/>
                                                                    </a>
                                                            </td>
                                                    </tr>';
                                                    }
                                                    $class = ($class == 'ligne_2_news') ? '' : 'ligne_2_news';
						}
						?>
					</table>
				</span>
				<span style="clear:both;display:blockheight:40px;padding:0px;">
					<font style="color:#FA652B;">Document(s) validation</font>
				</span>
				<span style="clear:both;display:block;padding:0px;">
					<table style="width:100%;border: #DEDEDE solid 1px;">
						<tr style="text-align:center;font-size:11px;">
                                                    <th class="title_table_news" style="width:15%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_NAME'];?></td>
                                                    <th class="title_table_news" style="width:15%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_STEP'];?></td>
                                                    <th class="title_table_news" style="width:20%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_RECEIVED_DOC'];?></td>
                                                    <th class="title_table_news" style="width:10%;"><?php echo $_SESSION['cste']['_DIMS_LABEL_DATE_RECEPTION_DOC'];?></td>
                                                    <th class="title_table_news" style="width:20%;"><?php echo $_SESSION['cste']['_DIMS_COMMENTS'];?></td>
                                                    <th class="title_table_news" style="width:15%;">Status</td>
                                                    <th class="title_table_news" style="width:5%;">Actions</td>
						</tr>
						<?
						if (isset($tab_doc[$id])) {
							$elem=$tab_doc[$id];
							// $tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['docs'][$tab_evt['id']]['id_doc'] = $tab_evt['id_doc_frontoffice'];
							foreach ($tab_doc[$id]['contact'] as $doc) {

								$name=$doc['name']." ".$doc['firstname'];
								foreach ($doc['steps'] as $k => $step) {

									if ($step['valide']==0) {
										$dt='';
										$id_etape_file_user=0;
                                                                                $class='ligne_2_news';

										foreach ($step['docs'] as  $l => $d) {


											$id_doc=$d['id_doc'];
											$dt=dims_timestamp2local($d['date_recep']);
											$id_etape_file_user=$l;

											$docinf = new docfile();
											$docinf->open($id_doc);
											$doc_url = $docinf->getwebpath();

											echo '<tr class="'.$class.'" style="text-align:center;">
												<td>'.$name.'</td>
												<td>'.$step['label'].'</td>
												<td><a href="'.$doc_url.'">'.$step['labelfile'].'</a></td>
												<td>'.$dt['date'].'</td>
												<td></td>
												<td><img src="./common/modules/system/img/ico_point_orange.gif"></td>
												<td>
												<a href="javascript:void(0);" onclick="javascript:validateDoc(\'\',\''.$id_etape_file_user.'\',\''.$id_doc.'\');">
                                                                                                    <img src="./common/img/checkdo2.png"/>
												</a> &nbsp;';

                                                                                        echo '<a href="javascript:void(0);" onclick="javascript:changeReceptionDoc(\''.$idfu.'\', 0)">
                                                                                                <img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'"/>
                                                                                        </a>';
                                                                                        echo '</td>
											</tr>';

                                                                                        $class = ($class == 'ligne_2_news') ? '' : 'ligne_2_news';
										}
									}


								}

							}
						}
						?>
					</table>
				</span>
			</span>
			<?
                        if (isset($event['contact']))
			foreach ($event['contact'] as $id_contact => $contact) {
                            if ($contact['company']!='') {
			?>
				<div style="margin:0px;padding:5px;font-size:14px;clear:both;">

					<div style="clear:both;display:block;border:#D6D6D6 1px solid;background-color: #FAFAFA;font-size:13px;">
						<div style="clear:both;display:block;padding:5px;">
							<span style="clear:both;display:block;height:34px;padding:0px;width:100%;">
								<span style="height:16px;float:left;">
									<img src="./common/modules/system/img/tiers.png"><?echo $contact['company']; ?>
                                                                        <br>
                                                        <?
                                                        //dims_print_r($contact);

                                                        $respass=$db->query('SELECT login,initial_password FROM dims_user WHERE id_contact= :idcontact', array(':idcontact' => $contact['id_contact']) );

                                                        if ($db->numrows($respass)>0) {
                                                            $up=$db->fetchrow($respass);

                                                            echo "Login :".$up['login']." , Password init. :".$up['initial_password'];
                                                        }

							//echo dims_create_button($contact['company'].'&nbsp;',"./common/modules/system/img/tiers.png","javascript:dims_switchdisplay('detail_etap_".$contact['id_contact']."_".$etap['id_etape']."')","","float:left;");
							echo "</span>";
							//echo dims_create_button($ch,$img,"javascript:dims_switchdisplay('detail_etap_".$contact['id_contact']."_".$etap['id_etape']."')");

							foreach ($tab_steps[$id][$contact['id_contact']] as $g => $etap) {
								echo  "<span style=\"float:right;\"><span style=\"margin-right:5px;font-size:11px;\">";
								$ch = "";
								$img= "";

								//echo "<a href=\"javascript:void(0);\" onclick=\"dims_switchdisplay('detail_etap_".$contact['id_contact']."_".$etap['id_etape']."');\">";
								switch ($etap['valide_etape']) {
									case 0:
										if (isset($tab_doc[$id]['contact'][$contact['id_contact']]['steps'][$g]['docs'])) {
											$img = "./common/modules/system/img/ico_point_orange.gif";
										}
										else {
											$img = "./common/modules/system/img/ico_point_grey.gif";
										}
										break;
									case 1:
										$img = "./common/modules/system/img/ico_point_orange.gif";
										break;
									case 2:
										$img = "./common/modules/system/img/ico_point_green.gif";
										break;
									default :

										break;
								}
                                                                $img="<img src=\"".$img."\" style=\"\">";
								$ch .= $img."&nbsp;".$etap['label'];

								echo dims_create_button($ch,'',"javascript:dims_switchdisplay('detail_etap_".$contact['id_contact']."_".$etap['id_etape']."')");
								echo "</span></span>";
							}
							?>

							</span>
						</div>

						<?
						foreach ($tab_steps[$id][$contact['id_contact']] as $g => $etap) {
						?>
						<div id="detail_etap_<? echo $contact['id_contact']."_".$etap['id_etape']; ?>" style="clear:both;padding:10px;display:none;">
							<?

								switch ($etap['valide_etape']) {
									case 0:
										if (isset($tab_doc[$id]['contact'][$contact['id_contact']]['steps'][$g]['docs'])) {
											echo "<img src=\"./common/modules/system/img/ico_point_orange.gif\">";
										}
										else {
											echo "<img src=\"./common/modules/system/img/ico_point_grey.gif\">";
										}

										break;
									case 2:
										echo "<img src=\"./common/modules/system/img/ico_point_green.gif\">";
										break;
									default :

										break;
								}
							echo $etap['label'];
							?>
							<table style="width:100%;">
								<tr style="background-color: #000000;color:#FFFFFF;">
									<td style="width:20%">Document</td>
									<td style="width:20%">Transmission date</td>
									<td style="width:30%">Comments</td>
									<td style="width:10%">Status</td>
									<td style="width:15%">Actions</td>
								</tr>
								<?
								if (isset($tab_doc[$id])) {
									$elem=$tab_doc[$id];

									foreach ($tab_doc[$id]['contact'] as $doc) {

										$name=$doc['name']." ".$doc['firstname'];
										foreach ($doc['steps'] as $k => $step) {

											if ($etap['id_etape']==$k) {
												if ($step['valide']==0 || true) {
													$dt='';

													foreach ($step['docs'] as  $l => $d) {
														$id_doc=$d['id_doc'];
														$dt=dims_timestamp2local($d['date_recep']);


														$docinf = new docfile();
														$docinf->open($id_doc);
														$doc_url = $docinf->getwebpath();
														echo '<tr class="trl2">
															<td><a href="'.$doc_url.'">'.$step['labelfile'].'</a></td>
															<td>'.$dt['date'].'</td>
															<td></td>';

														if ($step['valide']==1) {
															echo '<td><img src="./common/modules/system/img/ico_point_green.gif"></td><td>';
														}
														else {
															echo '<td><img src="./common/modules/system/img/ico_point_orange.gif"></td><td>

															<a href="javascript:void(0);" onclick="javascript:validateDoc(\'\',\''.$id_etape_file_user.'\',\''.$id_doc.'\');">
																<img src="./common/img/checkdo2.png"/>
															</a> &nbsp;';
														}

															echo '<a href="javascript:void(0);" onclick="javascript:changeReceptionDoc(\''.$idfu.'\', 0)">
																<img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'"/>
															</a>
														</td>
														</tr>';
													}
												}
											}
										}

									}
								}
								?>
							</table>
						</div>
						<?
						}
						?>
					</div>
				</div>
			<?
                            }
			}

		?>
			</div>
	</div>
	<?
}


?>
</div>