<div class="admin_models">
	<h2>
		<?php
		if($model->isNew()) echo $_SESSION['cste']['NEW_MODEL'];
		else echo $_SESSION['cste']['MODEL_EDITION']. ' <span class="object">'.$model->getLabel().'</span>';
		?>
	</h2>
	<div class="sub">
		<?php
		$form = new Dims\form(array('object' 		=> $model,
							   'action' 		=> dims::getInstance()->getScriptEnv().'?models_action=save',
							   'back_url' 		=> dims::getInstance()->getScriptEnv().'?models_action=index',
							   'continue'		=> true,
							   'submit_value'	=> $_SESSION['cste']['SAVE_THIS_MODEL'],
							   'enctype'		=> true
							   ));
		//HIDDEN ID / Vu que y'a pas de globalobject, quand on est en édition
		if( ! $model->isNew() ){
			$form->add_hidden_field(array('name'		=> 'id_object',
										  'db_field'	=> 'id'
										));
		}
		//TYPE
		$options = array();
		foreach($types as $type){
			$options[$type->getId()] = $type->getLabel();
		}

		$form->add_select_field(array('name'			=> 'model_id_type',
									  'db_field'		=> 'id_type',
									  'label'			=> $_SESSION['cste']['_TYPE'],
									  'mandatory'		=> true,
									  'options'			=> $options,
									  'empty_message' 	=> $_SESSION['cste']['PLEASE_SELECT_A_TYPE']
									  ));
		//FICHIER
		$form->add_file_field(array('name'				=> 'document',
									'label'				=> $_SESSION['cste']['MODEL'],
									'mandatory'			=> $model->isNew(),
									'revision'			=> 'ext:odt'
									));
		//LABEL
		$form->add_text_field(array('name'				=> 'model_label',
									'db_field'			=> 'label',
									'label'				=> $_SESSION['cste']['_DIMS_LABEL'],
									'mandatory'			=> true
									));
		//DESCRIPTION
		$form->add_textarea_field(array('name'			=> 'model_description',
									'db_field'			=> 'description',
									'label'				=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']
									));
		$form->build();
		?>
	</div>
</div>