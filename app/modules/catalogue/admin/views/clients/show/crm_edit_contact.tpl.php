<?php
$contact = $this->get('contact');

$id_tiers = $contact->getLightAttribute('id_tiers');
$function = $contact->getLightAttribute('function');

$idForm = 'edit_contact_'.$contact->get('id');
$img_add = _DESKTOP_TPL_PATH;

$lstUsed = array();
$myTags = $contact->getMyTags();

foreach ($myTags as $t) {
    $lstUsed[$t->get('id')] = $t->get('id');
}

$lstCateg = tag_category::getForObject(contact::MY_GLOBALOBJECT_CODE);
$optionsTags = array();
$addTagOptions = '<option value="0">'.$_SESSION['cste']['_UNCATEGORIZED'].'</option>';

foreach ($lstCateg as $cat) {
    $lstTag = $cat->getTagLink();
    if (count($lstTag)) {
        $opt = array();
        foreach ($lstTag as $tag) {
            $opt[$tag->get('id')] = str_replace("'", "\'", $tag->get('tag'));
        }
        $optionsTags[str_replace("'", "\'", $cat->get('label'))] = $opt;
    }
    $addTagOptions .= '<option value="'.$cat->get('id').'">'.str_replace("'", "\'", $cat->get('label')).'</option>';
}

$lstTag = tag::find_by(array('id_workspace' => $_SESSION['dims']['workspaceid'], 'id_category' => 0, 'type' => tag::TYPE_DEFAULT), ' ORDER BY tag ');
if (count($lstTag)) {
    $opt = array();
    foreach ($lstTag as $tag) {
        $opt[$tag->get('id')] = str_replace("'", "\'", $tag->get('tag'));
    }
    $optionsTags[$_SESSION['cste']['_UNCATEGORIZED']] = $opt;
}
$typeobj = contact::MY_GLOBALOBJECT_CODE;
$scriptenv = dims::getInstance()->getScriptEnv();
$js = <<<JS
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

	$('form#$idForm select#groups').chosen({width: "80%"}).parent().append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-group" />');
	$('form#$idForm').delegate('img.add-group','click',function(){
		$(this).parents('tr:first').after('<tr><td></td><td><input class="label-group-add" style="width:33%;" type="text" /><img style="cursor:pointer;" src="$img_add/gfx/contact/check16.png" class="group-valid" /><img style="cursor:pointer;" src="$img_add/gfx/contact/croix16.png" class="group-undo" /></td><td></td><td></td></tr>');
		$('input:last',$(this).parents('tr:first').next()).focus();
		$(this).remove();
	}).delegate('img.group-undo','click',function(){
		$('td.value_field:first',$(this).parents('tr:first').prev()).append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="group-tag" />');
		$(this).parents('tr:first').remove();
	}).delegate('img.group-valid','click',function(){
		var sel = $('form#$idForm select#groups').val(),
			value = $('input:last',$(this).parent()).val();
		$.ajax({
			type: "POST",
			url: '$scriptenv',
			data: {
				dims_op: 'desktopv2',
				action: 'add_new_group_ct',
				val: value,
			},
			dataType: 'html',
			success: function(data){
				var selected = $('form#$idForm select#groups').val();
				if(selected == null) selected = new Array();
				$('form#$idForm select#groups').html(data);
				var selected2 = $('form#$idForm select#groups').val();
				$('form#$idForm select#groups').val($.merge(selected,selected2)).trigger('liszt:updated');
			},
		});
		$('td.value_field:first',$(this).parents('tr:first').prev()).append('<img style="cursor:pointer;" src="$img_add/gfx/common/add.png" class="add-group" />');
		$(this).parents('tr:first').remove();
	}).delegate('input.label-group-add','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input.label-group-add','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$('form#$idForm img.group-valid').click();
		}
	});
JS;

$form = new Dims\form(array(
    'name' => $idForm,
    'object' => $contact,
    'action' => $contact->getLightAttribute('save_url'),
    'submit_value' => $_SESSION['cste']['_DIMS_SAVE'],
    'back_name' => (($contact->isNew() && ($id_tiers == '' || $id_tiers <= 0)) ? $_SESSION['cste']['REINITIALISER'] : $_SESSION['cste']['_DIMS_LABEL_CANCEL']),
    'back_url' => $contact->getLightAttribute('back_url'),
    'additional_js' => $js,
));

$default = $form->getBlock('default');
$default->setTitle(dims_constant::getVal('TAGS_AND_GROUPS'));

$form->add_select_field(array(
    'name' => 'tags[]',
    'id' => 'tags',
    'label' => $_SESSION['cste']['_DIMS_LABEL_TAGS'],
    'options' => $optionsTags,
    'value' => $lstUsed,
    'row' => 1,
    'col' => 1,
    'additionnal_attributes' => 'multiple="multiple" style="width:80%;"',
));

require_once DIMS_APP_PATH.'modules/system/class_ct_group.php';
$groups = ct_group::find_by(array('id_workspace' => $_SESSION['dims']['workspaceid']), ' ORDER BY label ');
$LstGr = $lstGrUsed = array();
foreach ($groups as $gr) {
    $LstGr[$gr->get('id')] = $gr->get('label');
}
require_once DIMS_APP_PATH.'modules/system/class_ct_group_link.php';
$groupsUsed = ct_group_link::find_by(array('id_globalobject' => $contact->get('id_globalobject'), 'type_contact' => contact::MY_GLOBALOBJECT_CODE));
foreach ($groupsUsed as $gr) {
    $lstGrUsed[$gr->get('id_group_ct')] = $gr->get('id_group_ct');
}
$form->add_select_field(array(
    'name' => 'groups[]',
    'id' => 'groups',
    'label' => $_SESSION['cste']['_GROUP'],
    'options' => $LstGr,
    'value' => $lstGrUsed,
    'row' => 2,
    'col' => 1,
    'additionnal_attributes' => 'multiple="multiple" style="width:80%;"',
));

if ($id_tiers != '' && $id_tiers > 0 && $contact->isNew()) {
    require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
    $tiers = tiers::find_by(array('id' => $id_tiers, 'id_workspace' => $_SESSION['dims']['workspaceid']), null, 1);
    if (!empty($tiers)) {
        $func = $form->addBlock('function', str_replace('{DIMS_TEXT}', $tiers->get('intitule'), $_SESSION['cste']['_FUNCTION_WITHIN_THIS_STRUCTURE']));
        $form->add_hidden_field(array(
            'name' => 'id_tiers',
            'value' => $id_tiers,
            'block' => 'function',
        ));

        // On est sur la fiche d'un contact
        $sel = 'SELECT 		DISTINCT function
				FROM 		'.tiersct::TABLE_NAME."
				WHERE 		function != ''
				AND 		id_workspace = :id_work
				GROUP BY 	function
				ORDER BY 	function";
        $params = array(
            ':id_work' => array('value' => $_SESSION['dims']['workspaceid'], 'type' => PDO::PARAM_INT),
        );
        $db = dims::getInstance()->getDb();
        $res = $db->query($sel, $params);
        $lstFct = array('' => '');
        while ($r = $db->fetchrow($res)) {
            $lstFct[trim($r['function'])] = trim($r['function']);
        }
        $form->add_select_field(array(
            'name' => 'function',
            'label' => $_SESSION['cste']['_DIMS_LABEL_FUNCTION'],
            'options' => $lstFct,
            'value' => $function,
            'mandatory' => true,
            'additionnal_attributes' => 'style="width:400px;"',
            'block' => 'function',
        ));

        $address = $form->addBlock('address', $_SESSION['cste']['_ADDRESS_OF_STRUCTURE']);
        $lstAdr = $tiers->getAllAdresses();
        $sel = true;
        $idArd = 0;
        foreach ($lstAdr as $type) {
            if (isset($type['add'])) {
                foreach ($type['add'] as $adr) {
                    $label = $adr->get('address');
                    if ($adr->get('address2') != '') {
                        $label .= '<br />'.$adr->get('address2');
                    }
                    if ($adr->get('address3') != '') {
                        $label .= '<br />'.$adr->get('address3');
                    }
                    $label .= '<br />'.$adr->get('postalcode');
                    $city = $adr->getCity();
                    $label .= ' '.$city->get('label');
                    $country = $adr->getCountry();
                    $label .= ' ('.$country->get('printable_name').')';
                    // TODO : CEDEX
                    $form->add_radio_field(array(
                        'name' => 'addresses',
                        'label' => $label,
                        'mandatory' => true,
                        'id' => 'radios['.$idArd.']',
                        'block' => 'address',
                        'checked' => $sel,
                        'value' => $adr->get('id'),
                    ));
                    if ($sel) {
                        $sel = false;
                    }
                    $idArd++;
                }
            }
        }
        $form->add_radio_field(array(
            'name' => 'addresses',
            'label' => $_SESSION['cste']['_CREATE_ADDRESS_LATER'],
            'mandatory' => true,
            'block' => 'address',
            'checked' => $sel,
            'id' => 'radios['.$idArd.']',
        ));
        ?>
		<script type="text/javascript">
			$(document).ready(function(){
				$('form#<?= $form->getId();
        ?> select[name="function"]').after('<img onclick="javascript:addFunction3(this);" src="<?= _DESKTOP_TPL_PATH;
        ?>/gfx/common/ajouter16.png" style="cursor:pointer;" />');
				$('form#<?= $form->getId();
        ?> select[name="function"]').chosen();
				$('form#<?= $form->getId();
        ?>').delegate('input.input-function-add','keydown',function(event){
					var keycode = event.keyCode;
					if(keycode == 13){ // enter
						event.preventDefault();
					}
				}).delegate('input.input-function-add','keyup',function(event){
					var keycode = event.keyCode;
					if(keycode == 13){ // enter
						event.preventDefault();
						validNewFunction3($(this));
					}
				});
			});
			if(window['addFunction3'] == undefined){
				window['addFunction3'] = function addFunction3(img){
					if($('tr.add_function', $(img).parents('table:first')).length <= 0){
						$(img).parents('tr:first').after('<tr class="add_function"><td></td><td colspan="3"><input class="input-function-add" type="text" style="width:350px;" /><img onclick="javascript:validNewFunction3(this);" src="<?= _DESKTOP_TPL_PATH;
        ?>/gfx/contact/check16.png" style="cursor:pointer;" /><img style="cursor:pointer;" onclick="javascript:$(this).parents(\'tr.add_function:first\').hide();" src="<?= _DESKTOP_TPL_PATH;
        ?>/gfx/contact/croix16.png" /></td></tr>')
					}
					$('tr.add_function input', $(img).parents('table:first')).val('');
					$('tr.add_function', $(img).parents('table:first')).show();
				}
			}
			if(window['validNewFunction3'] == undefined){
				window['validNewFunction3'] = function validNewFunction3(img){
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
		<?php

    }
}

// Réseaux sociaux
$rs = $form->addBlock('rs');
$rs->setTitle(ucfirst(strtolower($_SESSION['cste']['_SOCIAL_NETWORKS'])));
$form->add_text_field(array(
    'name' => 'ct_facebook',
    'label' => 'Facebook',
    'db_field' => 'facebook',
    'row' => 1,
    'col' => 1,
    'block' => 'rs',
));
$form->add_text_field(array(
    'name' => 'ct_twitter',
    'label' => 'Twitter',
    'db_field' => 'twitter',
    'row' => 1,
    'col' => 2,
    'block' => 'rs',
));
$form->add_text_field(array(
    'name' => 'ct_linkedin',
    'label' => 'Linkedin',
    'db_field' => 'linkedin',
    'row' => 2,
    'col' => 1,
    'block' => 'rs',
));
$form->add_text_field(array(
    'name' => 'ct_google_plus',
    'label' => 'Google+',
    'db_field' => 'google_plus',
    'row' => 2,
    'col' => 2,
    'block' => 'rs',
));
$form->add_text_field(array(
    'name' => 'ct_viadeo',
    'label' => 'Viadeo',
    'db_field' => 'viadeo',
    'row' => 3,
    'col' => 1,
    'block' => 'rs',
));

$form->build();
