<?php

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow_element.php';

$sql = 'SELECT  s.*,
                count(e.id) AS nb_elem
        FROM dims_mod_cata_wce_slideshow s
        LEFT JOIN dims_mod_cata_wce_slideshow_element e
        ON e.id_slideshow = s.id
        GROUP BY s.id';

$res = $db->query($sql);

?>
<div class="actions">
    <a href="<?php echo $dims->getScriptEnv(); ?>?action=add">
        Ajouter un slideshow
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
        while($slideshow = $db->fetchrow($res)) {
            $class = ($class == 'trl1') ? 'trl2' : 'trl1';
            ?>
            <tr class="<?php echo $class; ?>">
                <td align="center">
                    <?php echo $slideshow['id']; ?>
                </td>
                <td>
                    <?php echo $slideshow['nom']; ?>
                </td>
                <td>
                    <?php echo $slideshow['description']; ?>
                </td>
                <td align="center">
                    <?php echo $slideshow['nb_elem']; ?>
                </td>
                <td align="center">
                    <?php
                        $date_create = dims_timestamp2local($slideshow['timestp_create']);
                        echo $date_create['date'];
                    ?>
                </td>
                <td align="center">
                    <?php
                        $date_modify = dims_timestamp2local($slideshow['timestp_modify']);
                        echo $date_modify['date'];
                    ?>
                </td>
                <td align="center">
                    <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit&id_slideshow=<?php echo $slideshow['id']; ?>">
                        <img src="./common/modules/catalogue/img/modifier.gif" alt="Editer" title="Editer" />
                    </a>
                    <a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?action=delete&id_slideshow=<?php echo $slideshow['id']; ?>', 'Êtes vous sûr de vouloir supprimer ce slideshow');">
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
        <th colspan="7">Aucun slideshow défini</th>
    </tr>
    <?php
    }
    ?>
</table>
