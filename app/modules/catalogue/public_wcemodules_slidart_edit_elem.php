<?php

include_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

$id_slidart_elem = '';
$id_slidart_elem = dims_load_securvalue('id_slidart_elem', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_slidart_elem,-1);

$slidart_elem = new slidart_element();
$slidart_elem->init_description();

if($id_slidart_elem > 0) {
    $slidart_elem->open($id_slidart_elem);
}
else {
    $slidart_elem->setugm();
}

$id_slidart = '';
$id_slidart = dims_load_securvalue('id_slidart', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_slidart,$slidart_elem->fields['id_slidart']);

$slidart = new slidart();
$slidart->init_description();

if($id_slidart > 0) {
    $slidart->open($id_slidart);
}

if(isset($_POST) && !empty($_POST)) {

    $slidart_elem->setvalues($_POST, 'slidartelem_');
    $slidart_elem->fields['id_slidart'] = $id_slidart;

    if(!empty($slidart_elem->fields['titre'])) {
        if(isset($_FILES) && !empty($_FILES)) {
            if(isset($_FILES['elem_image']) && !$_FILES['elem_image']['error']) {

                $image = new docfile();

                $image->setugm();
                $image->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

                $image->fields['id_folder'] = -1;
                $image->tmpuploadedfile = $_FILES['elem_image']['tmp_name'];
                $image->fields['name'] = $_FILES['elem_image']['name'];
                $image->fields['size'] = filesize($_FILES['elem_image']['tmp_name']);

                $error = $image->save();

                if(!$error) {
                    if(!$slidart_elem->new && !$slidart_elem->fields['image']) {
                        $old_image = new docfile();
                        $old_image->open($slidart_elem->fields['image']);

                        $old_image->delete();
                    }
                    $slidart_elem->fields['image'] = $image->fields['id'];
                }
            }
            if(isset($_FILES['elem_miniature']) && !$_FILES['elem_miniature']['error']) {

                $miniature = new docfile();

                $miniature->setugm();
                $miniature->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

                $miniature->fields['id_folder'] = -1;
                $miniature->tmpuploadedfile = $_FILES['elem_miniature']['tmp_name'];
                $miniature->fields['name'] = $_FILES['elem_miniature']['name'];
                $miniature->fields['size'] = filesize($_FILES['elem_miniature']['tmp_name']);

                $error = $miniature->save();

                if(!$error) {
                    if(!$slidart_elem->new && !$slidart_elem->fields['miniature']) {
                        $old_miniature = new docfile();
                        $old_miniature->open($slidart_elem->fields['miniature']);

                        $old_miniature->delete();
                    }
                    $slidart_elem->fields['miniature'] = $miniature->fields['id'];
                }
            }
        }

        $slidart_elem->save();
        $id_slidart_elem = $slidart_elem->fields['id'];
    }
}

?>
<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
    <input type="hidden" name="id_slidart_elem" value="<?php echo $id_slidart_elem; ?>" />
    <input type="hidden" name="id_slidart" value="<?php echo $id_slidart; ?>" />
    <h3>
    <?php
        if(!$slidart_elem->new) {
            echo 'Edition de : "'.$slidart_elem->fields['titre'].'" pour le slidart : '.$slidart->fields['nom'];
        }
        else {
            echo 'Nouvel élément pour le slidart : '.$slidart->fields['nom'];
        }
    ?>
    </h3>
    <div>
        <div class="infos">
            <label for="slidartelem_titre">
                Titre * :
            </label>
            <input type="text" name="slidartelem_titre" id="slidartelem_titre" value="<?php echo $slidart_elem->fields['titre']; ?>" />
        </div>
        <div class="infos">
            <label for="slidartelem_descr_courte">
                Description courte :
            </label>
            <?php dims_fckeditor('slidartelem_descr_courte',$slidart_elem->fields['descr_courte'],'500','200',true); ?>
        </div>
        <div class="infos">
            <label for="slidartelem_descr_longue">
                Description longue :
            </label>
            <?php dims_fckeditor('slidartelem_descr_longue',$slidart_elem->fields['descr_longue'],'500','200',true); ?>
        </div>
        <div class="infos">
            <label for="slidartelem_image">
                Image (485x300) :
            </label>
            <?php
                if($slidart_elem->fields['image'] > 0) {

                    $image = new docfile();
                    $image->init_description();
                    $image->open($slidart_elem->fields['image']);

                    ?>
                    <a href="<?php echo $image->getwebpath(); ?>">
                        <?php echo $image->fields['name']; ?>
                    </a>
                    <?php
                }
            ?>
            <input type="file" name="elem_image" id="elem_image" />
        </div>
        <div class="infos">
            <label for="slidartelem_lien">
                Lien sur l'image :
            </label>
            <input type="text" name="slidartelem_lien" id="slidartelem_lien" value="<?php echo (!empty($slidart_elem->fields['lien'])) ? $slidart_elem->fields['lien'] : 'http://' ; ?>" />
        </div>
        <div class="infos">
            <label for="slidartelem_miniature">
                Miniature (60x60) :
            </label>
            <?php
                if($slidart_elem->fields['miniature'] > 0) {

                    $miniature = new docfile();
                    $miniature->init_description();
                    $miniature->open($slidart_elem->fields['miniature']);

                    ?>
                    <a href="<?php echo $miniature->getwebpath(); ?>">
                        <?php echo $miniature->fields['name']; ?>
                    </a>
                    <?php
                }
            ?>
            <input type="file" name="elem_miniature" id="elem_miniature" />
        </div>
        <div class="infos">
            <label for="slidartelem_connected_only">
                Visible uniquement pour les utilisateurs identifiés :
            </label>
            <input type="checkbox" <?php if($slidart_elem->fields['connected_only']) echo 'checked="checked"'; ?> name="slidartelem_connected_only" id="slidartelem_connected_only" value="1" />
        </div>
        <input type="submit" value="Enregistrer" />
        <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit&id_slidart=<?php echo $id_slidart; ?>">
            Revenir au slidart
        </a>
    </div>
</form>
