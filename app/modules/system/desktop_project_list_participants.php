<script language="javascript">
    function addContact(id_action) {
        dims_xmlhttprequest_todiv("admin.php", "dims_mainmenu=<?php echo dims_const::_DIMS_MENU_PROJECTS; ?>&dims_desktop=block&dims_action=public&op=affich_list_perstoadd&idaction="+id_action, '', 'add_contact_project');
        document.getElementById('add_contact_project').style.display="block";
    }

	function selgroup(id_group, nb_elem) {
		if(nb_elem > 0 && nb_elem < 10000) {
			if(document.getElementById("gp_"+id_group).checked) {
				for(i = 1;i<=nb_elem;i++) {
					document.getElementById(id_group+"_"+i).checked = "checked";
				}
			}
			else {
				for(i = 1;i<=nb_elem;i++) {
					document.getElementById(id_group+"_"+i).checked = "";
				}
			}
		}
	}

	function verif_gp(id_group) {
		//si un des elements de la liste n'est pas selectionne, on enlève le checked sur le groupe
		document.getElementById("gp_"+id_group).checked = "";
	}
</script>
<div id="add_contact_project" style="clear:both;position:absolute;left:200px;top:100px;width:500px;overflow:auto;display:none;"></div>
<?php
//echo $skin->open_simplebloc('');
//on affiches les personnes inscrites pour chaque action relatives au projet
$sql = "SELECT      t.id as idtask,
                    t.nb_place,
                    a.libelle,
                    a.id as idaction,
                    a.datejour,
                    a.heuredeb,
                    a.heurefin,
                    au.participate,
                    u.id as iduser,
                    u.firstname,
                    u.lastname,
                    u.id_contact

        FROM        dims_task t

        INNER JOIN  dims_mod_business_action a
        ON          a.id_task = t.id
        AND         a.datejour >= CURDATE()

        LEFT JOIN   dims_mod_business_action_utilisateur au
        ON          au.action_id = a.id
        AND         au.participate != 2

        LEFT JOIN   dims_user u
        ON          u.id = au.user_id

        WHERE       t.id_project = :idproject

        ORDER BY    t.date_start, a.datejour
                    ";
//echo $sql;
$res = $db->query($sql, array(
    ':idproject' => $project->fields['id']
));
if($db->numrows($res) > 0) {
    while($tab_res = $db->fetchrow($res)) {
        if(!isset($tab[$tab_res['idtask']])) $tab[$tab_res['idtask']] = array();
        $tab[$tab_res['idtask']]['libelle'] = $tab_res['libelle'];
        if(!isset($tab[$tab_res['idtask']][$tab_res['idaction']])) $tab[$tab_res['idtask']][$tab_res['idaction']] = array();
        $tab[$tab_res['idtask']][$tab_res['idaction']]['datejour'] = $tab_res['datejour'];
        $tab[$tab_res['idtask']][$tab_res['idaction']]['heuredeb'] = $tab_res['heuredeb'];
        $tab[$tab_res['idtask']][$tab_res['idaction']]['heurefin'] = $tab_res['heurefin'];
        $tab[$tab_res['idtask']][$tab_res['idaction']]['nb_place'] = $tab_res['nb_place'];
        if(isset($tab_res['iduser'])) {
            if(!isset($tab[$tab_res['idtask']][$tab_res['idaction']]['user'])) {
                $tab[$tab_res['idtask']][$tab_res['idaction']]['user'] = array();
                $tab[$tab_res['idtask']][$tab_res['idaction']]['cpt_att'] = 0;
                $tab[$tab_res['idtask']][$tab_res['idaction']]['cpt_ok'] = 0;
                $tab[$tab_res['idtask']][$tab_res['idaction']]['cpt_ann'] = 0;
            }
            $tab[$tab_res['idtask']][$tab_res['idaction']]['user'][$tab_res['iduser']]['name'] = $tab_res['firstname']." ".$tab_res['lastname'];
            $tab[$tab_res['idtask']][$tab_res['idaction']]['user'][$tab_res['iduser']]['id_contact'] = $tab_res['id_contact'];
            $tab[$tab_res['idtask']][$tab_res['idaction']]['user'][$tab_res['iduser']]['participate'] = $tab_res['participate'];
            //on enregistre chaque cas de participation pour choisir la couleur de l'action par la suite
            switch($tab_res['participate']) {
                case 1 :
                    $tab[$tab_res['idtask']][$tab_res['idaction']]['cpt_ok']++;
                break;
                case 0 :
                    $tab[$tab_res['idtask']][$tab_res['idaction']]['cpt_att']++;
                break;
                case 1 :
                    $tab[$tab_res['idtask']][$tab_res['idaction']]['cpt_ann']++;
                break;
            }

        }
    }
    //on affiche une legende pour les coloris utilises
    echo    '<div style="background-color:#FFFFFF;overflow:hidden;width:100%;">
                <p style="font-weight:bold;padding-left:2px;"><a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'div_legend\');">'.$_DIMS['cste']['_DIMS_LEGEND'].'</a></p>
                <div id="div_legend" style="display:none;">
                    <div style="padding-right:10px;padding-bottom:5px;float:left;">
                        <div style="width:20px;height:16px;background-color:#DCDCDC;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
                        <div style="height:16px;float:left;padding-top:4px;padding-left:5px;">'.$_DIMS['cste']['_DIMS_LABEL_PROJET_NOINSC_OR_DMD'].'</div>
                    </div>
                    <div style="padding-right:10px;padding-bottom:5px;float:left;">
                        <div style="width:20px;height:16px;background-color:#95E496;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
                        <div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_PROJET_ALLINSCOK'].'</div>
                    </div>
                    <div style="padding-right:10px;padding-bottom:5px;float:left;">
                        <div style="width:20px;height:16px;background-color:#EBE420;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
                        <div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_PROJET_NOTENOUGHTINSC'].'</div>
                    </div>
                    <div style="padding-right:10px;padding-bottom:5px;float:left;">
                        <div style="width:20px;height:16px;background-color:#E7B257;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
                        <div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_PROJET_ORANGE'].'</div>
                    </div>
                    <div style="padding-right:10px;padding-bottom:5px;float:left;">
                        <div style="width:20px;height:16px;background-color:#E75957;float:left;padding-left:5px;border:#cccccc 1px solid;margin:2px;"></div>
                        <div style="height:16px;float:left;padding-top:4px;padding-left:5px;padding-right:10px;">'.$_DIMS['cste']['_DIMS_LABEL_PROJET_INSCRED'].'</div>
                    </div>
                </div>
            </div>';

    foreach($tab as $id_task => $tab_action) {
        echo    '<table width="100%" style="margin-bottom:25px;background-color:#FFFFFF;" cellpadding="0" cellspacing="0">
                    <tr class="trl2">
                        <td style="font-size:14px;font-weight:bold;padding:5px;">
                            <a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'task_'.$id_task.'\');">'.$tab_action['libelle'].'</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div id="task_'.$id_task.'" style="display:block;">';
        $nb = 0;
        //echo                    '<table style="margin:5px;" cellpadding="0" cellspacing="0" width="99%">';
        foreach($tab_action as $lbl => $data) {
            if($lbl != "libelle") {
                $id_action = $lbl;
                $nb++;
                $back = "#FFFFFF";
                $tab_deb = explode("-",$data['datejour']);
                $date = $tab_deb[2]."/".$tab_deb[1]."/".$tab_deb[0];

                $hdeb = explode(":",$data['heuredeb']);
                $hfin = explode(":",$data['heurefin']);
                $heure = $_DIMS['cste']['_FROM']." ".$hdeb[0]."h".$hdeb[1]." ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$hfin[0]."h".$hfin[1];

                if(!isset($data['cpt_ok'])) $nbok = "0";
                else $nbok = $data['cpt_ok'];
                $place = "nb inscription : ".$nbok."/".$data['nb_place'];

                $list_insc = "";
                if(isset($data['user'])) {
                    foreach($data['user'] as $id_user => $user) {
                        switch($user['participate']) {
                            case 0: //en attente
                                $list_insc .= '<img src="./common/modules/system/img/ico_point_grey.gif"/>'.$user['name'];
                                if($nbok != $data['nb_place']) $list_insc .= '<a href="admin.php?op=valid_act_insc&id_user='.$id_user.'&id_task='.$id_task.'&id_action='.$id_action.'"><img style="border:none;" src="./common/img/checkdo.png"/></a> /';
                                $list_insc .= ' <a href="admin.php?op=refuse_act_insc&id_user='.$id_user.'&id_task='.$id_task.'&id_action='.$id_action.'"><img style="border:none;" src="./common/img/delete.png"/></a><br/>';
                                break;
                            case 3: //Annule en front
                            case 1: //accepte

                                if($user['participate'] == 3) $list_insc .= '<img src="./common/img/warning.png"/>';


                                $list_insc .= '<img src="./common/modules/system/img/ico_point_green.gif"/>'.$user['name'];
                                $list_insc .= '<a href="admin.php?op=refuse_act_insc&id_user='.$id_user.'&id_task='.$id_task.'&id_action='.$id_action.'"><img style="border:none;" src="./common/img/delete.png"/></a><br/>';
                                break;
                            case 2: //refuse -> ce cas n'est pas pris en ompte ici ... plutôt inutile, non ?
                                $list_insc .= '<img src="./common/modules/system/img/ico_point_red.gif"/>'.$user['name'];
                                $list_insc .= '<a href="admin.php?op=valid_act_insc&id_user='.$id_user.'&id_task='.$id_task.'&id_action='.$id_action.'"><img style="border:none;" src="./common/img/checkdo.png"/></a><br/>';
                                break;
                        }
                    }
                }
                else {
                    //$list_insc = '<a href="javascript:void(0);" onclick="javascript:addContact(\''.$id_action.'\');"><img src="./common/img/add.gif" style="border:none;"/>'.$_DIMS['cste']['_ADD_CT'].'</a>';
                    $back = "#DCDCDC"; //Gris : aucune demande, aucun inscrit
                }

                //coloris de l'action
                if($back != "#DCDCDC") {
                    $nb_user = count($data['user']);
                    if($data['cpt_ok'] == $nb_user) {
                        if($data['nb_place'] == $nb_user) $back = "#95E496"; //Vert : toutes les demandes validees ET nb inscrit = nb place disponibles
                        else $back="#EBE420"; //Jaune : toutes les demandes validees mais pas assez d'inscrit
                    }
                    elseif($data['cpt_ann'] == $nb_user) $back = "#E75957"; //Rouge : si tous les inscrits sont annules
                    else $back = "#E7B257"; //Orange : pas assez d'inscrits ou demandes non validées
                }

                if($nb == 1) echo   '<div style="clear:both;padding-left:5px;padding-bottom:5px;padding-top:5px;overflow:hidden;">';
                    echo                '<div style="float:left;padding-left:2px;margin-right:2px;border:#CCCCCC 1px solid;background-color:'.$back.';width:11%;">';
                    echo                    '<p style="font-weight:bold;padding-left:2px;">
                                                <a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'action_'.$id_action.'\');">'.$date.'</a></p>
                                            <p style="padding-left:2px;">
                                                <a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'action_'.$id_action.'\');">'.$heure.'</a></p>
                                            <p style="padding-left:2px;">
                                                <a href="javascript:void(0);" onclick="javascript:dims_switchdisplay(\'action_'.$id_action.'\');">'.$place.'</a></p>
                                            <div id="action_'.$id_action.'" style="display:none">'.$list_insc.'<br/>
                                                <a href="javascript:void(0);" onclick="javascript:addContact(\''.$id_action.'\');"><img src="./common/img/add.gif" style="border:none;"/>'.$_DIMS['cste']['_ADD_CT'].'</a>
                                            </div>
                                        </div>';

                    if($nb%8 == 0) echo '</div>
                                        <div style="clear:both;padding-left:5px;padding-bottom:5px;padding-top:5px;overflow:hidden;">';
            }
        }
        echo                '</div>

                            </div>
                        </td>
                    </tr>
                </table>';

    }
    //dims_print_r($tab);
}
else {
        echo "<p style=\"padding:25px;font-size:14px;\">Aucune action d&eacute;finie pour ce projet.</p>";
}
//echo $skin->close_simplebloc();
?>
