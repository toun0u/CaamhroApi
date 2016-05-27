<?php

include_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

$id_slideshow_elem = '';
$id_slideshow_elem = dims_load_securvalue('id_slideshow_elem', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_slideshow_elem,-1);

$slideshow_elem = new slideshow_element();
$slideshow_elem->init_description();

if($id_slideshow_elem > 0) {
    $slideshow_elem->open($id_slideshow_elem);
}
else {
    $slideshow_elem->setugm();
}

$id_slideshow = '';
$id_slideshow = dims_load_securvalue('id_slideshow', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_slideshow,$slideshow_elem->fields['id_slideshow']);

$slideshow = new slideshow();
$slideshow->init_description();

if($id_slideshow > 0) {
    $slideshow->open($id_slideshow);
}

if(isset($_POST) && !empty($_POST)) {

    $slideshow_elem->setvalues($_POST, 'slideshowelem_');
    $slideshow_elem->fields['id_slideshow'] = $id_slideshow;
    if (!isset($_POST['connected_only'])) {
		$slideshow_elem->fields['connected_only'] = 0;
	}

    if(!empty($slideshow_elem->fields['titre'])) {
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
                    if(!$slideshow_elem->new && $slideshow_elem->fields['image']) {
                        $old_image = new docfile();
                        $old_image->open($slideshow_elem->fields['image']);
                        $old_image->delete();
                    }
                    $slideshow_elem->fields['image'] = $image->fields['id'];
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
                    if(!$slideshow_elem->new && $slideshow_elem->fields['miniature']) {
                        $old_miniature = new docfile();
                        $old_miniature->open($slideshow_elem->fields['miniature']);
                        $old_miniature->delete();
                    }
                    $slideshow_elem->fields['miniature'] = $miniature->fields['id'];
                }
            }
        }

        $slideshow_elem->save();
        $id_slideshow_elem = $slideshow_elem->fields['id'];
    }
}

?>
<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
    <input type="hidden" name="id_slideshow_elem" value="<?php echo $id_slideshow_elem; ?>" />
    <input type="hidden" name="id_slideshow" value="<?php echo $id_slideshow; ?>" />
    <h3>
    <?php
        if(!$slideshow_elem->new) {
            echo 'Edition de : "'.$slideshow_elem->fields['titre'].'" pour le slideshow : '.$slideshow->fields['nom'];
        }
        else {
            echo 'Nouvel élément pour le slideshow : '.$slideshow->fields['nom'];
        }
    ?>
    </h3>
    <div>
        <div class="infos">
            <label for="slideshowelem_titre">
                Titre * :
            </label>
            <input type="text" name="slideshowelem_titre" id="slideshowelem_titre" value="<?php echo $slideshow_elem->fields['titre']; ?>" />
        </div>
        <div class="infos">
            <label for="slideshowelem_descr_courte">
                Description courte :
            </label>
            <?php dims_fckeditor('slideshowelem_descr_courte',$slideshow_elem->fields['descr_courte'],'500','200',true); ?>
        </div>
        <div class="infos">
            <label for="slideshowelem_descr_longue">
                Description longue :
            </label>
            <?php dims_fckeditor('slideshowelem_descr_longue',$slideshow_elem->fields['descr_longue'],'500','200',true); ?>
        </div>
        <div class="infos">
            <label for="slideshowelem_descr_position">
                Position de la description :
            </label>
            <select name="slideshowelem_descr_position" id="slideshowelem_descr_position">
            	<option value="">-- Non renseigné --</option>
            	<?php
            	foreach ($a_descr_positions as $value => $label) {
					$selected = ($value == $slideshow_elem->fields['descr_position']) ? ' selected="selected"' : '';
					echo '<option value="'.$value.'"'.$selected.'>'.$label.'</option>';
				}
            	?>
            </select>
        </div>
        <div class="infos">
            <label for="slideshowelem_image">
                Image (<?php echo _CATA_SLIDESHOW_DIMENTIONS_IMAGES; ?>) :
            </label>
            <?php
                if($slideshow_elem->fields['image'] > 0) {

                    $image = new docfile();
                    $image->init_description();
                    $image->open($slideshow_elem->fields['image']);

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
            <label for="slideshowelem_lien">
                Lien sur l'image :
            </label>
            <input type="text" name="slideshowelem_lien" id="slideshowelem_lien" value="<?php echo (!empty($slideshow_elem->fields['lien'])) ? $slideshow_elem->fields['lien'] : 'http://' ; ?>" />
        </div>
        <div class="infos">
            <label for="slideshowelem_miniature">
                Miniature (<?php echo _CATA_SLIDESHOW_DIMENTIONS_MINIATURES; ?>) :
            </label>
            <?php
                if($slideshow_elem->fields['miniature'] > 0) {

                    $miniature = new docfile();
                    $miniature->init_description();
                    $miniature->open($slideshow_elem->fields['miniature']);

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
            <label for="slideshowelem_connected_only">
                Visible uniquement pour les utilisateurs identifiés :
            </label>
            <input type="checkbox" <?php if($slideshow_elem->fields['connected_only']) echo 'checked="checked"'; ?> name="slideshowelem_connected_only" id="slideshowelem_connected_only" value="1" />
        </div>
        <input type="submit" value="Enregistrer" />
        <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit&id_slideshow=<?php echo $id_slideshow; ?>">
            Revenir au slideshow
        </a>
    </div>
</form>
