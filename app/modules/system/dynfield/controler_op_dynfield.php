<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once DIMS_APP_PATH.'modules/system/dynfield/global.php';
require_once(DIMS_APP_PATH . "/modules/system/class_metafield.php");
require_once(DIMS_APP_PATH . "/modules/system/class_metafielduse.php");

if (!isset($_SESSION['dims']['current_meta_object_id'])) $_SESSION['dims']['current_meta_object_id']=0;
if (!isset($_SESSION['dims']['current_meta_module_type_id'])) $_SESSION['dims']['current_meta_module_type_id']=0;
if (!isset($_SESSION['dims']['current_meta_tablename'])) $_SESSION['dims']['current_meta_tablename']='';

$object_id=dims_load_securvalue("object_id",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['current_meta_object_id']);
$module_type_id=dims_load_securvalue("module_type_id",dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['current_meta_module_type_id']);
$meta_field_id=dims_load_securvalue("meta_field_id",dims_const::_DIMS_NUM_INPUT,true,true);
$tablename = dims_load_securvalue('tablename',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['dims']['current_meta_tablename']);
$dynfield_op=dims_load_securvalue("dynfield_op",dims_const::_DIMS_CHAR_INPUT,true,true);

switch($dynfield_op){
	case 'savemetafield':
		$object_id = dims_load_securvalue('object_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$module_type_id = dims_load_securvalue('module_type_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$tablename = dims_load_securvalue('tablename',dims_const::_DIMS_CHAR_INPUT, true, true);
		$field_name = dims_load_securvalue('field_name',dims_const::_DIMS_CHAR_INPUT, true, true);
		$metafield_id = dims_load_securvalue('metafield_id',dims_const::_DIMS_CHAR_INPUT, true, true);

		if(empty($metafield_id)) {
			if(dims_data_object_dynamic::isUniqName($field_name, $object_id)) {
				$meta_field		= new meta_field();

				// case à cocher (-> booleen)
				$is_indexed = false;
				$is_indexed = (bool)dims_load_securvalue('is_indexed',dims_const::_DIMS_CHAR_INPUT, true, true);

				$meta_field->setIdObject($object_id);
				$meta_field->fields['option_needed'] = 0;
				$meta_field->fields['option_search'] = 0;
				$meta_field->fields['option_exportview'] = 0;

				$meta_field->setvalues($_POST,'field_');

				$meta_field->fields['fieldname'] = dims_data_object_dynamic::getBestFieldName($object_id,$tablename);

				$meta_field->setIsIndexed($is_indexed);

				$meta_field->setIdModuleType($module_type_id);
				$meta_field->setTableName($tablename);

				$meta_field->save();

				$dynamic_object = new dims_data_object_dynamic($object_id,$module_type_id);
				$dynamic_object->setTablename($tablename);

				$dynamic_object->addDynFields($meta_field);
			}
		}
		else {
			$meta_field = new meta_field();

			$meta_field->open($metafield_id);

			// case à cocher (-> booleen)
			$is_indexed = false;
			$is_indexed = (bool)dims_load_securvalue('is_indexed',dims_const::_DIMS_CHAR_INPUT, true, true);

			$fieldnew_position = dims_load_securvalue('fieldnew_position',dims_const::_DIMS_NUM_INPUT, true, true);

			$meta_field->setNewPosition($fieldnew_position);

			$meta_field->fields['option_needed'] = 0;
			$meta_field->fields['option_search'] = 0;
			$meta_field->fields['option_exportview'] = 0;

			$meta_field->setvalues($_POST,'field_');

			$meta_field->setIsIndexed($is_indexed);

			$meta_field->save();
		}
		break;

	case 'usemetafield':
		$metafield_id = dims_load_securvalue('metafield_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$metafield = new meta_field();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);
			$metafield->fields['used']=1;
			$metafield->save();
		}
		break;

	case 'deletemetafield':
		$object_id = dims_load_securvalue('object_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$metafield_id = dims_load_securvalue('metafield_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$id_module_type = dims_load_securvalue('id_module_type',dims_const::_DIMS_NUM_INPUT, true, true);
		$tablename = dims_load_securvalue('tablename',dims_const::_DIMS_CHAR_INPUT, true, true);

		$metafield = new meta_field();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);

			$dynamic_object = new dims_data_object_dynamic($object_id,$id_module_type);
			$dynamic_object->setTablename($tablename);

			$dynamic_object->deleteDynField($metafield);
		}
	break;

	case 'moveupmetafield':
		$metafield_id = dims_load_securvalue('metafield_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$metafield = new meta_field();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);
			$position=$metafield->fields['position'];
			if($metafield->fields['position']>1) {
				$position=$metafield->fields['position'];
				// on bouge celui du dessus en dessous
				$db->query("UPDATE dims_meta_field SET position=position+1
							WHERE position = :position and id_object = :idobject ", array(
					':idobject' => $metafield->fields['id_object'],
					':position'	=> $position-1
				));
				// on bouge celui courant au dessus
				$db->query("UPDATE dims_meta_field SET position= :position
							WHERE id = :id ", array(
					':position'	=> $position-1,
					':id'		=> $metafield->fields['id']
				));
			}
		}
		break;

	case 'movedownmetafield':
		$metafield_id = dims_load_securvalue('metafield_id',dims_const::_DIMS_NUM_INPUT, true, true);
		$metafield = new meta_field();
		if ($metafield_id>0) {
			$metafield->open($metafield_id);
			$select = "SELECT max(position) as maxpos from dims_meta_field where id_object = :idobject ";

			$res=$db->query($select, array(
				':idobject' => $object_id
			));
			$fields = $db->fetchrow($res);
			$maxpos = $fields['maxpos'];
			if($metafield->fields['position']<$maxpos) {
				$position=$metafield->fields['position'];
				// on bouge celui du dessous en dessus
				$db->query("UPDATE dims_meta_field SET position=position-1 where position = :position and id_object = :idobject ", array(
					':idobject' => $metafield->fields['id_object'],
					':position'	=> $position+1
				));
				// on bouge celui courant au dessus
				$db->query("UPDATE dims_meta_field SET position= :position where id = :id ", array(
					':position'	=> $position+1,
					':id'		=> $metafield->fields['id']
				));
			}
		}
		break;
}

?>
