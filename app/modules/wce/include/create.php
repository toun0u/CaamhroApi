<?
/*
 *      Copyright 2000-2009  Netlor Concept <contact@netlor.fr>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

require_once DIMS_APP_PATH.'modules/wce/include/classes/class_heading.php';
require_once DIMS_APP_PATH.'modules/wce/wiki/include/class_wce_lang.php';
require_once DIMS_APP_PATH.'modules/system/class_lang.php';

$id_lang = 0;
$first = true;
foreach(lang::all() as $label){
    $wceL = new wce_lang();
    $wceL->init_description();
    $wceL->fields['id_module'] = $this->fields['id'];
    $wceL->fields['id_workspace'] = $this->fields['id_workspace'];
    $wceL->fields['id_user'] = $_SESSION['dims']['userid'];
    $wceL->fields['label'] = $label->fields['label'];
    $wceL->fields['ref'] = $label->fields['ref'];
    $wceL->fields['id'] = $label->fields['id'];
    if($first){
        $wceL->fields['default'] = 1;
        $wceL->fields['is_active'] = 1;
        $first = false;
        $id_lang = $label->fields['id'];
    }
    $wceL->save();
}

$wce_heading = new wce_heading();
$wce_heading->init_description();
$wce_heading->fields['label'] = 'Racine';
$wce_heading->fields['id_heading'] = 0;
$wce_heading->fields['depth'] = 1;
$wce_heading->fields['parents'] = 0;
$wce_heading->fields['id_module'] = $this->fields['id'];
$wce_heading->fields['id_workspace'] = $this->fields['id_workspace'];
$wce_heading->fields['id_user'] = $_SESSION['dims']['userid'];
$wce_heading->fields['position'] = 1;
$wce_heading->fields['visible'] = 1;
$wce_heading->fields['visible_if_connected'] = 1;
$wce_heading->fields['id_lang'] = $id_lang;
$wce_heading->save();
?>
