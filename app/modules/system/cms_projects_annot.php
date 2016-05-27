<?php

$id_module 	= $project->fields['id_module'];
$id_object 	= dims_const::_SYSTEM_OBJECT_PROJECT;
$id_record 	= $project->fields['id'];
$id_user 	= $_SESSION['dims']['userid'];

//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_COMMENTS']);
echo $_DIMS['cste']['_DIMS_COMMENTS'];
dims_annotation($id_object, $id_record, $project->fields['label'], $_SESSION['dims']['userid'], $_SESSION['dims']['workspaceid'],$id_module,false);
//echo $skin->close_simplebloc();

//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_COMMENTS'].' '.$_DIMS['cste']['_DIMS_LABEL_PHASE'].'/'.$_DIMS['cste']['_DIMS_TASKS']);
echo $_DIMS['cste']['_DIMS_COMMENTS'].' '.$_DIMS['cste']['_DIMS_LABEL_PHASE'].'/'.$_DIMS['cste']['_DIMS_TASKS'];

$idProjet = $project->fields['id'];

$sqlTaskAnn = 'SELECT
                task.id as id_task,
                task.label,
                task.type,
                task.state,
                task.description,
                task.date_start,
                task.date_end,
                a.id as id_ann,
                a.*,
                user.lastname,
                user.firstname,
				t.id as idtag,
				t.tag
            FROM
                dims_task task
            INNER JOIN
                dims_annotation a
                ON
                    a.id_record = task.id
                AND
                    a.id_module = task.id_module
                AND
                    a.id_object = :idobject
            INNER JOIN
                dims_user user
                ON
                    user.id = a.id_user
			LEFT JOIN
				dims_annotation_tag at
				ON
					a.id = at.id_annotation
			LEFT JOIN
				dims_tag t
				ON
					t.id = at.id_tag
            WHERE
                task.id_project = :idprojet
			AND
				(a.private = 0
			OR
				(a.private = 1 AND a.id_user = :iduser ))
            ORDER BY
                task.type ASC,
                task.date_start ASC,
				a.date_annotation DESC';

$resTaskAnn = $db->query($sqlTaskAnn, array(
    ':idobject' => dims_const::_SYSTEM_OBJECT_TASK,
    ':idprojet' => $idProjet,
    ':iduser'   => $id_user
));

$tabTasks = array();
$tabPhase = array();

while($infoTaskAnn = $db->fetchrow($resTaskAnn)) {
    $tabTemp = array();

    if($infoTaskAnn['type'] == 0 && isset($tabPhase[$infoTaskAnn['id_task']])) //phase
        $tabTemp = $tabPhase[$infoTaskAnn['id_task']];
    elseif($infoTaskAnn['type'] == 1 && isset ($tabTasks[$infoTaskAnn['id_task']])) //tache
        $tabTemp = $tabTasks[$infoTaskAnn['id_task']];
    else {
        $tabTemp['id_task']    = $infoTaskAnn['id_task'];
        $tabTemp['label']      = $infoTaskAnn['label'];
        $tabTemp['type']       = $infoTaskAnn['type'];
        $tabTemp['state']      = $infoTaskAnn['state'];
        $tabTemp['description']= $infoTaskAnn['description'];
        $tabTemp['date_start'] = $infoTaskAnn['date_start'];
        $tabTemp['date_end']   = $infoTaskAnn['date_end'];
    }

    if(!empty($infoTaskAnn['id_ann'])) {
        $infoAnn = array();

        $infoAnn['id_ann']        	= $infoTaskAnn['id_ann'];
        $infoAnn['title']           = $infoTaskAnn['title'];
        $infoAnn['content']    		= $infoTaskAnn['content'];
        $infoAnn['object_label'] 	= $infoTaskAnn['object_label'];
        $infoAnn['date_annotation'] = $infoTaskAnn['date_annotation'];
        $infoAnn['id_user']        	= $infoTaskAnn['id_user'];
        $infoAnn['lastname']       	= $infoTaskAnn['lastname'];
        $infoAnn['firstname']      	= $infoTaskAnn['firstname'];

        $tabTemp['annot'][$infoTaskAnn['id_ann']] = $infoAnn;
    }

    if($infoTaskAnn['type'] == 0) //phase
        $tabPhase[$infoTaskAnn['id_task']] = $tabTemp;
    elseif($infoTaskAnn['type'] == 1) //tache
        $tabTasks[$infoTaskAnn['id_task']] = $tabTemp;
}

//dims_print_r($tabPhase);
//dims_print_r($tabTasks);

if(!empty($tabPhase)) {
    echo '<h3>'.$_DIMS['cste']['_DIMS_LABEL_PHASE'].'</h3>';
    foreach($tabPhase as $idPhase => $phase) {
        echo '<div style="margin: 10px;">';
        echo '<h4>'.$phase['label'].'</h4>';
        if(!empty($phase['annot'])){
            echo "<table width=\"100%\">
                <tr class=\"trl1\">
                    <td style=\"width:25%;padding-left:10px;\">".$_DIMS['cste']['_DIMS_LABEL_TITLE']."</td>
                    <td style=\"width:40%;\">".$_DIMS['cste']['_DIMS_LABEL_CONTENT']."</td>
                    <td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
                    <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
                </tr>";

            $licolor = 1;

            foreach($phase['annot'] as $idAnn => $annot) {
                if ($licolor==1) $licolor=2;
                else $licolor=1;
                $cdate = dims_timestamp2local($annot['date_annotation']);
                echo "<tr class=\"trl2\">
                    <td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">"
                        .$annot['title'].
					"</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$annot['content'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$cdate['date'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$annot['firstname'].
                        " "
                        .$annot['lastname'].
                    "</td>
                </tr>";
            }
            echo '</table>';
        }
        else {
            echo $_DIMS['cste']['_PROJET_NO_DOC'];
        }
        echo '</div>';
    }
}

if(!empty($tabTasks)) {
    echo '<h3>'.$_DIMS['cste']['_DIMS_TASKS'].'</h3>';
    foreach($tabTasks as $idTask => $task) {
        echo '<div style="margin: 10px;">';
        echo '<h4>'.$task['label'].'</h4>';
        if(!empty($task['annot'])){
            echo "<table width=\"100%\">
                <tr class=\"trl1\">
                    <td style=\"width:25%;padding-left:10px;\">".$_DIMS['cste']['_DIMS_LABEL_TITLE']."</td>
                    <td style=\"width:40%;\">".$_DIMS['cste']['_DIMS_LABEL_CONTENT']."</td>
                    <td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
                    <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
                </tr>";

            $licolor = 1;

            foreach($task['annot'] as $idAnn => $annot) {
                if ($licolor==1) $licolor=2;
                else $licolor=1;
                $cdate = dims_timestamp2local($annot['date_annotation']);
                echo "<tr class=\"trl2\">
                    <td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">"
                        .$annot['title'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$annot['content'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$cdate['date'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$annot['firstname'].
                        " "
                        .$annot['lastname'].
                    "</td>
                </tr>";
            }
            echo '</table>';
        }
        else {
            echo $_DIMS['cste']['_PROJET_NO_DOC'];
        }
        echo '</div>';
    }
}

echo $skin->close_simplebloc();
?>
