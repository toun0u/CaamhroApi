<?php
require_once DIMS_APP_PATH . "/modules/system/class_metafield.php";
require_once DIMS_APP_PATH . "/modules/system/class_business_metacateg.php";
$type = $this->getid_object();
$continue = false;
switch($type){
	case dims_const::_SYSTEM_OBJECT_TIERS:
		$continue = true;
		break;
	case dims_const::_SYSTEM_OBJECT_CONTACT:
		$continue = true;
		break;
}
if($continue){
	$sql = "SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
			FROM		".metafield::TABLE_NAME." as mf
			INNER JOIN	".mb_field::TABLE_NAME." as mb
			ON			mb.id=mf.id_mbfield
			LEFT JOIN	".business_metacateg::TABLE_NAME." as mc
			ON			mf.id_metacateg=mc.id
			WHERE		mf.id_object = :idobject
			AND			mf.used=1
			ORDER BY	mc.position, mf.position";
	$params = array(
		":idobject" => $type,
	);
	$db = dims::getInstance()->getDb();
	$res = $db->query($sql,$params);
	if($db->numrows($res)){
		$buildFields = array();
		while ($r = $db->fetchrow($res)){
			$buildFields[$r['id_cat']]['label'] = $r['categlabel'];
			$buildFields[$r['id_cat']]['fields'][] = array(
				'id_mtf' => $r['id'],
				'namefield' => $r['namefield'],
				'titlefield' => $r['titlefield'],
				'name' => $r['name'],
				'type' => $r['type'],
				'format' => $r['format'],
				'values' => $r['values'],
				'maxlength' => $r['maxlength'],
				'option_needed' => $r['option_needed'],
			);
		}
		foreach($buildFields as $b){
			if(!empty($b['fields'])){
				$blockLabel = empty($b['label'])?"Inconnue":$b['label'];
				$block = $form->addBlock('dyn_'.$blockLabel,$blockLabel);
				foreach($b['fields'] as $f){
					switch($f['type']) {
						case 'textarea':
							$form->add_textarea_field(array(
								'name'						=> 'fck_dyn_'.$f['namefield'],
								'label'						=> (isset($_SESSION['cste'][$f['titlefield']])?$_SESSION['cste'][$f['titlefield']]:$f['name']),
								'db_field'					=> $f['namefield'],
								'block'						=> 'dyn_'.$blockLabel,
								'mandatory'					=> $f['option_needed'],
							));
							break;
						case 'text':
							$form->add_text_field(array(
								'name'						=> 'dyn_'.$f['namefield'],
								'label'						=> (isset($_SESSION['cste'][$f['titlefield']])?$_SESSION['cste'][$f['titlefield']]:$f['name']),
								'db_field'					=> $f['namefield'],
								'block'						=> 'dyn_'.$blockLabel,
								'mandatory'					=> $f['option_needed'],
							));
							break;
						case 'select':
							$val = explode('||',$f['values']);
							$form->add_select_field(array(
								'name'						=> 'dyn_'.$f['namefield'],
								'label'						=> (isset($_SESSION['cste'][$f['titlefield']])?$_SESSION['cste'][$f['titlefield']]:$f['name']),
								'db_field'					=> $f['namefield'],
								'block'						=> 'dyn_'.$blockLabel,
								'mandatory'					=> $f['option_needed'],
								'options'					=> array_combine($val, $val),
							));
							break;
						case 'radio':
							$form->add_radio_field(array(
								'name'						=> 'dyn_'.$f['namefield'],
								'label'						=> (isset($_SESSION['cste'][$f['titlefield']])?$_SESSION['cste'][$f['titlefield']]:$f['name']),
								'block'						=> 'dyn_'.$blockLabel,
								'mandatory'					=> $f['option_needed'],
								'checked'					=> ($this->fields[$f['namefield']] == 'on'),
								'value'						=> 'on',
							));
							break;
					}
				}
			}
		}
	}
}
