<?php
$id = "";
if(!$this->isNew())
	$id = "&id=".$this->get('id');
$formId = 'edit_address_'.$this->get('id');
$myCp = $this->get('cp');

$id_ct = $this->getLightAttribute('id_ct');
$typeObj = $this->getLightAttribute('type');
$js = "";
$no_res = addslashes($_SESSION['cste']['NO_RESULT']);
$add_it = addslashes($_SESSION['cste']['ADD_IT_LA']);
$img_add = _DESKTOP_TPL_PATH.'/gfx/common/add.png';
$desktop_path = _DESKTOP_TPL_PATH;
$scriptEnv = dims::getInstance()->getScriptEnv();
$js = <<<JS
	var prevV = '$myCp';
	$('input#adr_postalcode').focusout(function(){
		var v = jQuery.trim($(this).val());
		if(v.length >= 5 && v != prevV){
			$.ajax({
				url: '$scriptEnv',
				type: "POST",
				data: {
					'dims_op': 'desktopv2',
					'action': 'searchCity',
					'val': v,
					'id_country': $('select#adr_id_country').val(),
				},
				dataType: 'html',
				success: function(data) {
					$('select#adr_id_city').html(data).trigger("liszt:updated");
				},
			});
			prevV = v;
		}
	});

	$("form#$formId select.type-address")
		.chosen({no_results_text: "$no_res"})
		.parent().append('<img style="cursor:pointer;" src="$img_add" class="add-type-address" />');
	$("form#$formId").delegate('img.add-type-address','click',function(){
		var td = $(this).parent();
		$(this).replaceWith('<input type="text" class="add-type-adr" style="width:175px;" /><img style="cursor:pointer;" src="$desktop_path/gfx/contact/check16.png" class="type-address-valid" /><img style="cursor:pointer;" src="$desktop_path/gfx/contact/croix16.png" class="type-address-undo" />');
		$('input',td).focus();
	}).delegate('img.type-address-valid','click',function(){
		var td = $(this).parent(),
			valType = $('form#$formId input.add-type-adr').val();
		$.ajax({
			type: "POST",
			url: '$scriptEnv',
			data: {
				dims_op: 'desktopv2',
				action: 'add_new_type_addr',
				val: valType,
			},
			async: false,
			dataType: "json",
			success: function(data){
				var options = "";
				for(var i=0; i<data.length; i++){
					if(data[i]['selected'])
						options = options+'<option value="'+data[i]['go']+'" selected=true>'+data[i]['label']+'</option>';
					else
						options = options+'<option value="'+data[i]['go']+'">'+data[i]['label']+'</option>';
				}
				$('select.type-address',td).html(options).trigger("liszt:updated");
				$('img.type-address-valid',td).remove();
				$('input',td).remove();
				td.append('<img style="cursor:pointer;" src="$img_add" class="add-type-address" />');
				$('img.type-address-undo',td).remove();

				$('select.type-address').each(function(){
					if($(this).parent() != td){
						var val = $(this).val(),
							options = "";
						for(var i=0; i<data.length; i++){
							if(data[i]['go'] == val)
								options = options+'<option value="'+data[i]['go']+'" selected=true>'+data[i]['label']+'</option>';
							else
								options = options+'<option value="'+data[i]['go']+'">'+data[i]['label']+'</option>';
						}
						$(this).html(options).trigger("liszt:updated");
					}
				});
			}
		});
	}).delegate('img.type-address-undo','click',function(){
		$('img.type-address-valid',$(this).parent()).remove();
		$('input',$(this).parent()).remove();
		$(this).parent().append('<img style="cursor:pointer;" src="$img_add" class="add-type-address" />');
		$(this).remove();
	}).delegate('input.add-type-adr','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input.add-type-adr','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$("form#$formId img.type-address-valid").trigger('click');
		}
	});
JS;
if($this->isNew()){
	$addAddr = $_SESSION['cste']['ADD_ADDRESS'];
	$js .= <<<JS
	$('div#linked_address.bloc_contact').hide();
	$('form#$formId a.undo').click(function(){
		$('div#add_address').html('<a class="add" href="javascript:void(0);">$addAddr</a>');
		$('div#linked_address.bloc_contact').show();
	}).attr('href','javascript:void(0);');
JS;
}
$js .= <<<JS
	var tempo = null;
	if(window['refreshVille'] == undefined){
		window['refreshVille'] = function refreshVille(elem,id_country){
			tmp = $('div.chzn-search input',$(elem).parent('td:first')).val();
			if(jQuery.trim(tmp) != ''){
				$.ajax({
					url: '$scriptEnv',
					type: "POST",
					data: {
						'dims_op': 'desktopv2',
						'action': 'searchCity',
						'val': tmp,
						'id_country': id_country,
					},
					dataType: 'html',
					success: function(data) {
						$(elem).html(data).trigger("liszt:updated");
						$('div.chzn-search input',$(elem).parent('td:first')).focus().val(tmp);
					},
				});
			}
			clearInterval(tempo);
			tempo = null;
		}
	}
	$("form#$formId select#adr_id_city").chosen({
		allow_single_deselect:true,
		no_results_text: "$no_res"
	}).parent().append('<img src="$img_add" style="cursor:pointer;" class="add-city-address" />')
	.ready(function(){
		var idCountry = $('form#$formId select#adr_id_country').val();
		$('div.chzn-search input:first',$('form#$formId select#adr_id_city').parent('td:first')).keyup(function(event){
			idCountry = $('form#$formId select#adr_id_country').val();
			if(event.keyCode != null){
				if (event.keyCode != 16 && event.keyCode != 38 && event.keyCode != 40 && event.keyCode != 39 &&
					event.keyCode != 37 && event.keyCode != 20 && event.keyCode != 17 && event.keyCode != 18 &&
					event.keyCode != 13){
					if ($(this).val().length >= 2){
						if (tempo != null)
							clearInterval(tempo);
						tempo = setInterval("refreshVille('form#$formId select#adr_id_city',"+idCountry+")",1200);
					}
				}else if(event.keyCode == 13){
					if (tempo != null)
						clearInterval(tempo);
					tempo = null
					refreshVille('form#$formId select#adr_id_city',idCountry);
				}
			}
		});
	});
	$("form#$formId select#adr_id_city").change(function(){
		var option = $('option[value="'+$(this).val()+'"]',$(this));
		if(option.attr('dims-data-value') != undefined && option.attr('dims-data-value') != '' && option.attr('dims-data-value') != '0'){
			$('form#$formId input#adr_postalcode').val(option.attr('dims-data-value'));
		}
	});
	$("form#$formId").delegate('img.add-city-address','click',function(){
		var td = $(this).parent();
		$(this).replaceWith('<input type="text" style="width:175px;" class="adr-add-city" /><img style="cursor:pointer;" src="$desktop_path/gfx/contact/check16.png" class="city-address-valid" /><img style="cursor:pointer;" src="$desktop_path/gfx/contact/croix16.png" class="city-address-undo" />');
		$('input',td).focus();
	});
	$("form#$formId").delegate('img.city-address-valid','click',function(){
		var td = $(this).parent(),
			country = $("form#$formId select#adr_id_country").val(),
			value = $('input:last',$(this).parent()).val();
		$.ajax({
			type: "POST",
			url: '$scriptEnv',
			data: {
				dims_op: 'desktopv2',
				action: 'add_new_city_addr',
				val: value,
				id_country: country,
			},
			async: false,
			dataType: "json",
			success: function(data){
				var options = "";
				for(var i=0; i<data.length; i++){
					if(data[i]['selected'])
						options = options+'<option value="'+data[i]['id']+'" selected=true>'+data[i]['label']+'</option>';
					else
						options = options+'<option value="'+data[i]['id']+'">'+data[i]['label']+'</option>';
				}
				$('select#adr_id_city',td).html(options).trigger("liszt:updated");
				$('img.city-address-valid',td).remove();
				$('input',td).remove();
				td.append('<img style="cursor:pointer;" src="$img_add" class="add-city-address" />');
				$('img.city-address-undo',td).remove();

				$('select#adr_id_city').each(function(){
					if($(this).parent() != td && $('select#adr_id_country', $(this).parents('form:first')).val() == country){
						var val = $(this).val(),
							options = "";
						for(var i=0; i<data.length; i++){
							if(data[i]['id'] == val)
								options = options+'<option value="'+data[i]['id']+'" selected=true>'+data[i]['label']+'</option>';
							else
								options = options+'<option value="'+data[i]['id']+'">'+data[i]['label']+'</option>';
						}
						$(this).html(options).trigger("liszt:updated");
					}
				});
			}
		});
	}).delegate('img.city-address-undo','click',function(){
		$('img.city-address-valid',$(this).parent()).remove();
		$('input',$(this).parent()).remove();
		$(this).parent().append('<img style="cursor:pointer;" src="$img_add" class="add-city-address" />');
		$(this).remove();
	}).delegate('input.adr-add-city','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input.adr-add-city','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$("form#$formId img.city-address-valid").trigger('click');
		}
	});
	$("form#$formId select#adr_id_country")
		.chosen({no_results_text: "$no_res"})
		.change(function(){
			if($(this).val() != '') {
				$('form#$formId #adr_id_city').removeAttr('disabled');
			}
			else {
				$('form#$formId #adr_id_city').attr('disabled','disabled');
			}
			$('form#$formId select#adr_id_city').html('<option value=""></option>').trigger("liszt:updated");
			/*var value = $(this).val(),
				form = $("form#$formId");
			if(value != ''){
				$.ajax({
					type: "POST",
					url: '$scriptEnv',
					data: {
						dims_op: 'desktopv2',
						action: 'get_all_city_from',
						val: value
					},
					async: false,
					dataType: "json",
					success: function(data){
						var options = "";
						for(var i=0; i<data.length; i++){
							options = options+'<option value="'+data[i]['id']+'">'+data[i]['label']+'</option>';
						}
						$('select#adr_id_city',form).html(options).trigger("liszt:updated");
					},
				});
			}*/
	});
JS;
$form = new Dims\form(array(
	'name' 			=> $formId,
	'object'		=> $this,
	'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=address&action=save_address".$id,
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_CANCEL'],
	'back_url'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=address&action=edit_address".$id."&id_ct=".$this->getLightAttribute('id_ct')."&type=".$typeObj,
	'ajax_submit'	=> true,
	'ajax_undo'		=> !$this->isNew(),
	'additional_js'	=> $js,
));
$default = $form->getBlock('default');
$default->setTitle(ucfirst(strtolower($_SESSION['cste']['ADD_ADDRESS'])));

$form->add_hidden_field(array(
	'name' 		=> 'type',
	'value' 	=> $typeObj,
));

$typeAdd = address_type::all("WHERE is_active=1 AND id_workspace = :idwork", array(':idwork'=>$_SESSION['dims']['workspaceid']));
$lstTypes = array('dims_nan'=>'');
foreach($typeAdd as $add){
	$lstTypes[$add->get('id')] = $add->getLabel();
}
$obj = $lk = null;
if($id_ct != '' && $id_ct > 0){
	$form->add_hidden_field(array(
		'name' 		=> 'id_ct',
		'value' 	=> $id_ct,
	));
	switch ($typeObj) {
		case tiers::MY_GLOBALOBJECT_CODE:
			$obj = new tiers();
			$obj->open($id_ct);
			break;
		case contact::MY_GLOBALOBJECT_CODE:
			$obj = new contact();
			$obj->open($id_ct);
			break;
	}
}
$goTiers = $this->getLightAttribute('go_tiers');
if($goTiers != '' && $goTiers > 0){
	$form->add_hidden_field(array(
		'name' 		=> 'go_tiers',
		'value' 	=> $goTiers,
	));
}
if(!empty($obj)){
	$lk = $this->getLinkCt($obj->get('id_globalobject'));
}
$form->add_select_field(array(
	'name'						=> 'address_type',
	'label'						=> $_SESSION['cste']['_TYPE'],
	'value'						=> (!empty($lk))?$lk->get('id_type'):'',
	'row'						=> 1,
	'col'						=> 1,
	'options'					=> $lstTypes,
	'mandatory'					=> true,
	'additionnal_attributes'	=> 'class="type-address" style="width:175px;"',
));

$form->add_text_field(array(
	'name'		=> 'adr_address',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_ADDRESS'],
	'db_field'	=> 'address',
	'mandatory'	=> true,
	'row'		=> 2,
	'col'		=> 1,
));
$form->add_text_field(array(
	'name'		=> 'adr_address2',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_ADDRESS']." 2",
	'db_field'	=> 'address2',
	'row'		=> 3,
	'col'		=> 1,
));
$form->add_text_field(array(
	'name'		=> 'adr_address3',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_ADDRESS']." 3",
	'db_field'	=> 'address3',
	'row'		=> 4,
	'col'		=> 1,
));

$a_countries = country::getAllCountries();
$lstCountry = array(0=>'');
foreach($a_countries as $cc){
	$lstCountry[$cc->get('id')] = $cc->get('printable_name');
}

$lstCities = array(0=>'');
if($this->get('id_city') != '' && $this->get('id_city') > 0){
	$city = city::find_by(array('id'=>$this->get('id_city')),null,1);
	if(!empty($city)){
		$lstCities[$city->get('id')] = $city->get('label')." (".substr($city->get('insee'),0,2).")";
	}
}
$idCountry = $this->get('id_country');

$form->add_text_field(array(
	'name'						=> 'adr_postalcode',
	'label'						=> $_SESSION['cste']['_DIMS_LABEL_CP'],
	'db_field'					=> 'postalcode',
	'mandatory'					=> true,
	'row'						=> 5,
	'col'						=> 1,
	'additionnal_attributes'	=> 'rev="number"',
));

$form->add_select_field(array(
	'name'						=> 'adr_id_city',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_CITY'],
	'options'					=> $lstCities,
	'db_field'					=> 'id_city',
	'mandatory'					=> true,
	'row'						=> 5,
	'col'						=> 2,
	'additionnal_attributes'	=> 'style="width:250px;" '.(empty($idCountry)?'disabled="true"':""),
));

$form->add_text_field(array(
	'name'						=> 'adr_bp',
	'label'						=> "BP / CEDEX / ...",
	'db_field'					=> 'bp',
	'row'						=> 6,
	'col'						=> 1,
	'additionnal_attributes'	=> ' placeholder="BP 352, CEDEX 1, ..."'
));

$form->add_select_field(array(
	'name'						=> 'adr_id_country',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_COUNTRY'],
	'options'					=> $lstCountry,
	'db_field'					=> 'id_country',
	'mandatory'					=> true,
	'row'						=> 6,
	'col'						=> 2,
	'additionnal_attributes'	=> 'style="width:300px;"',
));

if($id_ct != '' && $id_ct > 0 && !empty($obj) && $typeObj == contact::MY_GLOBALOBJECT_CODE){
	$coord = $form->addBlock('coord');
	$coord->setTitle(ucfirst(strtolower($_SESSION['cste']['_DIMS_EVT_INFO_COMPL'])));
	$form->add_text_field(array(
		'name'						=> 'lk_phone',
		'label'						=> $_SESSION['cste']['_PHONE'],
		'value'						=> (!empty($lk))?$lk->get('phone'):'',
		'row'						=> 1,
		'col'						=> 1,
		'block'						=> 'coord',
	));
	$form->add_text_field(array(
		'name'						=> 'lk_email',
		'label'						=> $_SESSION['cste']['_DIRECTORY_EMAIL'],
		'value'						=> (!empty($lk))?$lk->get('email'):'',
		'row'						=> 2,
		'col'						=> 1,
		'additionnal_attributes'	=> 'rev="email"',
		'block'						=> 'coord',
	));
	$form->add_text_field(array(
		'name'						=> 'lk_fax',
		'label'						=> $_SESSION['cste']['_DIMS_LABEL_FAX'],
		'value'						=> (!empty($lk))?$lk->get('fax'):'',
		'row'						=> 3,
		'col'						=> 1,
		'block'						=> 'coord',
	));
}else{
	$coord = $form->addBlock('lk');
	$coord->setTitle(ucfirst(strtolower($_SESSION['cste']['_LINKS_CONTACTS'])));
	$form->add_checkbox_field(array(
		'name'						=> 'link_to_contacts',
		'label'						=> $_SESSION['cste']['_ASSOCIATE_ADDRESS_TO_ALL_CT_OF_STRUCTURE'],
		'value'						=> 1,
		'row'						=> 1,
		'col'						=> 1,
		'block'						=> 'lk',
		'checked'					=> $this->isNew(),
	));
}
$form->build();
?>