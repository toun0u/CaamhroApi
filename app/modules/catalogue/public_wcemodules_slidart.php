<?php
/*
CREATE TABLE `dims_mod_cata_wce_slidart` (
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

CREATE TABLE `dims_mod_cata_wce_slidart_element` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_slidart` INT UNSIGNED NOT NULL ,
`ref` TINYINT UNSIGNED NOT NULL DEFAULT '0' ,
`position` INT UNSIGNED NOT NULL DEFAULT 0,
`id_user` INT UNSIGNED NOT NULL ,
`id_module` INT UNSIGNED NOT NULL ,
`id_workspace` INT UNSIGNED NOT NULL ,
`timestp_create` BIGINT UNSIGNED NOT NULL ,
`timestp_modify` BIGINT UNSIGNED NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `dims_mod_cata_wce_slidart_element` CHANGE `ref` `ref` VARCHAR( 20 ) NOT NULL DEFAULT '0';
*/

include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart_element.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT,true,true,false);

switch($action) {
    default:
    case 'liste':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slidart_list.php';
        break;
    case 'edit':
    case 'add':
        include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slidart_edit.php';
        break;
    case 'add_elem':
        $id_slidart = dims_load_securvalue('id_slidart', dims_const::_DIMS_NUM_INPUT, true, true, true);

        if($id_slidart > 0) {
            $slidart_elem = new slidart_element();

            $slidart_elem->init_description();
            $slidart_elem->setugm();

            $slidart_elem->setvalues($_POST, 'slidartelem_');
            $slidart_elem->fields['id_slidart'] = $id_slidart;

            if(!empty($slidart_elem->fields['ref'])) {
                $art = new article();
                $art->open($slidart_elem->fields['ref']);

                if($art->numrows > 0) {
                    $slidart_elem->save();
                }
            }
        }

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slidart='.$id_slidart);
        break;
    case 'delete':
        $id_slidart = dims_load_securvalue('id_slidart',dims_const::_DIMS_NUM_INPUT, true, true, false);

        $slidart = new slidart();
        $slidart->open($id_slidart);

        $slidart->delete();

        dims_redirect($dims->getScriptEnv());
        break;
    case 'delete_elem':
        $id_slidart_elem = dims_load_securvalue('id_slidart_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_slidart = 0;

        $slidart_elem = new slidart_element();
        $slidart_elem->open($id_slidart_elem);

        $id_slidart = $slidart_elem->fields['id_slidart'];

        $slidart_elem->delete();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slidart='.$id_slidart);
        break;
    case 'up_elem':
        $id_slidart_elem = dims_load_securvalue('id_slidart_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_slidart = 0;

        $slidart_elem = new slidart_element();
        $slidart_elem->open($id_slidart_elem);

        $id_slidart = $slidart_elem->fields['id_slidart'];

        $db->query('UPDATE dims_mod_cata_wce_slidart_element SET position = position-1 WHERE id_slidart = '.$id_slidart.' AND position = '.($slidart_elem->fields['position']+1));

        $slidart_elem->fields['position']++;

        $slidart_elem->save();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slidart='.$id_slidart);
        break;
    case 'down_elem':
        $id_slidart_elem = dims_load_securvalue('id_slidart_elem',dims_const::_DIMS_NUM_INPUT, true, true, false);
        $id_slidart = 0;

        $slidart_elem = new slidart_element();
        $slidart_elem->open($id_slidart_elem);

        $id_slidart = $slidart_elem->fields['id_slidart'];

        $db->query('UPDATE dims_mod_cata_wce_slidart_element SET position = position+1 WHERE id_slidart = '.$id_slidart.' AND position = '.($slidart_elem->fields['position']-1));

        $slidart_elem->fields['position']--;

        $slidart_elem->save();

        dims_redirect($dims->getScriptEnv().'?action=edit&id_slidart='.$id_slidart);
        break;
}
