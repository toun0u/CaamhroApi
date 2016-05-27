<?php

include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud_element.php';

$sql = 'SELECT  s.*,
                count(e.id) AS nb_elem
        FROM dims_mod_cata_wce_cloud s
        LEFT JOIN dims_mod_cata_wce_cloud_element e
        ON e.id_cloud = s.id
        GROUP BY s.id';

$res = $db->query($sql);

?>
<div class="actions">
    <a href="<?php echo $dims->getScriptEnv(); ?>?action=add">
        Ajouter un nuage
    </a>
</div>
<table width="100%">
    <tr>
        <th>#</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Nombre d'élèments</th>
        <th>Date de création</th>
        <th>Date de mise à jour</th>
        <th>Actions</th>
    </tr>
    <?php
    if($db->numrows($res)) {
        $class = '';
        while($cloud = $db->fetchrow($res)) {
            $class = ($class == 'trl1') ? 'trl2' : 'trl1';
            ?>
            <tr class="<?php echo $class; ?>">
                <td align="center">
                    <?php echo $cloud['id']; ?>
                </td>
                <td>
                    <?php echo $cloud['nom']; ?>
                </td>
                <td>
                    <?php echo $cloud['description']; ?>
                </td>
                <td align="center">
                    <?php echo $cloud['nb_elem']; ?>
                </td>
                <td align="center">
                    <?php
                        $date_create = dims_timestamp2local($cloud['timestp_create']);
                        echo $date_create['date'];
                    ?>
                </td>
                <td align="center">
                    <?php
                        $date_modify = dims_timestamp2local($cloud['timestp_modify']);
                        echo $date_modify['date'];
                    ?>
                </td>
                <td align="center">
                    <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit&id_cloud=<?php echo $cloud['id']; ?>">
                        <img src="./common/modules/catalogue/img/modifier.gif" alt="Editer" title="Editer" />
                    </a>
                    <a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?action=delete&id_cloud=<?php echo $cloud['id']; ?>', 'Êtes vous sûr de vouloir supprimer ce cloud');">
                        <img src="./common/modules/catalogue/img/supprimer.gif" alt="Supprimer" title="Supprimer" />
                    </a>
                </td>
            </tr>
            <?php
        }
    }
    else {
    ?>
    <tr>
        <th colspan="7">Aucun nuage défini</th>
    </tr>
    <?php
    }
    ?>
</table>
