<?php
$view = view::getInstance();
$obj = $view->get('obj');

$cts = $obj->getCtLinks();
$alreadyCt = array();
$alreadyCtDiv = '';
foreach($cts as $ct){
	$photo = "/common/modules/invitation/contacts40.png";
	if(file_exists($ct->getPhotoPath(40))){
		$photo = $ct->getPhotoWebPath(40);
	}
	$alreadyCtDiv .= '<div dims-data-value="'.$ct->get('id_globalobject').'"><img src="'.$photo.'" /><span>'.$ct->get('firstname')." ".$ct->get('lastname').'</span><img class="delete" src="/common/modules/invitation/delete16.png" /></div>';
	$alreadyCt[] = $ct->get('id_globalobject');
}

$lstDates = $obj->getDatesLink();
$nbLoadedDates = count($lstDates)+1;
$labelDateStart = $_SESSION['cste']['_INFOS_START_DATE'];
$labelHourStart = $_SESSION['cste']['_DIMS_LABEL_HEUREDEB'];
$labelDateEnd = $_SESSION['cste']['_INFOS_END_DATE'];
$labelHourEnd = $_SESSION['cste']['_DIMS_LABEL_HEUREFIN'];

$dateStart = $_SESSION['cste']['_INFOS_START_DATE'];
$dateEnd = $_SESSION['cste']['_DIMS_LABEL_HEUREDEB'];

$addAll = $_SESSION['cste']['_DIMS_LABEL_ADD_ALL'];
$scriptEnv = dims::getInstance()->getScriptEnv();
$js = <<<JS
var tmpsearch = null;
var nbDatesLoaded = $nbLoadedDates;
window['searchCt'] = function searchCt(val){
	if(val != ''){
		$.ajax({
			url: '$scriptEnv',
			type: "POST",
			data: {
				'c': 'obj',
				'a': 'search_ct',
				'val': val,
				'not': $('input#contacts').val(),
			},
			dataType: 'json',
			success: function(data) {
				var res = "";
				if(data != undefined && data.length){
					for(i=0;i<data.length;i++){
						res += '<div dims-data-value="'+data[i]['id']+'"><img src="'+data[i]['img']+'" /><span>'+data[i]['val']+'</span><img class="add" src="/common/modules/invitation/add.png" /></div>';
					}
				}
				$('td.result-search').html(res);
			},
		});
		clearInterval(tmpsearch);
		tmpsearch = null;
	}
}
$(document).ready(function(){
	$('input.date-deb').datepicker({ 
		dateFormat: "dd/mm/yy",
		showOn: "both",
		minDate: 0,
		buttonImage: "/common/modules/invitation/planning16.png",
		buttonImageOnly: true,
		buttonText: "$dateStart",
		onClose: function(date){
			var id = $(this).attr('id').split('_');
			$('input.date-fin#date2_'+id[1]).datepicker("option", "minDate", date);
		},
	});
	$('input.date-fin').datepicker({ 
		dateFormat: "dd/mm/yy",
		showOn: "both",
		minDate: 0,
		buttonImage: "/common/modules/invitation/planning16.png",
		buttonImageOnly: true,
		buttonText: "$dateEnd",
	});
	$('input#rech_contact').parents('tr:first').after('<tr><td style="text-align:right;"><a href="javascript:void(0);" class="check-all-ct">$addAll</a></td><td class="result-search"></td></tr><tr><td></td><td class="added-ct">$alreadyCtDiv</td></tr>');
	$('a.check-all-ct').click(function(){
		$('td.result-search img.add').click();
	});
	$('input#rech_contact').keydown(function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).keyup(function(event){
		var keycode = event.keyCode;
		if (tmpsearch != null)
			clearInterval(tmpsearch);
		if(keycode == 13){ // enter
			event.preventDefault();
			searchCt($(this).val());
		}else{
			tmpsearch = setInterval("searchCt('"+$(this).val()+"')",1200);
		}
	});
	$('form table td.result-search').delegate('div img.add', 'click', function(){
		$('input#contacts').val($('input#contacts').val()+";"+$(this).parents('div:first').attr('dims-data-value'));
		$(this).attr({'class':'delete', 'src':'/common/modules/invitation/delete16.png'});
		$('form table td.added-ct').append($(this).parents('div:first'));
	});
	$('form table td.added-ct').delegate('div img.delete', 'click', function(){
		$('input#contacts').val($('input#contacts').val().replace(';'+$(this).parents('div:first').attr('dims-data-value'),''));
		$(this).parents('div:first').remove();
		searchCt($('input#rech_contact').val());
	});
	$('form table td select.heure-fin').each(function(){
		$('td',$(this).parents('tr:first').next('tr:first')).css('border-bottom','1px solid #C0C0C0');
	});
	$('form table td input.date-deb').after('<img src="/common/modules/invitation/supprimer16.png" class="supp-date" />');
	$('form table td').delegate('img.supp-date','click',function(){
		if($('form table td img.supp-date').length-1 == $('form table td img.supp-date').index(this)){
			$('form table td input.date-deb:last').val("");
			$('form table td select.heure-deb:last').val("");
			$('form table td input.date-fin:last').val("").datepicker("option", "minDate", 0);
			$('form table td select.heure-fin:last').val("");
		}else{
			var ind = $('form table td img.supp-date').index(this);
			$('form table td input.date-deb').eq(ind).parents('tr:first').next('tr:first').remove();
			$('form table td input.date-deb').eq(ind).parents('tr:first').remove();
			$('form table td select.heure-deb').eq(ind).parents('tr:first').next('tr:first').remove();
			$('form table td select.heure-deb').eq(ind).parents('tr:first').remove();
			$('form table td input.date-fin').eq(ind).parents('tr:first').next('tr:first').remove();
			$('form table td input.date-fin').eq(ind).parents('tr:first').remove();
			$('form table td select.heure-fin').eq(ind).parents('tr:first').next('tr:first').remove();
			$('form table td select.heure-fin').eq(ind).parents('tr:first').remove();
			$('form input.dates').eq(ind).remove();
		}
	});
	$('form div#default table').append('<tr class="tr-add-date"><td colspan="2" style="text-align:right;"><img class="add-date" src="/common/modules/invitation/add.png" style="cursor:pointer;" /></td></tr>');
	$('form table').delegate('img.add-date','click',function(){
		var last = $('form div#default table tr').eq($('form div#default table tr').length-2).clone();
		var prevLast = $('form div#default table tr').eq($('form div#default table tr').length-3).clone();
		$('select.heure-fin:last',$(prevLast)).val("").attr('id','heure2_'+nbDatesLoaded);
		$('td.label_field:first',$(prevLast)).html('<label for="heure2_'+nbDatesLoaded+'">$labelHourEnd '+(nbDatesLoaded+1)+'</label>');

		var last2 = $('form div#default table tr').eq($('form div#default table tr').length-4).clone();
		var prevLast2 = $('form div#default table tr').eq($('form div#default table tr').length-5).clone();
		$('img.ui-datepicker-trigger',$(prevLast2)).remove();
		$('input.date-fin',$(prevLast2)).val("").attr({'id':'date2_'+nbDatesLoaded, 'class':'date-fin'}).datepicker({ 
			dateFormat: "dd/mm/yy",
			showOn: "both",
			minDate: 0,
			buttonImage: "/common/modules/invitation/planning16.png",
			buttonImageOnly: true,
			buttonText: "$dateEnd",
		});
		$('td.label_field:first',$(prevLast2)).html('<label for="date2_'+nbDatesLoaded+'">$labelDateEnd '+(nbDatesLoaded+1)+'</label>');

		var last3 = $('form div#default table tr').eq($('form div#default table tr').length-6).clone();
		var prevLast3 = $('form div#default table tr').eq($('form div#default table tr').length-7).clone();
		$('select.heure-deb:last',$(prevLast3)).val("").attr('id','heure1_'+nbDatesLoaded);
		$('td.label_field:first',$(prevLast3)).html('<label for="heure1_'+nbDatesLoaded+'">$labelHourStart '+(nbDatesLoaded+1)+'</label>');

		var last4 = $('form div#default table tr').eq($('form div#default table tr').length-8).clone();
		var prevLast4 = $('form div#default table tr').eq($('form div#default table tr').length-9).clone();
		$('img.supp-date',$(prevLast4)).click(function(){
			if($('form table td img.supp-date').length-1 == $('form table td img.supp-date').index(this)){
				$('form table td input.date-deb:last').val("");
				$('form table td select.heure-deb:last').val("");
				$('form table td input.date-fin:last').val("").datepicker("option", "minDate", 0);
				$('form table td select.heure-fin:last').val("");
			}else{
				var ind = $('form table td img.supp-date').index(this);
				$('form table td input.date-deb').eq(ind).parents('tr:first').next('tr:first').remove();
				$('form table td input.date-deb').eq(ind).parents('tr:first').remove();
				$('form table td select.heure-deb').eq(ind).parents('tr:first').next('tr:first').remove();
				$('form table td select.heure-deb').eq(ind).parents('tr:first').remove();
				$('form table td input.date-fin').eq(ind).parents('tr:first').next('tr:first').remove();
				$('form table td input.date-fin').eq(ind).parents('tr:first').remove();
				$('form table td select.heure-fin').eq(ind).parents('tr:first').next('tr:first').remove();
				$('form table td select.heure-fin').eq(ind).parents('tr:first').remove();
				$('form input.dates').eq(ind).remove();
			}
		});
		$('img.ui-datepicker-trigger',$(prevLast4)).remove();
		$('input.date-deb',$(prevLast4)).val("").attr({'id':'date1_'+nbDatesLoaded, 'class':'date-deb'}).datepicker({ 
			dateFormat: "dd/mm/yy",
			showOn: "both",
			minDate: 0,
			buttonImage: "/common/modules/invitation/planning16.png",
			buttonImageOnly: true,
			buttonText: "$dateStart",
			onClose: function(date){
				var id = $(this).attr('id').split('_');
				$('input.date-fin#date2_'+id[1]).datepicker("option", "minDate", date);
			},
		});
		$('td.label_field:first',$(prevLast4)).html('<label for="date1_'+nbDatesLoaded+'">$labelDateStart '+(nbDatesLoaded+1)+'</label>');

		$('form div#default table .tr-add-date').before(prevLast4,[last4,prevLast3,last3,prevLast2,last2,prevLast,last]);
		nbDatesLoaded++;
	});
});
JS;

$form = new Dims\form(array(
	'name' 			=> "invitation",
	'object'		=> $obj,
	'action'		=> dims::getInstance()->getScriptEnv()."?c=obj&a=save",
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> (!$obj->isNew())?$_SESSION['cste']['REINITIALISER']:$_SESSION['cste']['_DIMS_LABEL_CANCEL'],
	'back_url'		=> dims::getInstance()->getScriptEnv().($obj->isNew()?'?c=list&a=view':('?c=obj&a=view&id='.$obj->get('id'))),
	'additional_js'	=> $js,
));
$default = $form->getBlock('default');

$form->add_text_field(array(
	'name'		=> 'obj_libelle',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_LABEL'],
	'db_field'	=> 'libelle',
	'mandatory'	=> true,
));

$form->add_textarea_field(array(
	'name'		=> 'obj_description',
	'label'		=> $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION'],
	'db_field'	=> 'description',
));

$id = 0;
$heures = array();
for($i=0;$i<24;$i++){
	for($y=0;$y<60;$y+=15){
		$c = ((strlen("$i")==1)?"0$i":"$i").":".((strlen("$y")==1)?"0$y":"$y");
		$heures[$c] = $c;
	}
}

foreach($lstDates as $d){
	$form->add_hidden_field(array(
		'name'						=> 'dates[]',
		'value'						=> $d->get('id'),
		'additionnal_attributes'	=> 'class="dates"',
	));
	$form->add_text_field(array(
		'name'						=> 'date1[]',
		'label'						=> $_SESSION['cste']['_INFOS_START_DATE']." ".($id+1),
		'id'						=> 'date1_'.$id,
		'revision'					=> 'date_jj/mm/yyyy',
		'additionnal_attributes'	=> 'style="width:75px;" class="date-deb"',
		'value'						=> implode('/',array_reverse(explode('-',$d->get('datejour')))),
	));
	$form->add_select_field(array(
		'options'					=> $heures,
		'name'						=> 'heure1[]',
		'label'						=> $_SESSION['cste']['_DIMS_LABEL_HEUREDEB']." ".($id+1),
		'id'						=> 'heure1_'.$id,
		'revision'					=> 'heure_hh:mm',
		'additionnal_attributes'	=> 'style="width:75px;" class="heure-deb"',
		'value'						=> substr($d->get('heuredeb'), 0,-3),
	));
	$form->add_text_field(array(
		'name'						=> 'date2[]',
		'label'						=> $_SESSION['cste']['_INFOS_END_DATE']." ".($id+1),
		'id'						=> 'date2_'.$id,
		'revision'					=> 'date_jj/mm/yyyy',
		'additionnal_attributes'	=> 'style="width:75px;" class="date-fin"',
		'value'						=> implode('/',array_reverse(explode('-',$d->get('datefin')))),
	));
	$form->add_select_field(array(
		'options'					=> $heures,
		'name'						=> 'heure2[]',
		'label'						=> $_SESSION['cste']['_DIMS_LABEL_HEUREFIN']." ".($id+1),
		'id'						=> 'heure2_'.$id,
		'revision'					=> 'heure_hh:mm',
		'additionnal_attributes'	=> 'style="width:75px;" class="heure-fin"',
		'value'						=> substr($d->get('heurefin'), 0,-3),
	));

	$id++;
}

$form->add_text_field(array(
	'name'						=> 'date1[]',
	'label'						=> $_SESSION['cste']['_INFOS_START_DATE']." ".($id+1),
	'id'						=> 'date1_'.$id,
	'revision'					=> 'date_jj/mm/yyyy',
	'additionnal_attributes'	=> 'style="width:75px;" class="date-deb"',
));
$form->add_select_field(array(
	'options'					=> $heures,
	'name'						=> 'heure1[]',
	'label'						=> $_SESSION['cste']['_DIMS_LABEL_HEUREDEB']." ".($id+1),
	'id'						=> 'heure1_'.$id,
	'revision'					=> 'heure_hh:mm',
	'additionnal_attributes'	=> 'style="width:75px;" class="heure-deb"',
));
$form->add_text_field(array(
	'name'						=> 'date2[]',
	'label'						=> $_SESSION['cste']['_INFOS_END_DATE']." ".($id+1),
	'id'						=> 'date2_'.$id,
	'revision'					=> 'date_jj/mm/yyyy',
	'additionnal_attributes'	=> 'style="width:75px;" class="date-fin"',
));
$form->add_select_field(array(
	'options'					=> $heures,
	'name'						=> 'heure2[]',
	'label'						=> $_SESSION['cste']['_DIMS_LABEL_HEUREFIN']." ".($id+1),
	'id'						=> 'heure2_'.$id,
	'revision'					=> 'heure_hh:mm',
	'additionnal_attributes'	=> 'style="width:75px;" class="heure-fin"',
));

$contact = $form->addBlock('contact',$_SESSION['cste']['_DIMS_LABEL_CONTACTS']);

$form->add_hidden_field(array(
	'name'						=> 'contacts',
	'id'						=> 'contacts',
	'value'						=> ';'.implode(';',$alreadyCt),
	'block'						=> 'contact',
));
$form->add_text_field(array(
	'label'						=> $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT'],
	'id'						=> 'rech_contact',
	'block'						=> 'contact',
));

$form->build();
