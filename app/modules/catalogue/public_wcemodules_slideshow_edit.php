<?php

$id_slideshow = '';
$id_slideshow = dims_load_securvalue('id_slideshow', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_slideshow,-1);

$slideshow = new slideshow();
$slideshow->init_description();

if($id_slideshow > 0) {
    $slideshow->open($id_slideshow);
}
else {
    $slideshow->setugm();
}

if(isset($_POST) && !empty($_POST)) {
    $slideshow->setvalues($_POST, 'slideshow_');

    if(!empty($slideshow->fields['nom'])) {

        $slideshow->save();

        $id_slideshow = $slideshow->fields['id'];
    }
}

$tpl_list = cata_wceslideshows_gettpl();

?>
<form method="post" action="">
    <input type="hidden" name="id_slideshow" value="<?php echo $id_slideshow; ?>" />
    <h3>
    <?php
        if(!$slideshow->new) {
            echo 'Edition du slideshow : '.$slideshow->fields['nom'];
        }
        else {
            echo 'Nouveau slideshow';
        }
    ?>
    </h3>
    <div>
        <div class="infos">
            <label for="slideshow_nom">
                Nom * :
            </label>
            <input type="text" name="slideshow_nom" id="slideshow_nom" value="<?php echo $slideshow->fields['nom']; ?>" />
        </div>
        <div class="infos">
            <label for="slideshow_description">
                Description :
            </label>
            <textarea name="slideshow_description" id="slideshow_description"><?php echo $slideshow->fields['description']; ?></textarea>
        </div>
        <div class="infos">
            <label for="slideshow_template">
                Modèle :
            </label>
            <select id="slideshow_template" name="slideshow_template">
                <option value=""></option>
                <?php
                foreach($tpl_list as $tpl) {
                    $sel = '';

                    if($tpl == $slideshow->fields['template']) {
                        $sel = 'selected="selected"';
                    }

                    echo '<option '.$sel.' value="'.$tpl.'">'.$tpl.'</option>';
                }
                ?>
            </select>
        </div>
        <input type="submit" value="Enregistrer" />
        <a href="<?php echo $dims->getScriptEnv(); ?>">
            Annuler
        </a>
    </div>
</form>
<?php
    if(!$slideshow->new) {
        ?>
        <div>
            <h3>Elements du slideshow</h3>
            <div class="actions">
                <a href="<?php echo $dims->getScriptEnv(); ?>?action=add_elem&id_slideshow=<?php echo $slideshow->fields['id']; ?>">
                    Ajouter un élément
                </a>
            </div>
            <table width="100%">
                <tr>
                    <th>#</th>
                    <th>titre</th>
                    <th>Description courte</th>
                    <th>Date de création</th>
                    <th>Date de modification</th>
                    <th>Actions</th>
                </tr>
                <?php
                if(count($slide_elems = $slideshow->getElements()) > 0) {
                    foreach($slide_elems as $slide_elem) {
                        ?>
                        <tr class="trl1">
                            <td><?php echo $slide_elem->fields['id']; ?></td>
                            <td><?php echo $slide_elem->fields['titre']; ?></td>
                            <td><?php echo $slide_elem->fields['descr_courte']; ?></td>
                            <td align="center">
                                <?php
                                    $date_create = dims_timestamp2local($slide_elem->fields['timestp_create']);
                                    echo $date_create['date'];
                                ?>
                            </td>
                            <td align="center">
                                <?php
                                    $date_modify = dims_timestamp2local($slide_elem->fields['timestp_modify']);
                                    echo $date_modify['date'];
                                ?>
                            </td>
                            <td align="center">
                                <a href="<?php echo $dims->getScriptEnv(); ?>?action=down_elem&id_slideshow_elem=<?php echo $slide_elem->fields['id']; ?>">
                                    <img src="./common/modules/catalogue/img/haut.png" alt="Monter" title="Monter" />
                                </a>
                                <a href="<?php echo $dims->getScriptEnv(); ?>?action=up_elem&id_slideshow_elem=<?php echo $slide_elem->fields['id']; ?>">
                                    <img src="./common/modules/catalogue/img/bas.png" alt="Descendre" title="Descendre" />
                                </a>
                                <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit_elem&id_slideshow_elem=<?php echo $slide_elem->fields['id']; ?>">
                                    <img src="./common/modules/catalogue/img/modifier.gif" alt="Editer" title="Editer" />
                                </a>
                                <a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?action=delete_elem&id_slideshow_elem=<?php echo $slide_elem->fields['id']; ?>', 'Êtes vous sûr de vouloir supprimer cet élément');">
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
