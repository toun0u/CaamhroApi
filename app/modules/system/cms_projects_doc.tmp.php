<?php

echo $_DIMS['cste']['_DOCS'];


$id_module = $project->fields['id_module'];
$id_object = dims_const::_SYSTEM_OBJECT_EVENT;
$id_record = $project->fields['id'];

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
        </tr>";
    }
}

echo '</table></div>';

?>
