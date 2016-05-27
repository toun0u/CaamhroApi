<?php

include_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

$id_cloud_elem = '';
$id_cloud_elem = dims_load_securvalue('id_cloud_elem', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_cloud_elem,-1);

$cloud_elem = new cloud_element();
$cloud_elem->init_description();

if($id_cloud_elem > 0) {
    $cloud_elem->open($id_cloud_elem);
}
else {
    $cloud_elem->setugm();
}

$id_cloud = '';
$id_cloud = dims_load_securvalue('id_cloud', dims_const::_DIMS_NUM_INPUT, true, true, true,$id_cloud,$cloud_elem->fields['id_cloud']);

$cloud = new cloud();
$cloud->init_description();

if($id_cloud > 0) {
    $cloud->open($id_cloud);
}

if(isset($_POST) && !empty($_POST)) {

    $cloud_elem->setvalues($_POST, 'cloudelem_');
    $cloud_elem->fields['id_cloud'] = $id_cloud;

    if(!is_numeric($cloud_elem->fields['niveau']))
        $cloud_elem->fields['niveau'] = 100;
    elseif($cloud_elem->fields['niveau'] < 1)
        $cloud_elem->fields['niveau'] = 1;
    elseif($cloud_elem->fields['niveau'] > 200)
        $cloud_elem->fields['niveau'] = 200;


    if(!empty($cloud_elem->fields['titre'])) {
        $cloud_elem->save();
        $id_cloud_elem = $cloud_elem->fields['id'];
    }
}

?>
<form method="post" action="">
    <input type="hidden" name="id_cloud_elem" value="<?php echo $id_cloud_elem; ?>" />
    <input type="hidden" name="id_cloud" value="<?php echo $id_cloud; ?>" />
    <h3>
    <?php
        if(!$cloud_elem->new) {
            echo 'Edition de : "'.$cloud_elem->fields['titre'].'" pour le nuage : '.$cloud->fields['nom'];
        }
        else {
            echo 'Nouvel élément pour le nuage : '.$cloud->fields['nom'];
        }
    ?>
    </h3>
    <div>
        <div class="infos">
            <label for="cloudelem_titre">
                Titre * :
            </label>
            <input type="text" name="cloudelem_titre" id="cloudelem_titre" value="<?php echo $cloud_elem->fields['titre']; ?>" />
        </div>
        <div class="infos">
            <label for="cloudelem_lien">
                Lien :
            </label>
            <input type="text" name="cloudelem_lien" id="cloudelem_lien" value="<?php echo (!empty($cloud_elem->fields['lien'])) ? $cloud_elem->fields['lien'] : 'http://' ; ?>" />
        </div>
        <div class="infos">
            <label for="cloudelem_niveau">
                Importance (% 1-200) :
            </label>
            <input type="text" name="cloudelem_niveau" id="cloudelem_niveau" value="<?php echo $cloud_elem->fields['niveau']; ?>" />
        </div>
        <div class="infos">
            <label for="cloudelem_couleur">
                Couleur :
            </label>
            <input type="text" name="cloudelem_couleur" id="cloudelem_couleur" value="<?php echo $cloud_elem->fields['couleur']; ?>" />
            <a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('cloudelem_couleur', event);"><img src="./common/img/colorpicker/colorpicker.png" align="top" border="0"></a>
        </div>
        <input type="submit" value="Enregistrer" />
        <a href="<?php echo $dims->getScriptEnv(); ?>?action=edit&id_cloud=<?php echo $id_cloud; ?>">
            Revenir au nuage
        </a>
    </div>
</form>
