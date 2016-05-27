<?php
/*
CREATE TABLE `dims_mod_cata_wce_slideshow` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`nom` VARCHAR( 255 ) NULL DEFAULT NULL ,
`description` TEXT NULL DEFAULT NULL ,
`template` VARCHAR( 255 ) NULL DEFAULT NULL ,
`id_user` INT UNSIGNED NOT NULL ,
`id_module` INT UNSIGNED NOT NULL ,
`id_workspace` INT UNSIGNED NOT NULL ,
`timestp_create` BIGINT UNSIGNED NOT NULL ,
`timestp_modify` BIGINT UNSIGNED NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `dims_mod_cata_wce_slideshow_element` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_slideshow` INT UNSIGNED NOT NULL ,
`connected_only` TINYINT UNSIGNED NOT NULL DEFAULT '0' ,
`titre` VARCHAR( 255 ) NULL DEFAULT NULL ,
`descr_courte` TEXT NULL DEFAULT NULL ,
`descr_longue` TEXT NULL DEFAULT NULL ,
`lien` VARCHAR( 255 ) NULL DEFAULT NULL ,
`image` VARCHAR( 255 ) NULL DEFAULT NULL ,
`miniature` VARCHAR( 255 ) NULL DEFAULT NULL ,
`position` INT UNSIGNED NOT NULL DEFAULT 0,
`id_user` INT UNSIGNED NOT NULL ,
`id_module` INT UNSIGNED NOT NULL ,
`id_workspace` INT UNSIGNED NOT NULL ,
`timestp_create` BIGINT UNSIGNED NOT NULL ,
`timestp_modify` BIGINT UNSIGNED NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `dims_mb_wce_object` (`id`, `label`, `id_module_type`, `script`, `select_id`, `select_label`, `select_table`, `select_params`) VALUES
('', 'Affichage d''un slider', 18, '?op=''slideshow''', 'id', 'nom', 'dims_mod_cata_wce_slideshow', '');

*/

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow_element.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT,true,true,false);

switch($action) {
    default:
    case 'liste':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slideshow_list.php';
        break;
    case 'edit':
    case 'add':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slideshow_edit.php';
        break;
    case 'edit_elem':
    case 'add_elem':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slideshow_edit_elem.php';
        break;
    case 'delete':
        $id_slideshow = dims_load_securvalue('id_slideshow',dims_const::_DIMS_NUM_INPUT, true, true, false);

        $slideshow = new slideshow();
        $slideshow->open($id_slideshow);

        $slideshow->delete();

        dims_redirect($dims->getScriptEnv());
        break;
    case 'delete_elem':
        $id_slideshow_elem = dims_load_securvalue('id_slideshow_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_slideshow = 0;

        $slideshow_elem = new slideshow_element();
        $slideshow_elem->open($id_slideshow_elem);

        $id_slideshow = $slideshow_elem->fields['id_slideshow'];

        $slideshow_elem->delete();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slideshow='.$id_slideshow);
        break;
    case 'up_elem':
        $id_slideshow_elem = dims_load_securvalue('id_slideshow_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_slideshow = 0;

        $slideshow_elem = new slideshow_element();
        $slideshow_elem->open($id_slideshow_elem);

        $id_slideshow = $slideshow_elem->fields['id_slideshow'];

        $db->query('UPDATE dims_mod_cata_wce_slideshow_element SET position = position-1 WHERE id_slideshow = '.$id_slideshow.' AND position = '.($slideshow_elem->fields['position']+1));

        $slideshow_elem->fields['position']++;

        $slideshow_elem->save();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slideshow='.$id_slideshow);
        break;
    case 'down_elem':
        $id_slideshow_elem = dims_load_securvalue('id_slideshow_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_slideshow = 0;

        $slideshow_elem = new slideshow_element();
        $slideshow_elem->open($id_slideshow_elem);

        $id_slideshow = $slideshow_elem->fields['id_slideshow'];

        $db->query('UPDATE dims_mod_cata_wce_slideshow_element SET position = position+1 WHERE id_slideshow = '.$id_slideshow.' AND position = '.($slideshow_elem->fields['position']-1));

        $slideshow_elem->fields['position']--;

        $slideshow_elem->save();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slideshow='.$id_slideshow);
        break;
}
