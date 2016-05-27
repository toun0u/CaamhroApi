<?php
/*
CREATE TABLE `dims_mod_cata_wce_cloud` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`nom` VARCHAR( 255 ) NULL DEFAULT NULL ,
`description` TEXT NULL DEFAULT NULL ,
`mode` TINYINT UNSIGNED NULL DEFAULT NULL ,
`id_user` INT UNSIGNED NOT NULL ,
`id_module` INT UNSIGNED NOT NULL ,
`id_workspace` INT UNSIGNED NOT NULL ,
`timestp_create` BIGINT UNSIGNED NOT NULL ,
`timestp_modify` BIGINT UNSIGNED NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `dims_mod_cata_wce_cloud_element` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_cloud` INT UNSIGNED NOT NULL ,
`titre` VARCHAR( 255 ) NULL DEFAULT NULL ,
`lien` VARCHAR( 255 ) NULL DEFAULT NULL ,
`niveau` TINYINT UNSIGNED NULL DEFAULT NULL ,
`couleur` VARCHAR( 50 ) NULL DEFAULT NULL ,
`id_user` INT UNSIGNED NOT NULL ,
`id_module` INT UNSIGNED NOT NULL ,
`id_workspace` INT UNSIGNED NOT NULL ,
`timestp_create` BIGINT UNSIGNED NOT NULL ,
`timestp_modify` BIGINT UNSIGNED NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
('', 'Affichage d''un nuage de tags', 18, '?op=''cloud''', 'id', 'nom', 'dims_mod_cata_wce_cloud', '');

*/

include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud_element.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT,true,true,false);

switch($action) {
    default:
    case 'liste':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_cloud_list.php';
        break;
    case 'edit':
    case 'add':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_cloud_edit.php';
        break;
    case 'edit_elem':
    case 'add_elem':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_cloud_edit_elem.php';
        break;
    case 'delete':
        $id_cloud = dims_load_securvalue('id_cloud',dims_const::_DIMS_NUM_INPUT, true, true, false);

        $cloud = new cloud();
        $cloud->open($id_cloud);

        $cloud->delete();

        dims_redirect($dims->getScriptEnv());
        break;
    case 'delete_elem':
        $id_cloud_elem = dims_load_securvalue('id_cloud_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_cloud = 0;

        $cloud_elem = new cloud_element();
        $cloud_elem->open($id_cloud_elem);

        $id_cloud = $cloud_elem->fields['id_cloud'];

        $cloud_elem->delete();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_cloud='.$id_cloud);
        break;
}
?>
