<?php
require_once DIMS_APP_PATH.'include/functions/files.php';
$id_module = $project->fields['id_module'];
$id_object = dims_const::_SYSTEM_OBJECT_PROJECT;
$id_record = $project->fields['id'];

echo $_DIMS['cste']['_DOCS'];
// collecte des fichiers deja insérés
$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record);
echo dims_createAddFileLink($id_module,$id_object,$id_record,'float: left;');

echo '<div style="clear:both;width:100%;"><table>';
if (!empty($lstfiles)) {
    echo "<tr class=\"trl1\">
        <td style=\"width:38%;padding-left:10px;\">".$_DIMS['cste']['_DOCS']."</td>
        <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
        <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']."</td>
        <td style=\"width:37%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
								<td></td>
    </tr>";
    $licolor=2;
    foreach ($lstfiles as $file) {
        if ($licolor==1) $licolor=2;
        else $licolor=1;
        $cdate = dims_timestamp2local($file['timestp_create']);
        $mdate = dims_timestamp2local($file['timestp_modify']);
        echo "<tr class=\"trl2\">
            <td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
                <a href=".$file['downloadlink']." title=\"Voir le document.\">"
                .$file['name'].
                "</a>
            </td>
            <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                .$cdate['date'].
            "</td>
            <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                .$mdate['date'].
            "</td>
            <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                .$file['firstname'].
                " "
                .$file['lastname'].
            "</td>
            <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">";
        echo "</td>
        </tr>";
    }
}

echo '</table></div>';

echo $_DIMS['cste']['_DOCS'].' '.$_DIMS['cste']['_DIMS_LABEL_PHASE'].'/'.$_DIMS['cste']['_DIMS_TASKS'];

$idProjet = $project->fields['id'];

$sqlTaskDoc = 'SELECT
                task.id as id_task,
                task.label,
                task.type,
                task.state,
                task.description,
                task.date_start,
                task.date_end,
                file.id as id_file,
                file.name,
                file.description,
                file.timestp_create,
                file.timestp_modify,
                file.size,
                file.id_user,
                user.lastname,
                user.firstname
            FROM
                dims_task task
            INNER JOIN
                dims_mod_doc_file file
                ON
                    file.id_record = task.id
                AND
                    file.id_module = task.id_module
                AND
                    file.id_object = :idobject
            LEFT JOIN
                dims_user user
                ON
                    user.id = file.id_user
            WHERE
                task.id_project = :idprojet
            ORDER BY
                task.type ASC,
                task.date_start ASC';

$resTaskDoc = $db->query($sqlTaskDoc, array(
    ':idobject' => dims_const::_SYSTEM_OBJECT_TASK,
    ':idprojet' => $idProjet
));

$tabTasks = array();
$tabPhase = array();

while($infoTaskDoc = $db->fetchrow($resTaskDoc)) {
    $tabTemp = array();

    if($infoTaskDoc['type'] == 0 && isset($tabPhase[$infoTaskDoc['id_task']])) //phase
        $tabTemp = $tabPhase[$infoTaskDoc['id_task']];
    elseif($infoTaskDoc['type'] == 1 && isset ($tabTasks[$infoTaskDoc['id_task']])) //tache
        $tabTemp = $tabTasks[$infoTaskDoc['id_task']];
    else {
        $tabTemp['id_task']    = $infoTaskDoc['id_task'];
        $tabTemp['label']      = $infoTaskDoc['label'];
        $tabTemp['type']       = $infoTaskDoc['type'];
        $tabTemp['state']      = $infoTaskDoc['state'];
        $tabTemp['description']= $infoTaskDoc['description'];
        $tabTemp['date_start'] = $infoTaskDoc['date_start'];
        $tabTemp['date_end']   = $infoTaskDoc['date_end'];
    }

    if(!empty($infoTaskDoc['id_file'])) {
        $infoDocs = array();

        $infoDocs['id_file']        = $infoTaskDoc['id_file'];
        $infoDocs['name']           = $infoTaskDoc['name'];
        $infoDocs['description']    = $infoTaskDoc['description'];
        $infoDocs['timestp_create'] = $infoTaskDoc['timestp_create'];
        $infoDocs['timestp_modify'] = $infoTaskDoc['timestp_modify'];
        $infoDocs['size']           = $infoTaskDoc['size'];
        $infoDocs['id_user']        = $infoTaskDoc['id_user'];
        $infoDocs['lastname']       = $infoTaskDoc['lastname'];
        $infoDocs['firstname']      = $infoTaskDoc['firstname'];
        $infoDocs['downloadlink']   = dims_urlencode("admin-light.php?dims_op=doc_file_download&docfile_id=".$infoTaskDoc['id_file']);

        $tabTemp['docs'][$infoTaskDoc['id_file']] = $infoDocs;
    }

    if($infoTaskDoc['type'] == 0) //phase
        $tabPhase[$infoTaskDoc['id_task']] = $tabTemp;
    elseif($infoTaskDoc['type'] == 1) //tache
        $tabTasks[$infoTaskDoc['id_task']] = $tabTemp;
}

//dims_print_r($tabPhase);
//dims_print_r($tabTasks);

if(!empty($tabPhase)) {
    echo '<h3>'.$_DIMS['cste']['_DIMS_LABEL_PHASE'].'</h3>';
    foreach($tabPhase as $idPhase => $phase) {
        echo '<div style="margin: 10px;">';
        echo '<h4>'.$phase['label'].'</h4>';
        if(!empty($phase['docs'])){
            echo "<table>
                <tr class=\"trl1\">
                    <td style=\"width:30%;padding-left:10px;\">".$_DIMS['cste']['_DOCS']."</td>
                    <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</td>
                    <td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
                    <td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']."</td>
                    <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
                    <td></td>
                </tr>";

            $licolor = 1;

            foreach($phase['docs'] as $idDoc => $file) {
                if ($licolor==1) $licolor=2;
                else $licolor=1;
                $cdate = dims_timestamp2local($file['timestp_create']);
                $mdate = dims_timestamp2local($file['timestp_modify']);
                echo "<tr class=\"trl2\">
                    <td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
                        <a href=".$file['downloadlink']." title=\"Voir le document.\">"
                        .$file['name'].
                        "</a>
                    </td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$file['description'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$cdate['date'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$mdate['date'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$file['firstname'].
                        " "
                        .$file['lastname'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">";
                    if($file['id_user'] == $_SESSION['dims']['userid'] ||
                       $project->fields['id_create'] == $_SESSION['dims']['userid'] ||
                       $project->fields['id_resp'] == $_SESSION['dims']['userid']) {
                        echo "<a href=\"javascript:void(0);\" onclick=\"javascript:delete_doc('".$file['id_file']."')\"><img src=\"./common/img/delete.png\"/></a>";
                    }
                echo "</td>
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
        if(!empty($task['docs'])){
            echo "<table>
                <tr class=\"trl1\">
                    <td style=\"width:30%;padding-left:10px;\">".$_DIMS['cste']['_DOCS']."</td>
                    <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</td>
                    <td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_CREATE_ON']."</td>
                    <td style=\"width:10%;\">".$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']."</td>
                    <td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
                    <td></td>
                </tr>";

            $licolor = 1;

            foreach($task['docs'] as $idDoc => $file) {
                if ($licolor==1) $licolor=2;
                else $licolor=1;
                $cdate = dims_timestamp2local($file['timestp_create']);
                $mdate = dims_timestamp2local($file['timestp_modify']);
                echo "<tr class=\"trl2\">
                    <td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
                        <a href=".$file['downloadlink']." title=\"Voir le document.\">"
                        .$file['name'].
                        "</a>
                    </td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$file['description'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$cdate['date'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$mdate['date'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">"
                        .$file['firstname'].
                        " "
                        .$file['lastname'].
                    "</td>
                    <td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">";
                echo "</td>
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

?>
