<?php

$id_slidart = '';
$id_slidart = dims_load_securvalue('id_slidart', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_slidart,-1);

$slidart = new slidart();
$slidart->init_description();

if($id_slidart > 0) {
    $slidart->open($id_slidart);
}
else {
    $slidart->setugm();
}

if(isset($_POST) && !empty($_POST)) {
    $slidart->setvalues($_POST, 'slidart_');

    if(!empty($slidart->fields['nom'])) {

        $slidart->save();

        $id_slidart = $slidart->fields['id'];
    }
}

?>
<form method="post" action="">
    <input type="hidden" name="id_slidart" value="<?php echo $id_slidart; ?>" />
    <h3>
    <?php
        if(!$slidart->new) {
            echo 'Edition du slidart : '.$slidart->fields['nom'];
        }
        else {
            echo 'Nouveau slidart';
        }
    ?>
    </h3>
    <div>
        <div class="infos">
            <label for="slidart_nom">
                Nom * :
            </label>
            <input type="text" name="slidart_nom" id="slidart_nom" value="<?php echo $slidart->fields['nom']; ?>" />
        </div>
        <div class="infos">
            <label for="slidart_description">
                Description :
            </label>
            <textarea name="slidart_description" id="slidart_description"><?php echo $slidart->fields['description']; ?></textarea>
        </div>
        <?php /* <div class="infos">
            <label for="slidart_template">
                Modèle :
            </label>
            <select id="slidart_template" name="slidart_template">
                <option value=""></option>
                <?php
                foreach($tpl_list as $tpl) {
                    $sel = '';

                    if($tpl == $slidart->fields['template']) {
                        $sel = 'selected="selected"';
                    }

                    echo '<option '.$sel.' value="'.$tpl.'">'.$tpl.'</option>';
                }
                ?>
            </select>
        </div> */ ?>
        <input type="submit" value="Enregistrer" />
        <a href="<?php echo $dims->getScriptEnv(); ?>">
            Annuler
        </a>
    </div>
</form>
<?php
    if(!$slidart->new) {
        ?>
        <div>
            <h3>Elements du slidart</h3>
            <div class="actions">
                <form method="post" action="">
                    <input type="hidden" name="action" value="add_elem" />
                    <input type="hidden" name="id_slidart" value="<?php echo $id_slidart; ?>" />
                    <label form="slidartelem_ref">
                        Référence :
                    </label>
                    <input type="text" name="slidartelem_ref" id="slidartelem_ref" />
                    <input type="submit" value="Ajouter" />
                </form>
            </div>
            <table width="100%">
                <tr>
                    <th>#</th>
                    <th>référence</th>
                    <th>Actions</th>
                </tr>
                <?php
                if(count($slide_elems = $slidart->getElements()) > 0) {
                    foreach($slide_elems as $slide_elem) {
                        ?>
                        <tr class="trl1">
                            <td><?php echo $slide_elem->fields['id']; ?></td>
                            <td><?php echo $slide_elem->fields['ref']; ?></td>
                            <td align="center">
                                <a href="<?php echo $dims->getScriptEnv(); ?>?action=down_elem&id_slidart_elem=<?php echo $slide_elem->fields['id']; ?>">
                                    <img src="./common/modules/catalogue/img/haut.png" alt="Monter" title="Monter" />
                                </a>
                                <a href="<?php echo $dims->getScriptEnv(); ?>?action=up_elem&id_slidart_elem=<?php echo $slide_elem->fields['id']; ?>">
                                    <img src="./common/modules/catalogue/img/bas.png" alt="Descendre" title="Descendre" />
                                </a>
                                <a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?action=delete_elem&id_slidart_elem=<?php echo $slide_elem->fields['id']; ?>', 'Êtes vous sûr de vouloir supprimer cet élément');">
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
