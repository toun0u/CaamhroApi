<?php
require_once DIMS_APP_PATH.'modules/system/class_tag.php';
require_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';
switch ($action) {
	default:
	case 'show':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/geo/display_geo_tag.tpl.php';
		break;
	case 'edit':
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_GEO),null,1);
		if(!empty($tag)){
			$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/admin/geo/edit_geo_tag.tpl.php');
		}else
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=geo");
		break;
	case 'save_label':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$label = dims_load_securvalue("label",dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_GEO),null,1);
		if(!empty($tag) && trim($label) != ''){
			$tag->set('tag',$label);
			$tag->save();
		}
		die();
		break;
	case 'save':
		$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'type_tag'=>tag_category::_TYPE_GEO),' ORDER BY label ');
		$idCateg = array(0=>0);
		foreach($categs as $c){
			$idCateg[$c->get('id')] = $c->get('id');
		}
		$tags = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_GEO), ' ORDER BY tag ');
		foreach($tags as $t){
			$idCat = dims_load_securvalue("obj_val_".$t->get('id'),dims_const::_DIMS_NUM_INPUT,true,true,true);
			if(in_array($idCat, $idCateg)){
				$t->set('id_category',$idCat);
				$t->save();
			}
		}
		$new_label = trim(dims_load_securvalue("new_label",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($new_label != ''){
			$tag = new tag();
			$tag->init_description();
			$tag->setugm();
			$tag->set('tag',$new_label);
			$tag->set('type',tag::TYPE_GEO);
			$tmp = dims_createtimestamp();
			$tag->set('timestp_modify',$tmp);
			$tag->set('timestp_create',$tmp);
			$new_obj = dims_load_securvalue("new_obj",dims_const::_DIMS_NUM_INPUT,true,true,true);
			if(in_array($new_obj, $idCateg)){
				$tag->set('id_category',$new_obj);
			}
			$tag->save();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=geo");
		break;
	case 'delete':
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_GEO),null,1);
		if(!empty($tag)){
			$tag->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=geo");
		break;
	case 'add_city':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$idc = dims_load_securvalue("idc",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_GEO),null,1);
		$c = city::find_by(array('id'=>$idc),null,1);
		if(!empty($tag) && !empty($c)){
			$lk = tag_globalobject::find_by(array('id_tag'=>$tag->get('id'),'id_globalobject'=>$c->get('id_globalobject')),null,1);
			if(empty($lk)){
				$lk = new tag_globalobject();
				$lk->init_description();
				$lk->set('id_tag',$tag->get('id'));
				$lk->set('id_globalobject',$c->get('id_globalobject'));
				$lk->set('timestp_modify',dims_createtimestamp());
				$lk->save();
			}
		}
		die();
		break;
	case 'del_city':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$idc = dims_load_securvalue("idc",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_GEO),null,1);
		$c = city::find_by(array('id'=>$idc),null,1);
		if(!empty($tag) && !empty($c)){
			$lk = tag_globalobject::find_by(array('id_tag'=>$tag->get('id'),'id_globalobject'=>$c->get('id_globalobject')),null,1);
			if(!empty($lk)){
				$lk->delete();
			}
		}
		die();
		break;
	case 'search_city':
		ob_clean();
		$id = dims_load_securvalue("id",dims_const::_DIMS_NUM_INPUT,true,true,true);
		$val = trim(dims_load_securvalue("val",dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if($val != ''){
			$tag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>$id, 'type'=>tag::TYPE_GEO),null,1);
			$lst = array();
			if(!empty($tag)){
				$lk = tag_globalobject::find_by(array('id_tag'=>$tag->get('id')));
				foreach($lk as $l){
					$lst[$l->get('id_globalobject')] = $l->get('id_globalobject');
				}
			}

			$db = dims::getInstance()->getDb();
			$reg = '/([0-9]+) ?- ?([0-9]+)/';
			if(preg_match($reg, $val, $matches) !== false && count($matches) == 3){
				$min = $matches[1];
				if(strlen($min) < 6){
					$lmin = 5-strlen($min);
					for($i=0;$i<$lmin;$i++)
						$min = $min."0";
				}
				$max = $matches[2];
				if(strlen($max) < 6){
					$lmax = 5-strlen($max);
					for($i=0;$i<$lmax;$i++)
						$max = $max."0";
				}
				$params = array(
					':min1'=>array('value'=>$min,'type'=>PDO::PARAM_STR),
					':min2'=>array('value'=>$min,'type'=>PDO::PARAM_STR),
					':max1'=>array('value'=>$max,'type'=>PDO::PARAM_STR),
					':max2'=>array('value'=>$max,'type'=>PDO::PARAM_STR),
				);
				$sel = "SELECT 		DISTINCT *
						FROM 		".city::TABLE_NAME."
						WHERE 		((insee >= :min1
						AND 		insee <= :max1)
						OR 			(cp >= :min2
						AND 		cp <= :max2))
						".(count($lst)?"AND id_globalobject NOT IN (".$db->getParamsFromArray($lst,"go",$params).")":"")."
						ORDER BY 	label";
				$res = $db->query($sel,$params);
				while($r = $db->fetchrow($res)){
					$city = new city();
					$city->openFromResultSet($r);
					$city->display(_DESKTOP_TPL_LOCAL_PATH.'/admin/geo/city_result_search.tpl.php');
				}
			}else{
				$params = array(
					':l'=>array('value'=>$val."%",'type'=>PDO::PARAM_STR),
					':i'=>array('value'=>$val."%",'type'=>PDO::PARAM_STR),
					':c'=>array('value'=>$val."%",'type'=>PDO::PARAM_STR),
				);
				$sel = "SELECT 		DISTINCT *
						FROM 		".city::TABLE_NAME."
						WHERE 		(label LIKE :l
						OR 			insee LIKE :i
						OR 			cp LIKE :c)
						".(count($lst)?"AND id_globalobject NOT IN (".$db->getParamsFromArray($lst,"go",$params).")":"")."
						ORDER BY 	label";
				$res = $db->query($sel,$params);
				while($r = $db->fetchrow($res)){
					$city = new city();
					$city->openFromResultSet($r);
					$city->display(_DESKTOP_TPL_LOCAL_PATH.'/admin/geo/city_result_search.tpl.php');
				}
			}
		}
		die();
		break;
}
