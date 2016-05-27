<?php
$view = view::getInstance();
switch ($sa) {
	default:
	case 'index':
		$view->render('form/fields.tpl.php');
		break;
	case 'edit':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		if(empty($field)){
			$field = new field();
			$field->init_description();
			$field->set('id_forms',$form->get('id'));
			$field->set('cols',1);
			$field->set('type',dims_load_securvalue('t',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$field->set('position',dims_load_securvalue('p',dims_const::_DIMS_NUM_INPUT,true,true,true));
		}
		$view->assign('edit',$field);
		$view->render('form/fields.tpl.php');
		break;
	case 'save':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		$isNew = false;
		if(empty($field)){
			$field = new field();
			$field->init_description();
			$field->set('id_forms',$form->get('id'));
			$isNew = true;
		}
		$field->fields['option_needed']				= 0;
		$field->fields['option_arrayview']			= 0;
		$field->fields['option_exportview']			= 0;
		$field->fields['option_cmsgroupby']			= 0;
		$field->fields['option_cmsdisplaylabel']	= 0;
		$field->fields['option_cmsshowfilter']		= 0;

		$field->setvalues($_POST,'field_');
		if($field->save()){
			$form->fields['nb_fields'] ++;
			$form->save();
			if($isNew){
				$view->flash("La création s'est effectuée avec succès", 'bg-success');
			}else{
				$view->flash("La modification s'est effectuée avec succès", 'bg-success');
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'))));
		break;
	case 'delete':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		if(!empty($field)){
			$form->fields['nb_fields'] --;
			$form->save();
			$field->delete();
			$view->flash("La suppression s'est effectuée avec succès", 'bg-success');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'))));
		break;
	case 'down':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		if(!empty($field)){
			$field->downField();
			$view->flash("La modification s'est effectuée avec succès", 'bg-success');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'))));
		break;
	case 'up':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		if(!empty($field)){
			$field->upField();
			$view->flash("La modification s'est effectuée avec succès", 'bg-success');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'))));
		break;
	case 'toggle_required':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		if(!empty($field)){
			$field->fields['option_needed'] = !$field->fields['option_needed'];
			$field->save();
			$view->flash("La modification s'est effectuée avec succès", 'bg-success');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'))));
		break;
	case 'toggle_export':
		$idf = dims_load_securvalue('idf',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$field = field::find_by(array('id'=>$idf,'id_forms'=>$form->get('id')),null,1);
		if(!empty($field)){
			$field->fields['option_exportview'] = !$field->fields['option_exportview'];
			$field->save();
			$view->flash("La modification s'est effectuée avec succès", 'bg-success');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(form\get_path(array('c'=>'form','a'=>'fields','id'=>$form->get('id'))));
		break;
}
