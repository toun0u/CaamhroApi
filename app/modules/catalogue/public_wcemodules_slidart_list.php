<?php

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart_element.php';

$sql = 'SELECT  s.*,
                count(e.id) AS nb_elem
        FROM dims_mod_cata_wce_slidart s
        LEFT JOIN dims_mod_cata_wce_slidart_element e
        ON e.id_slidart = s.id
        GROUP BY s.id';

$res = $db->query($sql);

?>
<div class="actions">
    <a href="<?php echo $dims->getScriptEnv(); ?>?action=add">
        Ajouter un slider d'articles
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
        while($slidart = $db->fetchrow($res)) {
            $class = ($class == 'trl1') ? 'trl2' : 'trl1';
            ?>
            <tr class="<?php echo $class; ?>">
                <td align="center">
                    <?php echo $slidart['id']; ?>
                </td>
                <td>
                    <?php echo $slidart['nom']; ?>
                </td>
                <td>
                    <?php echo $slidart['description']; ?>
                </td>
                <td align="center">
                    <?php echo $slidart['nb_elem']; ?>
                </td>
                <td align="center">
                    <?php
                        $date_create = dims_timestamp2local($slidart['timestp_create']);
                        echo $date_create['date'];
                    ?>
                </td>
                <td align="center">
                    <?php
                        $date_modify = dims_timestamp2local($slidart['timestp_modify']);
                        echo $date_modify['date'];
                    ?>
                </td>
                <td align="center">
                    <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit&id_slidart=<?php echo $slidart['id']; ?>">
                        <img src="./common/modules/catalogue/img/modifier.gif" alt="Editer" title="Editer" />
                    </a>
                    <a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?action=delete&id_slidart=<?php echo $slidart['id']; ?>', 'Êtes vous sûr de vouloir supprimer ce slidart');">
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
        <th colspan="7">Aucun slider d'articles défini</th>
    </tr>
    <?php
    }
    ?>
</table>
