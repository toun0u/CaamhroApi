<?php
unset($_SESSION['dims']['currentproject']);
unset($_SESSION['dims']['currenttask']);
unset($_SESSION['dims']['currentphase']);

$_SESSION['dims']['projectmenu'] = dims_const::_DIMS_PROJECTMENU_TASK;

//modification de l'avancement d'une tache
$id_ftask = dims_load_securvalue('id_task', dims_const::_DIMS_NUM_INPUT, true, true);

if($id_ftask > 0) {
    $progress = dims_load_securvalue('progress', dims_const::_DIMS_NUM_INPUT, true, true);

    $task = new task();
    $task->open($id_ftask);

    $task->fields['progress'] = $progress;

    $task->save();
}

//on initialise des variables utilisees pour savoir quelles requetes ne renvoient rien
//ce qui sera utilise pour l'affichage
$view_tasktodo = 0;
$view_dmdstodo = 0;
/////////////////////////////////////////////
///         TACHES A EFFECTUER            ///
/////////////////////////////////////////////

//on selectionne toutes les inscriptions concernant le user courant
$sql = "SELECT      p.label as p_name,
                    p.type as p_type,
                    t.label as t_name,
                    t.id as id_task,
                    t.date_start,
                    t.date_end,
                    t.progress,
                    a.libelle,
                    a.id as idaction,
                    a.datejour,
                    a.heuredeb,
                    a.heurefin,
                    au.participate

        FROM        dims_task t

        INNER JOIN  dims_mod_business_action a
        ON          a.id_task = t.id
        AND         a.datejour >= CURDATE()

        INNER JOIN  dims_project p
        ON          p.id = t.id_project

        INNER JOIN  dims_mod_business_action_utilisateur au
        ON          au.action_id = a.id
        AND         au.participate = 1
        AND         au.user_id = :userid

        ORDER BY    t.date_start, p_name";

$res = $db->query($sql, array(
    ':userid' => $_SESSION['dims']['userid']
));
if($db->numrows($res) > 0) {
    $view_tasktodo = 1; //il y a des resultats, il faudra afficher le tableau
}

///////////////////////////////////////////
///         DEMANDES A GERER            ///
///////////////////////////////////////////

//on selectionne tous les projets ou le user est responsable ou createur
$sql_d =   "SELECT      p.label as p_name,
                        p.type as p_type,
						p.id as p_id,
                        t.label as t_name,
                        t.id as id_task,
                        t.nb_place,
                        a.id as idaction,
                        a.datejour,
                        a.heuredeb,
                        a.heurefin,
                        au.participate,
                        au.user_id

            FROM        dims_task t

            INNER JOIN  dims_mod_business_action a
            ON          a.id_task = t.id
            AND         a.datejour >= CURDATE()

            INNER JOIN  dims_project p
            ON          p.id = t.id_project
            AND         (p.id_create = :userid
                    OR  p.id_resp = :userid
                    OR  p.id_resp2 = :userid
                    OR  p.id_resp3 = :userid )

            INNER JOIN  dims_mod_business_action_utilisateur au
            ON          au.action_id = a.id
            AND         (au.participate = 0
                    OR  au.participate = 1)

            ORDER BY    t.date_start, p_name";

$resd = $db->query($sql_d, array(
    ':userid' => $_SESSION['dims']['userid']
));
if($db->numrows($resd) > 0) {
    $view_dmdstodo = 1; //il y a des resultats, il faudra afficher le tableau
    $tabd = array();
    while($data = $db->fetchrow($resd)) {
        if(!isset($tabd[$data['id_task']])) $tabd[$data['id_task']] = array();
        if(!isset($tabd[$data['id_task']][$data['idaction']])) $tabd[$data['id_task']][$data['idaction']] = array();
		$tabd[$data['id_task']][$data['idaction']]['p_id'] = $data['p_id'];
        $tabd[$data['id_task']][$data['idaction']]['p_name'] = $data['p_name'];
        $tabd[$data['id_task']][$data['idaction']]['p_type'] = $data['p_type'];
        $tabd[$data['id_task']][$data['idaction']]['t_name'] = $data['t_name'];
		$tabd[$data['id_task']][$data['idaction']]['t_id'] = $data['id_task'];
        $tabd[$data['id_task']][$data['idaction']]['nb_place'] = $data['nb_place'];
        if(!isset($tabd[$data['id_task']][$data['idaction']]['nb_insc'])) $tabd[$data['id_task']][$data['idaction']]['nb_insc'] = 0;
        if(!isset($tabd[$data['id_task']][$data['idaction']]['nb_dmd'])) $tabd[$data['id_task']][$data['idaction']]['nb_dmd'] = 0;

        if($data['participate'] == 1 )  $tabd[$data['id_task']][$data['idaction']]['nb_insc'] += 1;
        elseif($data['participate'] == 0 )  $tabd[$data['id_task']][$data['idaction']]['nb_dmd'] += 1;

        $tmpd = explode("-", $data['datejour']);
        $hd = explode(":", $data['heuredeb']);
        $hf = explode(":", $data['heurefin']);

        $tabd[$data['id_task']][$data['idaction']]['date'] = $tmpd[2]."/".$tmpd[1]."/".$tmpd[0]." ".$_DIMS['cste']['_FROM']." ".$hd[0]."h".$hd[1]." ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$hf[0]."h".$hf[1];
    }
}
//dims_print_r($tabd);

//Affichage des resultats
if($view_tasktodo == 0 && $view_dmdstodo == 0) {
        echo $skin->open_simplebloc('','100%');
            echo '<p>'.$_DIMS['cste']['_DIMS_LABEL_PROJET_NO_ACTION'].'</p>';
        echo $skin->close_simplebloc();
}else {
    if($view_tasktodo == 1) {
        echo '<div style="float:left;width:50%;overflow:hidden;">';
            echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_PROJET_ACTION_TODO'],'100%');
                //echo $sql;
                echo '<table width="100%" cellpadding="0" cellspacing="0">
                            <tr class="trl1" style="height:30px;">
                                <th align="left">'.$_DIMS['cste']['_LABEL_PROJECTS'].'</th>
                                <th align="left">'.$_DIMS['cste']['_TYPE'].'</th>
                                <th align="left">'.$_DIMS['cste']['_DIMS_TASKS'].'</th>
                                <th align="left">'.$_DIMS['cste']['_DIMS_DATE'].'</th>
                                <th align="left">'.$_DIMS['cste']['_DIMS_LABEL_PROGRESS'].'</th>
                            </tr>';
                    $class = "trl1";
                    $tab_task = array();
                    while($tab_t = $db->fetchrow($res)) {
                        if($class == "trl1") $class = "trl2";
                        else $class = "trl1";
                        if($tab_t['p_type'] != "Formation") {
                            if(!isset($tab_task[$tab_t['id_task']])) {
                                //traitement des dates
                                $ddeb = dims_timestamp2local($tab_t['date_start']);
                                $dfin = dims_timestamp2local($tab_t['date_end']);
                                $date = "du ".$ddeb['date']." au ".$dfin['date'];

                                //raccourci pour indiquer l'avancement d'une tache
                                $progress = '<form id="form'.$tab_t['id_task'].'" name="form'.$tab_t['id_task'].'" method="post" action="'.$scriptenv.'?op=project_main&id_task='.$tab_t['id_task'].'">';
                                // Sécurisation du formulaire par token
                                require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                                $token = new FormToken\TokenField;
                                $token->field("progress");
                                $tokenHTML = $token->generate();
                                $progress .= $tokenHTML;
                                $progress .= '   <select style="width:70px" id="progress" name="progress">';
                                    for($i=0;$i<=10;$i++) {
                                        $j = $i*10;
                                        if($j == $tab_t['progress']) $chk = 'selected = "selected" ';
                                        else $chk = '';
                                        $progress .= '<option value="'.$j.'" '.$chk.'>'.$j.'%</option>';
                                    }
                                $progress .=     '</select>
                                                <input type="submit" value="OK"/>
                                            </form>';

                                $tab_task[$tab_t['id_task']] = $tab_t['id_task'];
                            }
                            else continue;
                        }
                        else {
                            //traitement des dates
                            $tmpd = explode("-", $tab_t['datejour']);
                            $hd = explode(":", $tab_t['heuredeb']);
                            $hf = explode(":", $tab_t['heurefin']);

                            $date = $tmpd[2]."/".$tmpd[1]."/".$tmpd[0]." ".$_DIMS['cste']['_FROM']." ".$hd[0]."h".$hd[1]." ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$hf[0]."h".$hf[1];

                            $progress = "--";
                        }
                        echo    '<tr class="'.$class.'" style="height:26px;">
                                    <td>'.$tab_t['p_name'].'</td>
                                    <td>'.$tab_t['p_type'].'</td>
                                    <td>'.$tab_t['t_id'].'</td>
                                    <td>'.$date.'</td>
                                    <td>'.$progress.'</td>
                                </tr>';
                    }
                echo '</table>';
            echo $skin->close_simplebloc();
        echo '</div>';
    }
    if($view_dmdstodo == 1) {
        echo '<div style="float:left;width:80%;overflow:hidden;">';
            echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_PROJET_DMD_TODO'],'100%');
                //echo $sql_d;
                echo '<table width="100%" cellpadding="0" cellspacing="0">
                            <tr class="trl1" style="height:30px;">
                                <th align="left">'.$_DIMS['cste']['_LABEL_PROJECTS'].'</th>
                                <th align="left">'.$_DIMS['cste']['_DIMS_TASKS'].'</th>
                                <th align="left">'.$_DIMS['cste']['_DIMS_DATE'].'</th>
                                <th align="left" width="10%">Nb demandes</th>
                                <th align="left" width="10%">Nb inscrits</th>
                                <th align="left" width="10%">Nb places</th>
                            </tr>';
                $class = "trl1";
                foreach($tabd as $id_task => $tab_action) {

                    foreach($tab_action as $id_action => $inf) {

                        if($class == "trl1") $class = "trl2";
                        else $class = "trl1";

                        echo '<tr onclick="javascript:document.location.href=\'/admin.php?dims_mainmenu=7&dims_desktop=block&dims_action=public&idproject='.$inf['p_id'].'&desktop_project_suite=2&idtask='.$inf['t_id'].'\';" class="'.$class.'" style="height:26px;">
                                    <td>'.$inf['p_name'].'</td>
                                    <td>'.$inf['t_name'].'</td>
                                    <td>'.$inf['date'].'</td>
                                    <td>'.$inf['nb_dmd'].'</td>
                                    <td>'.$inf['nb_insc'].'</td>
                                    <td>'.$inf['nb_place'].'</td>
                                </tr>';
                    }
                }
                echo '</table>';
            echo $skin->close_simplebloc();
        echo '</div>';
    }
}
?>
