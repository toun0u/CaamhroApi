<?php
global $field_types;
?>
<tr>
	<td colspan="7">
		<h4>
			<?php if($this->isNew()): ?>
				Cr&eacute;ation d'un champ : <?= $field_types[$this->get('type')]; ?>
			<?php else: ?>
				&Eacute;dition d'un champ : <?= $this->get('name')." (".$field_types[$this->get('type')].")"; ?>
			<?php endif; ?>
		</h4>
		<?php
		$f = new Dims\form(array(
			'name' 			=> 'field_edit_'.$this->get('id'),
			'object'		=> $this,
			'action'		=> form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'),'sa'=>'save')),
			'submit_value' 	=> $_SESSION['cste']['_DIMS_SAVE'],
			'back_url'		=> form\get_path(array('c'=>'form','a'=>'fields','id'=>$this->get('id_forms'))),
		));
		if(!$this->isNew()){
			$f->add_hidden_field(array(
				'name' 		=> 'idf',
				'db_field' 	=> 'id',
				'mandatory'	=> true,
			));
		}else{
			$f->add_hidden_field(array(
				'name' 		=> 'field_type',
				'db_field' 	=> 'type',
				'mandatory'	=> true,
			));
			$f->add_hidden_field(array(
				'name' 		=> 'field_position',
				'db_field' 	=> 'position',
				'mandatory'	=> true,
			));
		}
		$f->add_text_field(array(
			'name' 		=> 'field_name',
			'label'		=> $_SESSION['cste']['_FORMS_FIELD_NAME'],
			'db_field' 	=> 'name',
			'mandatory'	=> true,
		));
		$f->add_text_field(array(
			'name' 		=> 'field_fieldname',
			'label'		=> $_SESSION['cste']['_FIELD_FIELDNAME'],
			'db_field' 	=> 'fieldname',
			'mandatory'	=> false,
		));
		$f->add_textarea_field(array(
			'name' 		=> 'field_description',
			'label'		=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION'],
			'db_field' 	=> 'description',
			'mandatory'	=> false,
		));
		$f->add_checkbox_field(array(
			'name' 		=> 'field_option_needed',
			'label'		=> $_SESSION['cste']['_FIELD_NEEDED'],
			'db_field' 	=> 'option_needed',
			'value'		=> 1,
		));
		$f->add_checkbox_field(array(
			'name' 		=> 'field_option_exportview',
			'label'		=> $_SESSION['cste']['_FORMS_FIELD_EXPORTVIEW'],
			'db_field' 	=> 'option_exportview',
			'value'		=> 1,
		));
		switch ($this->get('type')) {
			default:
			case 'text':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_text.tpl.php';
				break;
			case 'textarea':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_textarea.tpl.php';
				break;
			case 'checkbox':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_checkbox.tpl.php';
				break;
			case 'radio':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_radio.tpl.php';
				break;
			case 'select':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_select.tpl.php';
				break;
			case 'file':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_file.tpl.php';
				break;
			case 'autoincrement':
				include_once DIMS_APP_PATH.'modules/forms/views/field/_autoincrement.tpl.php';
				break;
		}
		$f->build();
		?>
	</td>
</tr>