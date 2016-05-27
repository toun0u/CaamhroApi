<h2>
	<?php
	if($this->isNew()) echo "Nouveau type";
	else echo $_SESSION['cste']['SHORT_EDITION']. ' <span class="object">'.$this->get('label').'</span>';
	?>
</h2>
<div class="sub">
	<?php
	$form = new Dims\form(array(
		'object'        => $this,
		'action'        => dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=save_type',
		'back_url'      => dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=index',
		'continue'      => true,
		'submit_value'  => $_SESSION['cste']['_DIMS_SAVE'],
	));
	$form->add_hidden_field(array(
		'name'		=> 'id',
		'db_field'	=> 'id'
	));

	require_once DIMS_APP_PATH."modules/system/suivi/class_suivi.php";
	$types = suivi::find_by(array('id_type' => $this->get('id')), null, 1);
	if(empty($types) || $this->isNew()){
		$form->add_text_field(array(
			'name'				=> 'type_label',
			'db_field'			=> 'label',
			'label'				=> $_SESSION['cste']['_DIMS_LABEL'],
			'mandatory'			=> true,
		));

		if($this->getLightAttribute('hascatalogue')) {
			$form->add_select_field(array(
				'name'      => 'type_type_doc',
				'db_field'  => 'type_doc',
				'label'     => $_SESSION['cste']['DOCUMENT_TYPE_CONCERNED'],
				'mandatory' => true,
				'options'   => array(
					cata_facture::TYPE_FACTURE          => cata_facture::gettypelabel(cata_facture::TYPE_FACTURE),
					cata_facture::TYPE_QUOTATION        => cata_facture::gettypelabel(cata_facture::TYPE_QUOTATION),
					cata_facture::TYPE_PURCHASEORDER    => cata_facture::gettypelabel(cata_facture::TYPE_PURCHASEORDER),
					cata_facture::TYPE_DELIVERYORDER    => cata_facture::gettypelabel(cata_facture::TYPE_DELIVERYORDER),
					cata_facture::TYPE_ASSET            => cata_facture::gettypelabel(cata_facture::TYPE_ASSET),
				),
			));
		}
	}else{
		$form->add_simple_text(array(
			'name'				=> 'type_label',
			'db_field'			=> 'label',
			'label'				=> $_SESSION['cste']['_DIMS_LABEL'],
		));

		if($this->getLightAttribute('hascatalogue')) {
			$form->add_simple_text(array(
				'name'      => 'type_type_doc',
				'label'     => dims_constant::getVal('DOCUMENT_TYPE_CONCERNED'),
				'value'     => cata_facture::gettypelabel($this->fields['type_doc'])
			));
		}
	}
	/*$form->add_checkbox_field(array(
		'name'				=> 'type_public',
		'label'				=> $_SESSION['cste']['_PRIVATE'],
		'checked'			=> !$this->get('public'),
		'value'				=> 1,
	));*/
	$form->add_checkbox_field(array(
		'name'				=> 'type_status',
		'label'				=> $_SESSION['cste']['_DIMS_LABEL_ACTIVE'],
		'checked'			=> ($this->isNew() || $this->get('status')),
		'value'				=> 1,
	));
	$form->build();
	?>
</div>
