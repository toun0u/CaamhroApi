<?php
$function = $this->getLightAttribute('function');
$id_ct = $this->getLightAttribute('id_ct');
$type = $this->getLightAttribute('type');
$id = $id2 = $onCt2 = $back_url = "";
if(!$this->isNew())
	$id = "&id=".$this->get('id');
if($id_ct != '' && $id_ct > 0){
	switch ($type) {
		case contact::MY_GLOBALOBJECT_CODE:
			$id2 = "&id_ct=$id_ct";
			$onCt2 = "id_ct: ".$this->get('id_tiers').",";
			$back_url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$this->getLightAttribute('id_ct');
			break;
		case tiers::MY_GLOBALOBJECT_CODE:
			$id2 = "&id_tiers=$id_ct";
			$onCt2 = "id_tiers: ".$this->get('id_tiers').",";
			$back_url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$this->getLightAttribute('id_ct');
			break;
	}
}else{
	if($this->isNew()){
		$back_url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=new";
	}else{
		$back_url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$this->get('id');
	}
}

$idForm = 'edit_tiers_'.$this->get('id');
$img_add = _DESKTOP_TPL_PATH;

$lstUsed = array();
$myTags = $this->getMyTags();
foreach($myTags as $t)
	$lstUsed[$t->get('id')] = $t->get('id');
$lstCateg = tag_category::getForObject(tiers::MY_GLOBALOBJECT_CODE);
$optionsTags = array();
$addTagOptions = '<option value="0">'.$_SESSION['cste']['_UNCATEGORIZED'].'</option>';
foreach($lstCateg as $cat){
	$lstTag = $cat->getTagLink();
	if(count($lstTag)){
		$opt = array();
		foreach($lstTag as $tag){
			$opt[$tag->get('id')] = str_replace("'","\'",$tag->get('tag'));
		}
		$optionsTags[str_replace("'","\'",$cat->get('label'))] = $opt;
	}
	$addTagOptions .= '<option value="'.$cat->get('id').'">'.str_replace("'","\'",$cat->get('label')).'</option>';
}
$lstTag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'id_category'=>0, 'type'=>tag::TYPE_DEFAULT),' ORDER BY tag ');
if(count($lstTag)){
	$opt = array();
	foreach($lstTag as $tag){
		$opt[$tag->get('id')] = str_replace("'","\'",$tag->get('tag'));
	}
	$optionsTags[$_SESSION['cste']['_UNCATEGORIZED']] = $opt;
}
$typeobj = tiers::MY_GLOBALOBJECT_CODE;
$scriptenv = dims::getInstance()->getScriptEnv();

$js = $js2 = "";

$js .= <<<JS
	$('form#$idForm select#tags').chosen({width: "80%"}).parent().append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-tag" />');
	$('form#$idForm').delegate('img.add-tag','click',function(){
		$(this).parents('tr:first').after('<tr><td></td><td><select>$addTagOptions</select><input class="label-tag-add" style="width:33%;" type="text" /><img style="cursor:pointer;" src="$img_add/gfx/contact/check16.png" class="tag-valid" /><img style="cursor:pointer;" src="$img_add/gfx/contact/croix16.png" class="tag-undo" /></td><td></td><td></td></tr>');
		$('input:last',$(this).parents('tr:first').next()).focus();
		$(this).remove();
	}).delegate('img.tag-undo','click',function(){
		$('td.value_field:first',$(this).parents('tr:first').prev()).append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-tag" />');
		$(this).parents('tr:first').remove();
	}).delegate('img.tag-valid','click',function(){
		var sel = $('form#$idForm select#tags').val(),
			value = $('input:last',$(this).parent()).val(),
			id_cat = $('select:first',$(this).parent()).val();
		$.ajax({
			type: "POST",
			url: '$scriptenv',
			data: {
				dims_op: 'desktopv2',
				action: 'add_new_tag_categ',
				val: value,
				typeobj : $typeobj,
				id_cat: id_cat,
			},
			dataType: 'html',
			success: function(data){
				var selected = $('form#$idForm select#tags').val();
				if(selected == null) selected = new Array();
				$('form#$idForm select#tags').html(data);
				var selected2 = $('form#$idForm select#tags').val();
				$('form#$idForm select#tags').val($.merge(selected,selected2)).trigger('liszt:updated');
				$('select#tags').each(function(){
					if($(this).parents('form:first') != $('form#$idForm')){
						var selected = $(this).val();
						$(this).html(data).val(selected).trigger('liszt:updated');
					}
				});
			},
		});
		$('td.value_field:first',$(this).parents('tr:first').prev()).append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-tag" />');
		$(this).parents('tr:first').remove();
	}).delegate('input.label-tag-add','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input.label-tag-add','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$('form#$idForm img.tag-valid').click();
		}
	});
JS;

$form = new Dims\form(array(
	'name' 						=> $idForm,
	'object'					=> $this,
	'action'					=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=save".$id,
	'submit_value'				=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'					=> $_SESSION['cste']['_DIMS_CANCEL'],
	'back_url'					=> $back_url,
	'additional_js' 			=> $js,
));
$default = $form->getBlock('default');
$default->setTitle($_SESSION['cste']['_NEW_STRUCTURE']." / ".strtolower($_SESSION['cste']['_COMPANY_CT']));

$form->add_hidden_field(array(
	'name' 		=> 'type',
	'value' 	=> 0,
));

$row = 1;
$form->add_text_field(array(
	'name'		=> 'tiers_intitule',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_LABEL'],
	'db_field'	=> 'intitule',
	'mandatory'	=> true,
	'row'		=> $row,
	'col'		=> 1,
));
$form->add_text_field(array(
	'name'		=> 'tiers_abrege',
	'label'		=> $_SESSION['cste']['_ACRONYMS'],
	'db_field'	=> 'abrege',
	'row'		=> $row,
	'col'		=> 2,
));
$row++;

// if(!$this->isParent()){
// 	$lstTiers = tiers::find_by(array('id_tiers'=>0,'id_workspace'=>$_SESSION['dims']['workspaceid']), 'ORDER BY intitule');
// 	$lstParents = array();
// 	$lstParents[0] = '';
// 	foreach ($lstTiers as $t) {
// 		if($t->get('id') != $this->get('id')){
// 			$lstParents[$t->get('id')] = $t->get('intitule');
// 		}
// 	}
// 	if($this->isNew() && $type == tiers::MY_GLOBALOBJECT_CODE){
// 		$form->add_select_field(array(
// 			'name'						=> 'tiers_id_tiers',
// 			'label'						=> $_SESSION['cste']['_UNDER_SERVICE_OF'],
// 			'options'					=> $lstParents,
// 			'value' 					=> $id_ct,
// 			'additionnal_attributes'	=> 'style="width:400px;"',
// 			'row'						=> $row,
// 			'col'						=> 1,
// 		));
// 	}else{
// 		$form->add_select_field(array(
// 			'name'						=> 'tiers_id_tiers',
// 			'label'						=> $_SESSION['cste']['_UNDER_SERVICE_OF'],
// 			'options'					=> $lstParents,
// 			'db_field'					=> 'id_tiers',
// 			'additionnal_attributes'	=> 'style="width:400px;"',
// 			'row'						=> $row,
// 			'col'						=> 1,
// 		));
// 	}
// 	$row++;
// }
$form->add_file_field(array(
	'name'						=> 'photo',
	'label'						=> $_SESSION['cste']['_DIMS_LABEL_PHOTO'],
	'additionnal_attributes'	=> 'rev="ext:jpg,jpeg,png,gif"',
	'row'						=> $row,
	'col'						=> 1,
));
$row++;
$form->add_text_field(array(
	'name'		=> 'tiers_mel',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_EMAIL'],
	'db_field'	=> 'mel',
	'revision'	=> 'email',
	'mandatory'	=> false,
	'row'		=> $row,
	'col'		=> 1,
));
$form->add_text_field(array(
	'name'		=> 'tiers_site_web',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_ENT_WSITE'],
	'db_field'	=> 'site_web',
	'mandatory'	=> false,
	'row'		=> $row,
	'col'		=> 2,
));
$row++;
$form->add_text_field(array(
	'name'		=> 'tiers_telephone',
	'label'		=> $_SESSION['cste']['PHONE_NUMBER'],
	'db_field'	=> 'telephone',
	'mandatory'	=> false,
	'row'		=> $row,
	'col'		=> 1,
));
$form->add_text_field(array(
	'name'		=> 'tiers_telecopie',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_FAX'],
	'db_field'	=> 'telecopie',
	'row'		=> $row,
	'col'		=> 2,
));
$row++;

$form->add_select_field(array(
	'name'						=> 'tags[]',
	'id'						=> 'tags',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_TAGS'],
	'options'					=> $optionsTags,
	'value'						=> $lstUsed,
	'row'						=> $row,
	'col'						=> 1,
	'additionnal_attributes'	=> 'multiple="multiple" style="width:80%;"',
));
$row++;

$form->add_textarea_field(array(
	'name'		=> 'tiers_commentaire',
	'label'		=> $_SESSION['cste']['_DIMS_COMMENTS'],
	'db_field'	=> 'commentaire',
	'row'		=> $row,
	'col'		=> 1,
	'additionnal_attributes'	=> 'style="resize: none;"',
));

if(!empty($function) || $this->isNew()){
	require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
	$ct = contact::find_by(array('id'=>$id_ct,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
	if (!empty($ct)){
		$func = $form->addBlock('function',str_replace('{DIMS_TEXT}', $ct->get('firstname')." ".$ct->get('lastname'), $_SESSION['cste']['_FUNCTION_WITHIN_THIS_STRUCTURE']));
		$form->add_hidden_field(array(
			'name'						=> 'id_ct',
			'value'						=> $id_ct,
			'block'						=> 'function',
		));

		// On est sur la fiche d'un contact
		$sel = "SELECT 		DISTINCT function
				FROM 		".tiersct::TABLE_NAME."
				WHERE 		function != ''
				AND 		id_workspace = :id_work
				GROUP BY 	function
				ORDER BY 	function";
		$params = array(
			':id_work' => array('value'=>$_SESSION['dims']['workspaceid'], 'type'=>PDO::PARAM_INT),
		);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		$lstFct = array(''=>'');
		while($r = $db->fetchrow($res)){
			$lstFct[trim($r['function'])] = trim($r['function']);
		}
		$form->add_select_field(array(
			'name'						=> 'function',
			'label'						=> $_SESSION['cste']['_DIMS_LABEL_FUNCTION'],
			'options'					=> $lstFct,
			'value'						=> $function,
			'mandatory'					=> true,
			'additionnal_attributes'	=> 'style="width:400px;"',
			'block'						=> 'function',
		));

		$address = $form->addBlock('address',$_SESSION['cste']['_ADDRESS_OF_STRUCTURE']);
		$lstAdr = $ct->getAllAdresses();
		$sel = true;
		$idArd = 0;
		foreach($lstAdr as $type){
			if(isset($type['add'])){
				foreach($type['add'] as $adr){
					$label = $adr->get('address');
					if($adr->get('address2') != '')
						$label .= '<br />'.$adr->get('address2');
					if($adr->get('address3') != '')
						$label .= '<br />'.$adr->get('address3');
					$label .= '<br />'.$adr->get('postalcode');
					$city = $adr->getCity();
					$label .= " ".$city->get('label');
					$country = $adr->getCountry();
					$label .= " (".$country->get('printable_name').")";
					// TODO : CEDEX
					$form->add_radio_field(array(
						'name'						=> 'addresses',
						'label'						=> $label,
						'mandatory'					=> true,
						'id'						=> 'radios['.$idArd.']',
						'block'						=> 'address',
						'checked'					=> $sel,
						'value'						=> $adr->get('id'),
					));
					if($sel) $sel = false;
					$idArd++;
				}
			}
		}
		$form->add_radio_field(array(
			'name'						=> 'addresses',
			'label'						=> $_SESSION['cste']['_CREATE_ADDRESS_LATER'],
			'mandatory'					=> true,
			'block'						=> 'address',
			'checked'					=> $sel,
			'id'						=> 'radios['.$idArd.']',
		));
	}
}

include_once DIMS_APP_PATH."modules/system/desktopV2/templates/form_ct_tiers/shared/_edit_dynamic_fields.tpl.php";

$form->build();
?>
<script type="text/javascript">
	<?php if($this->isNew()){ ?>
		$("div.bloc_contact#linked_tiers").remove();
	<?php } ?>
	$(document).ready(function(){
		$('form#<?= $form->getId(); ?> select[name="function"]').after('<img onclick="javascript:addFunction(this);" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png" style="cursor:pointer;" />');
		$('form#<?= $form->getId(); ?> select[name="function"]').chosen();
		<?php if(!$this->isParent()){ ?>
			$('form#<?= $form->getId(); ?> select[name="tiers_id_tiers"]').chosen();
		<?php } ?>
		$('form#<?= $form->getId(); ?>').delegate('input.input-function-add','keydown',function(event){
			var keycode = event.keyCode;
			if(keycode == 13){ // enter
				event.preventDefault();
			}
		}).delegate('input.input-function-add','keyup',function(event){
			var keycode = event.keyCode;
			if(keycode == 13){ // enter
				event.preventDefault();
				validNewFunction($(this));
			}
		});
	});
	if(window['addFunction'] == undefined){
		window['addFunction'] = function addFunction(img){
			if($('tr.add_function', $(img).parents('table:first')).length <= 0){
				$(img).parents('tr:first').after('<tr class="add_function"><td></td><td colspan="3"><input class="input-function-add" type="text" style="width:350px;" /><img onclick="javascript:validNewFunction(this);" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/check16.png" style="cursor:pointer;" /><img style="cursor:pointer;" onclick="javascript:$(this).parents(\'tr.add_function:first\').hide();" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/croix16.png" /></td></tr>')
			}
			$('tr.add_function input', $(img).parents('table:first')).val('');
			$('tr.add_function', $(img).parents('table:first')).show();
		}
	}
	if(window['validNewFunction'] == undefined){
		window['validNewFunction'] = function validNewFunction(img){
			if($('input',$(img).parents('tr.add_function')).val() != ''){
				var opt = "<option value=\""+$('input',$(img).parents('tr.add_function')).val()+"\">"+$('input',$(img).parents('tr.add_function')).val()+"</option>";
				$('select[name="function"]').append(opt);
				$('select[name="function"]',$(img).parents('form:first')).val($('input',$(img).parents('tr.add_function')).val());
				$('select[name="function"]').trigger('liszt:updated');
				$('tr.add_function', $(img).parents('table:first')).hide();
			}
		}
	}
</script>
