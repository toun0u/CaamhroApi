<?php

$id_cloud = '';
$id_cloud = dims_load_securvalue('id_cloud', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_cloud,-1);

$cloud = new cloud();
$cloud->init_description();

if($id_cloud > 0) {
    $cloud->open($id_cloud);
}
else {
    $cloud->setugm();
}

if(isset($_POST) && !empty($_POST)) {
    $cloud->setvalues($_POST, 'cloud_');

    if(!empty($cloud->fields['nom'])) {

        $cloud->save();

        $id_cloud = $cloud->fields['id'];
    }
}

?>
<form method="post" action="">
    <input type="hidden" name="id_cloud" value="<?php echo $id_cloud; ?>" />
    <h3>
    <?php
        if(!$cloud->new) {
            echo 'Edition du nuage : '.$cloud->fields['nom'];
        }
        else {
            echo 'Nouveau nuage';
        }
    ?>
    </h3>
    <div>
        <div class="infos">
            <label for="cloud_nom">
                Nom * :
            </label>
            <input type="text" name="cloud_nom" id="cloud_nom" value="<?php echo $cloud->fields['nom']; ?>" />
        </div>
        <div class="infos">
            <label for="cloud_description">
                Description :
            </label>
            <textarea name="cloud_description" id="cloud_description"><?php echo $cloud->fields['description']; ?></textarea>
        </div>
        <div class="infos">
            <label for="cloud_mode_alea">
                Aléatoire :
            </label>
            <input type="radio" name="cloud_mode" value="1" id="cloud_mode_alea" <?php if($cloud->fields['mode'] <= 1) echo 'checked="checked" '; ?>/>
        </div>
        <div class="infos">
            <label for="cloud_mode_impo">
                Par importance :
            </label>
            <input type="radio" name="cloud_mode" value="2" id="cloud_mode_impo" <?php if($cloud->fields['mode'] == 2) echo 'checked="checked" '; ?>/>
        </div>
        <input type="submit" value="Enregistrer" />
        <a href="<?php echo $dims->getScriptEnv(); ?>">
            Annuler
        </a>
    </div>
</form>
<?php
    if(!$cloud->new) {
        ?>
        <div>
            <h3>Elements du nuage</h3>
            <div class="actions">
                <a href="<?php echo $dims->getScriptEnv(); ?>?action=add_elem&id_cloud=<?php echo $cloud->fields['id']; ?>">
                    Ajouter un élément
                </a>
            </div>
            <table width="100%">
                <tr>
                    <th>#</th>
                    <th>titre</th>
                    <th>lien</th>
                    <th>importance</th>
                    <th>Date de création</th>
                    <th>Date de modification</th>
                    <th>Actions</th>
                </tr>
                <?php
                if(count($cloud_elems = $cloud->getElements()) > 0) {
                    foreach($cloud_elems as $cloud_elem) {
                        ?>
                        <tr class="trl1">
                            <td><?php echo $cloud_elem->fields['id']; ?></td>
                            <td><?php echo $cloud_elem->fields['titre']; ?></td>
                            <td><?php echo $cloud_elem->fields['lien']; ?></td>
                            <td align="center"><?php echo $cloud_elem->fields['niveau']; ?></td>
                            <td align="center">
                                <?php
                                    $date_create = dims_timestamp2local($cloud_elem->fields['timestp_create']);
                                    echo $date_create['date'];
                                ?>
                            </td>
                            <td align="center">
                                <?php
                                    $date_modify = dims_timestamp2local($cloud_elem->fields['timestp_modify']);
                                    echo $date_modify['date'];
                                ?>
                            </td>
                            <td align="center">
                                <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit_elem&id_cloud_elem=<?php echo $cloud_elem->fields['id']; ?>">
                                    <img src="./common/modules/catalogue/img/modifier.gif" alt="Editer" title="Editer" />
                                </a>
                                <a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?action=delete_elem&id_cloud_elem=<?php echo $cloud_elem->fields['id']; ?>', 'Êtes vous sûr de vouloir supprimer cet élément');">
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
                        <th colspan="7">Aucun élément défini</th>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    }
?>
