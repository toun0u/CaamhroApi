<h2>
	<?php
	if($this->isNew()) echo $_SESSION['cste']['NEW_MODEL'];
	else echo $_SESSION['cste']['MODEL_EDITION']. ' <span class="object">'.$this->getLabel().'</span>';
	?>
</h2>
<div class="sub">
	<?php
	$form = new Dims\form(array(
		'object' 		=> $this,
		'action' 		=> dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=save_model',
		'back_url' 		=> dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=index',
		'continue'		=> true,
		'submit_value'	=> $_SESSION['cste']['SAVE_THIS_MODEL'],
		'enctype'		=> true
	));
	$form->add_hidden_field(array(
		'name'		=> 'id',
		'db_field'	=> 'id'
	));

	//TYPE
	$types = suivi_type::all();
	$options = array();
	foreach($types as $type){
		$options[$type->getId()] = $type->getLabel();
	}

	$form->add_select_field(array(
		'name'				=> 'model_id_type',
		'db_field'			=> 'id_type',
		'label'				=> $_SESSION['cste']['_TYPE'],
		'mandatory'			=> true,
		'options'			=> $options,
		'empty_message' 	=> $_SESSION['cste']['PLEASE_SELECT_A_TYPE']
	));
	//FICHIER
	$form->add_file_field(array(
		'name'				=> 'document',
		'label'				=> $_SESSION['cste']['MODEL'],
		'mandatory'			=> $this->isNew(),
		'revision'			=> 'ext:odt'
	));
	//LABEL
	$form->add_text_field(array(
		'name'				=> 'model_label',
		'db_field'			=> 'label',
		'label'				=> $_SESSION['cste']['_DIMS_LABEL'],
		'mandatory'			=> true
	));
	//DESCRIPTION
	$form->add_textarea_field(array(
		'name'				=> 'model_description',
		'db_field'			=> 'description',
		'label'				=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']
	));
	$form->build();
	?>
</div>
