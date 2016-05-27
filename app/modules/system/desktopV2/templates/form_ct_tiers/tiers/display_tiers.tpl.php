<style type="text/css">
div.zone_address_book a{
	text-decoration: underline !important;
}
div.zone_address_book{
	margin-right: 30px;
}
</style>
<?php
global $dims_agenda_months;
$missingInfos = array(
	'phone'=>false,
	'adr'=>false,
);
?>
<table style="width:100%;">
	<tr>
		<td style="width:110px;vertical-align:top;" rowspan="<?= (trim($this->get('commentaire')) != '')?5:4; ?>">
			<?php
			$file = $this->getPhotoPath(100);//real_path
			if(file_exists($file)){
				?>
				<img class="picture" src="<?= $this->getPhotoWebPath(100); ?>">
				<?php
			}
			else{
				?>
				<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/company100.png">
				<?php
			}
			?>
		</td>
		<th style="font-size:15px;height:20px;border:0px;text-align:left;">
			<?= (($this->get('id_tiers')>0)?$_SESSION['cste']['_SERVICE_SHEET']:$_SESSION['cste']['_SHEET_STRUCTURE'])." : ".$this->get("intitule"); ?>
			<?php
			if($this->get('id_tiers') != '' && $this->get('id_tiers') > 0){
				$parent = tiers::find_by(array('id'=>$this->get('id_tiers'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(!empty($parent)){
					echo " (<a href=\"".dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$parent->get('id')."\">".$parent->get('intitule').'</a>)';
				}
			}
			?>
		</th>
	</tr>
	<tr>
		<td style="vertical-align:top;">
		<?php
		if (dims::getInstance()->isModuleTypeEnabled('catalogue')) {
			include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
			$catalogueclient = current(client::find_by(array('tiers_id' => $this->getId())));
			if(!empty($catalogueclient)) {
				?>
				<div class="customerCard">
					<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>">
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/fleche_result.png" alt="<?= dims_constant::getVal('CUSTOMER_CARD'); ?>" title="<?= dims_constant::getVal('CUSTOMER_CARD'); ?>" />
						<?= dims_constant::getVal('CUSTOMER_CARD'); ?>
					</a>
					<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>&sc=quotations">
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/quotation.png" alt="<?= dims_constant::getVal('QUOTATION'); ?>" title="<?= dims_constant::getVal('QUOTATION'); ?>" />
						<?= dims_constant::getVal('QUOTATION'); ?>
					</a>
					<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>&sc=quotations&sa=new">
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/quotation_add.png" alt="<?= dims_constant::getVal('ADD_QUOTATION'); ?>" title="<?= dims_constant::getVal('ADD_QUOTATION'); ?>" />
						<?= dims_constant::getVal('ADD_QUOTATION'); ?>
					</a>
				</div>
				<?php
			}
		}
		?>
		</td>
	</tr>
	<?php if(trim($this->get('commentaire')) != ''){ ?>
	<tr>
		<td style="vertical-align:top;">
			<?= nl2br(trim($this->get('commentaire'))); ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td style="height:15px;">
			<?php
			$infos = array();
			if($this->get('mel') != '')
				$infos[] = '<a href="mailto:'.$this->get('mel').'">'.$this->get('mel').'</a>';
			if($this->get('telephone') != '')
				$infos[] ="<span data-phone=".$this->get('telephone')." data-callname=".$this->get('intitule').">".$this->get('telephone')."(Tél)</span>";
			else{
				$missingInfos['phone'] = true;
			}
			if($this->get('telecopie') != '')
				$infos[] = $this->get('telecopie')." (Fax)";
			print implode(' - ',$infos);
			?>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top;">
			<?php
			$myTags = $this->getMyTags(tag::TYPE_DEFAULT);
			foreach($myTags as $t){
				?>
				<span class="tag" dims-data-value="<?= $t->get('id'); ?>">
					<?= $t->get('tag'); ?>
				</span>
				<?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-bottom:10px;">
			<?php
			$tagsTmp = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION), ' ORDER BY tag ');
			foreach($tagsTmp as $t){
				$months = array();
				$years = $t->getYearsTiers($this->get('id_globalobject'),$months);
				$titleTmp = $t->get('tag');
				if(count($months)){
					foreach($months as $y => $m)
						$titleTmp .= "\n - ".$dims_agenda_months[$m]." $y";
				}
				?>
				<span class="tag-tmp" dims-undo-years="<?= implode(',',$years); ?>" dims-data-value="<?= $t->get('id'); ?>" title="<?= $titleTmp; ?>">
					<?= $t->get('tag').(count($years)?" (".implode('/',$years).")":""); ?>
					<input type="hidden" />
				</span>
				<?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="actions">
			<input class="edit" type="button" value="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=edit&id=".$this->get('id'); ?>';" />
			<input class="delete" type="button" value="<?= $_SESSION['cste']['_DELETE']; ?>" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=company&action=delete&id=<?= $this->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');" />
		</td>
	</tr>
</table>

<?php
// Champs dynamiques
$this->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/_dynamic_fields.tpl.php');
?>

<h2 class="contact">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/structures.png" />
	<span>
		<?= $_SESSION['cste']['_SUB_SERVICES']; ?>
	</span>
</h2>
<div id="add_tiers">
	<a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['_ADD_SUB_SERVICES']; ?>
	</a>
</div>
<div id="linked_tiers" class="bloc_contact">
	<?php
	$lstComp = tiers::find_by(array('id_tiers'=>$this->get('id')), ' ORDER BY intitule ');
	if(count($lstComp)){
		foreach($lstComp as $tiers){
			$tiers->setLightAttribute('mode','company');
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/tiers/display_mini_tiers.tpl.php');
			/*if($this->getLightAttribute('adr') == $tiers->get('id')){
				$address = new address();
				$address->init_description();
				$address->setLightAttribute('go_tiers',$tiers->get('id_globalobject'));
				$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/edit_address.tpl.php');
			}*/
		}
	}else{
		echo $_SESSION['cste']['_NO_STRUCTURE_AT_THE_MOMENT'];
	}
	?>
</div>

<h2 class="contact">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human_picto.png" />
	<span>
		<?= $_SESSION['cste']['_DIMS_LABEL_CONTACTS']; ?>
	</span>
</h2>
<div id="add_contact">
	<a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['_ADD_CT']; ?>
	</a>
</div>
<div id="linked_contact" class="bloc_contact">
	<?php
	$lstCt = $this->getAllContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
	if(count($lstCt)){
		foreach($lstCt as $ct){
			$ct->setLightAttribute('mode','company');
			$ct->setLightAttribute('id_tiers',$this->get('id'));
			$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/display_mini_contact.tpl.php');
		}
	}else{
		echo $_SESSION['cste']['_DIMS_LABEL_NO_CT_ATTACHED'];
	}
	?>
</div>

<h2 class="contact">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/adresses.png" />
	<span>
		<?= $_SESSION['cste']['_ADDRESSES']; ?>
	</span>
</h2>
<div id="add_address">
	<a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['ADD_ADDRESS']; ?>
	</a>
</div>
<div id="linked_address" class="bloc_contact">
	<?php
	$addresses = address::getAddressesFromGo($this->get('id_globalobject'));
	if(count($addresses)){
		foreach($addresses as $addr){
			$addr->setLightAttribute('mode','company');
			$addr->setLightAttribute('go_parent',$this->get('id_globalobject'));
			$addr->setLightAttribute('id_ct',$this->get('id'));
			$addr->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/display_mini_address.tpl.php');
		}
	}else{
		echo $_SESSION['cste']['_NO_ADDRESS_FOR_THE_MOMENT'];
		$missingInfos['adr'] = true;
	}
	?>
</div>

<?php
if(empty($catalogueclient)) {
	//include _DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/suivi/list_suivi.tpl.php';
}else{
	// on a le catalogue
	$catalogueclient->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/cata_facture/list.tpl.php');
}
?>

<h2 class="contact">
	<balise id="doc">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/documents.png" />
		<span>
			<?= $_SESSION['cste']['_DOCS']; ?>
		</span>
	</balise>
</h2>
<div id="add_document">
	<a class="add" href="javascript:void(0);">
		<?= ucfirst(strtolower($_SESSION['cste']['_ADD_DOCUMENTS'])); ?>
	</a>
</div>
<div id="linked_doc" class="bloc_contact">
	<?php
	require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
	$id_folder = $this->initFolder();

	$folder = new docfolder();
	$folder->open($id_folder);
	$folder->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=add_file&id_tiers=".$this->get('id'));
	$folder->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$this->get('id'));
	$folder->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/doc/add_doc.tpl.php');

	$childs = docfolder::find_by(array('id_folder'=>$id_folder));
	$lstDocFolder = array($id_folder);
	foreach($childs as $child){
		$lstDocFolder[] = $child->get('id');
	}

	$documents = docfile::find_by(array('id_folder'=>$lstDocFolder),' ORDER BY timestp_modify DESC ');
	if(count($documents)){
		$i = 1;
		$nbMore = 0;
		$nbDoc = count($documents);
		foreach($documents as $doc){
			if($i == 1){
				?>
				<div class="display-more" <?= ($nbMore == 0)?'':'style="display:none;"'; ?>>
				<?php
				$nbMore++;
			}
			$doc->setLightAttribute('folders',$childs);
			$doc->setLightAttribute('mode','company');
			$doc->setLightAttribute('id_ct',$this->get('id'));
			$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/doc/display_mini_doc.tpl.php');
			if($i == 5){
				$i = 1;
				?>
				</div>
				<?php
			}else
				$i++;
		}
		if($i > 1){
			?>
			</div>
			<?php
		}
		if($nbMore > 1){
			?>
			<div class="show-me-more">
				<?= $_SESSION['cste']['SEE_MORE']; ?>
			</div>
			<?php
		}
	}else{
		echo '<div class="display-more">'.$_SESSION['cste']['_NO_DOC_FOR_NOW'].'</div>';
	}
	?>
</div>

<h2 class="contact">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/planning.png" />
	<span>
		<?= $_SESSION['cste']['EVENTS']; ?>
	</span>
</h2>
<div>
	<a class="add" href="/admin.php?submenu=<?= _DESKTOP_V2_DESKTOP; ?>&mode=activity&action=edit&tiers_id=<?= $this->fields['id']; ?>">
		<?= ucfirst(strtolower($_SESSION['cste']['ENTER_NEW_BUSINESS_EVENT'])); ?>
	</a>
</div>
<div id="add_activity">
    <?php
    /*
    <a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['ENTER_NEW_BUSINESS_ACTIVITY']; ?>
	</a>*/
    ?>
</div>
<div id="linked_activity" class="bloc_contact">
	<?php
    // a modifier pour rechercher dans la matrice
    require_once DIMS_APP_PATH."modules/system/activity/class_activity.php";
    $activities = dims_activity::find_by(array('tiers_id'=>$this->fields['id']), ' ORDER BY datejour desc');
    foreach($activities as $activity) {
        $activity->display(_DESKTOP_TPL_LOCAL_PATH . '/activity/view_activity_line.tpl.php');
    }
	//$this->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/planning/display_mini_planning.tpl.php');
	?>
</div>

<h2 class="contact">
	<balise id="todo">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/todos.png" />
		<span>
			<?= $_SESSION['cste']['_TODOS']; ?>
		</span>
	</balise>
</h2>
<div id="add_todo">
	<a class="add" href="javascript:void(0);">
		<?= ucfirst(strtolower($_SESSION['cste']['_ADD_TODO'])); ?>
	</a>
</div>
<div id="linked_todo" class="bloc_contact">
	<div style="display:none;">
		<?php
		require_once DIMS_APP_PATH.'include/class_todo.php';
		$todo = new todo();
		$todo->init_description();
		$todo->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=add_todo&id_ct=".$this->get('id'));
		$todo->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$this->get('id'));
		$todo->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/todo/edit_todo.tpl.php');
		?>
	</div>
	<?php
	$todos = todo::getTodosObj($this->get('id_globalobject'));
	if(count($todos)){
		$i = 1;
		$nbMore = 0;
		$nbTodos = count($todos);
		foreach($todos as $todo){
			if($i == 1){
				?>
				<div class="display-more" <?= ($nbMore == 0)?'':'style="display:none;"'; ?>>
				<?php
				$nbMore++;
			}
			$todo->setLightAttribute('id_ct',$this->get('id'));
			$todo->setLightAttribute('mode','company');
			$todo->setLightAttribute('remove_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=delete_todo&id_ct=".$this->get('id'));
			$todo->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/todo/display_todo.tpl.php');
			if($i == 5){
				$i = 1;
				?>
				</div>
				<?php
			}else
				$i++;
		}
		if($i > 1){
			?>
			</div>
			<?php
		}
		if($nbMore > 1){
			?>
			<div class="show-me-more">
				<?= $_SESSION['cste']['SEE_MORE']; ?>
			</div>
			<?php
		}
	}else{
		echo '<div class="display-more">'.$_SESSION['cste']['_NO_TODO_FOR_NOW'].'</div>';
	}
	?>
</div>

<!--<h2 class="contact">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/discussions.png" />
	<span>
		<?= $_SESSION['cste']['_DISCUSSIONS']; ?>
	</span>
</h2>
<div id="add_discussions">
	<a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['_ADD_DISCUSSION']; ?>
	</a>
</div>
<div id="linked_discussions" class="bloc_contact">
	<?php
	$discussions = array();
	if(count($discussions)){
		foreach($discussions as $disc){

		}
	}else{
		echo $_SESSION['cste']['_NO_DISCUSSION_FOR_NOW'];
	}
	?>
</div>-->

<?php
if($missingInfos['phone'] || $missingInfos['adr']){
	?>
	<div class="warning-missing-infos">
		<table cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/warning32.png" />
				</td>
				<td>
					<?= $_SESSION['cste']['_INFORMATIONS_MISSING_ON_THIS_SHEET']; ?>
				</td>
			</tr>
			<?php if($missingInfos['phone']){ ?>
			<tr>
				<td colspan="2">
					<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=edit&id=".$this->get('id'); ?>"><?= $_SESSION['cste']['_NO_NUM_TEL_PROVIDE']; ?></a>
				</td>
			</tr>
			<?php } ?>
			<?php if($missingInfos['adr']){ ?>
			<tr>
				<td colspan="2">
					<a onclick="javascript:$('div#add_address a.add').click();" href="javascript:void(0);"><?= $_SESSION['cste']['ADD_ADDRESS']; ?></a>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
}
$curMonth = date('n');
$months = "";
foreach($dims_agenda_months as $i => $m){
	if($i == $curMonth)
		$months .= '<option selected=true value="'.$i.'">'.$m.'</option>';
	else
		$months .= '<option value="'.$i.'">'.$m.'</option>';
}
$years = "";
$curYear = date('Y');
for($i=$curYear-10;$i<=$curYear+1;$i++){
	if($i == $curYear)
		$years .= '<option selected=true value="'.$i.'">'.$i.'</option>';
	else
		$years .= '<option value="'.$i.'">'.$i.'</option>';
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('span.tag-tmp').click(function(event){
			idPopup = dims_getUniqId();
			var lstYears = $(this).attr('dims-undo-years').split(','),
				delYear = "";
			if(lstYears.length){
				delYear = '<div class="popup-separator"></div><h3><?= $_SESSION['cste']['_RESTAINT_DATE']; ?></h3><ul style="list-style-type: disc;margin-left: 20px;">';
				for(var i=0; i<lstYears.length; i++){
					delYear += '<li style="height: 16px;">'+lstYears[i]+'<img class="img-del-year-tag" dims-data-year="'+lstYears[i]+'" dims-data-value="'+$(this).attr('dims-data-value')+'" style="float:right;cursor:pointer;" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/delete16.png" alt="<?= $_SESSION['cste']['_DELETE']; ?>" title="<?= $_SESSION['cste']['_DELETE']; ?>" /></li>';
				}
				delYear += "</ul>"
			}
			var popup = $('<div style="display: none;" class="dims-todo-popup dims-tag-tmp" id="'+idPopup+'">\
								<div class="todo-dests">\
									<h3>\
										<?= $_SESSION['cste']['_SELECT_MONTH_AND_YEAR']; ?>\
										<a href="javascript:void(0);" onclick="javascript:$(this).parents(\'div#'+idPopup+':first\').remove();">\
											<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />\
										</a>\
									</h3>\
									<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=company&action=add_tmp_tag&id=<?= $this->get('id'); ?>&id_tag='+$(this).attr('dims-data-value')+'">\
										<div>\
											<label id="tag-month"><?= $_SESSION['cste']['_DIMS_MONTH']; ?></label>\
											<select name="month" id="tag-month"><?= $months; ?></select><br />\
											<label id="tag-year"><?= $_SESSION['cste']['_DIMS_LABEL_YEAR']; ?></label>\
											<select name="year" id="tag-year"><?= $years; ?></select>\
										</div>\
										<div class="actions">\
											<input type="submit" value="<?= $_SESSION['cste']['_DIMS_ADD']; ?>" />\
											<?= $_SESSION['cste']['_DIMS_OR']; ?>\
											<a href="javascript:void(0);" onclick="javascript:$(this).parents(\'div#'+idPopup+':first\').remove();">\
												<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>\
											</a>\
										</div>\
									</form>\
									'+delYear+'\
								</div>\
							</div>');
			if(lstYears.length){
				for(var i=0; i<lstYears.length; i++){
					popup.find('select#tag-year option[value="'+lstYears[i]+'"]:first').remove();
				}
			}
			$('div#popup_container').append(popup);
			var hDoc = parseInt($(document).height()),
				top = parseInt(event.pageY);
			$('div#popup_container div#'+idPopup).css({'visibility':'visible','display':'block','top':event.pageY,'left':event.pageX});
			if(top+parseInt($('div#popup_container div#'+idPopup).outerHeight()) > hDoc-10){
				$('div#popup_container div#'+idPopup).css({'top':(hDoc-10-parseInt($('div#popup_container div#'+idPopup).outerHeight()))});
			}
		});
		$(document).delegate('form','submit',function(e){
			if(!$('body div#loading_form').length){
				var div = '<div id="loading_ajax"><img src="./common/img/loading.gif" /></div>';
				$("body").append(div);
				var div = '<div id="loading_form"></div>';
				$("body").append(div);
			}
			return true;
		}).delegate('img.img-del-year-tag','click',function(){
			document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=company&action=del_tmp_tag&id=<?= $this->get('id'); ?>&id_tag='+$(this).attr('dims-data-value')+'&year='+$(this).attr('dims-data-year');
		});

		// ADDRESS
		$(document).delegate('div#add_address a.add','click',function(){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'submenu': '1',
					'mode': 'address',
					'action' : 'view_edit',
					'id_ct' : '<?= $this->get('id'); ?>',
					'type': <?= tiers::MY_GLOBALOBJECT_CODE; ?>,
				},
				dataType: "html",
				async: false,
				success: function(data){
					$('div#add_address').html(data);
				},
				error: function(data){}
			});
		});
		$(document).delegate('.tooltips','mouseenter',function(event){ // in
			if(!$(this).next('div.tooltip-info').length)
				$(this).after('<div class="tooltip-info">'+$(this).attr('tooltip')+'</div>');
			$(this).next('div.tooltip-info').show().css({top:event.pageY,left:event.pageX+10});
		}).delegate('.tooltips','mouseleave',function(event){ // out
			$(this).next('div.tooltip-info').hide();
		});

		// TIERS
		$(document).delegate('div#add_tiers a.add','click',function(){
			var data = '<h2 style="margin-bottom:10px;color:#686868;"><?= $_SESSION['cste']['_NEW_SUB_SERVICE']; ?></h2>\
						<span><input class="button_search" type="image" style="float:left;" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" />\
						<input type="text" class="desktop_editbox_search" style="width: 350px;color:#424242;" placeholder="<?= $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?> ..." />\
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" /></span><p style="clear:both;"></p>\
						<div id="res_search_tiers"><a class="undo" href="javascript:void(0);"><?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?></a></div>';
			$('div#add_tiers').html(data);
			$('div#add_tiers input.desktop_editbox_search').focus();
			$('div#linked_tiers').hide();
		});
		$('div#add_tiers').delegate('input.desktop_editbox_search','keyup',function(event){
			var keycode = event.keyCode;
			var value = "";
			if($(this).val() != '' && $(this).val() != '<?= $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?> ...'){
				value = $(this).val();
				clearTimeout(temp_search);
				temp_search = setTimeout('searchTiers("'+value+'")' , 2000);
			}
			if(keycode == 13){ // enter
				event.preventDefault();
			}
		}).delegate('input.desktop_editbox_search','keydown',function(event){
			var keycode = event.keyCode;
			if(keycode == 13){ // enter
				event.preventDefault();
				if($(this).val() != '' && $(this).val() != '<?= $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?> ...'){
					clearTimeout(temp_search);
					searchTiers($(this).val());
				}
			}
		}).delegate('a.undo','click',function(){
			$('div#add_tiers').html('<a class="add" href="javascript:void(0);"><?= $_SESSION['cste']['_ADD_SUB_SERVICES']; ?></a>');
			$('div#linked_tiers').show();
		}).delegate('input.button_search','click',function(){
			if($('div#add_tiers input.desktop_editbox_search').val() != '' && $('div#add_tiers input.desktop_editbox_search').val() != '<?= $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?> ...'){
				clearTimeout(temp_search);
				searchTiers($('div#add_tiers input.desktop_editbox_search').val());
			}
		}).delegate('input.submit.add_tiers','click',function(){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'submenu': '1',
					'mode': 'company',
					'action' : 'view_edit',
					'id_ct': '<?= $this->get('id'); ?>',
					'type': <?= tiers::MY_GLOBALOBJECT_CODE; ?>,
					'label': $('div#add_tiers input.desktop_editbox_search').val(),
				},
				dataType: "html",
				async: false,
				success: function(data){
					$('div#add_tiers').replaceWith(data);
				},
				error: function(data){}
			});
		});

		// CONTACTS
		$(document).delegate('div#add_contact a.add','click',function(){
			var data = '<h2 style="margin-bottom:10px;color:#686868;"><?= $_SESSION['cste']['_ADD_CT']; ?></h2>\
						<span><input class="button_search" type="image" style="float:left;" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" />\
						<input type="text" class="desktop_editbox_search" style="width: 350px;color:#424242;" placeholder="<?= $_SESSION['cste']['_SEARCH_CONTACT']; ?> ..." />\
						<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" /></span><p style="clear:both;"></p>\
						<div id="res_search_contact"><a class="undo" href="javascript:void(0);"><?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?></a></div>';
			$('div#add_contact').html(data);
			$('div#add_contact input.desktop_editbox_search').focus();
			$('div#linked_contact').hide();
		});
		$('div#add_contact').delegate('input.desktop_editbox_search','keyup',function(event){
			var keycode = event.keyCode;
			var value = "";
			if($(this).val() != '' && $(this).val() != '<?= $_SESSION['cste']['_SEARCH_CONTACT']; ?> ...'){
				value = $(this).val();
				clearTimeout(temp_search2);
				temp_search2 = setTimeout('searchTiers("'+value+'")' , 2000);
			}
			if(keycode == 13){ // enter
				event.preventDefault();
			}
		}).delegate('input.desktop_editbox_search','keydown',function(event){
			var keycode = event.keyCode;
			if(keycode == 13){ // enter
				event.preventDefault();
				if($(this).val() != '' && $(this).val() != '<?= $_SESSION['cste']['_SEARCH_CONTACT']; ?> ...'){
					clearTimeout(temp_search2);
					searchContacts($(this).val());
				}
			}
		}).delegate('a.undo','click',function(){
			$('div#add_contact').html('<a class="add" href="javascript:void(0);"><?= $_SESSION['cste']['_ADD_CT']; ?></a>');
			$('div#linked_contact').show();
		}).delegate('input.button_search','click',function(){
			if($('div#add_contact input.desktop_editbox_search').val() != '' && $('div#add_contact input.desktop_editbox_search').val() != '<?= $_SESSION['cste']['_SEARCH_CONTACT']; ?> ...'){
				clearTimeout(temp_search2);
				searchContacts($('div#add_contact input.desktop_editbox_search').val());
			}
		}).delegate('input.submit.add_contact','click',function(){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'submenu': '1',
					'mode': 'contact',
					'action' : 'view_edit',
					'id_tiers': '<?= $this->get('id'); ?>',
					'type': <?= contact::MY_GLOBALOBJECT_CODE; ?>,
					'label': $('div#add_contact input.desktop_editbox_search').val()
				},
				dataType: "html",
				async: false,
				success: function(data){
					$('div#add_contact').replaceWith(data);
				},
				error: function(data){}
			});
		});
		// DOC
		$('div#add_document').delegate('a.add','click',function(){
			$('div#linked_doc form:first').show();
			$('div#linked_doc div.display-more').hide();
			$('div#linked_doc div.show-me-more').hide();
		});
		$('div#linked_doc').delegate('div.show-me-more','click',function(){
			if($('div#linked_doc div.display-more:hidden').length){
				$('div#linked_doc div.display-more:hidden:first').show();
				if(!$('div#linked_doc div.display-more:hidden').length){
					$(this).text('<?= $_SESSION['cste']['SEE_LESS']; ?>');
				}
			}else{
				$('div#linked_doc div.display-more').hide();
				$('div#linked_doc div.display-more:hidden:first').show();
				$(this).text('<?= $_SESSION['cste']['SEE_MORE']; ?>');
			}
		});
		// TODO
		$('div#add_todo').delegate('a.add','click',function(){
			$('div#linked_todo div:first').show();
			$('div#linked_todo div.display-more').hide();
			$('div#linked_todo div.show-me-more').hide();
		});
		$('div#linked_todo div:first form').submit(function(){
			$('div#linked_todo div:first form input').attr('disabled',false);
			return true;
		});
	});
	var temp_search = null;
	function searchTiers(label){
		$.ajax({
			type: "POST",
			url: '<?= dims::getInstance()->getScriptEnv(); ?>',
			data: {
					'submenu': '1',
					'mode': 'company',
					'action' : 'search_tiers',
					'id_tiers' : '<?= $this->get('id'); ?>',
					'label_search_tiers' : $('div#add_tiers input.desktop_editbox_search').val()
				},
			dataType: 'html',
			success: function(data){
				$('div#add_tiers div#res_search_tiers').html(data);
				clearTimeout(temp_search);
			},
		});
	}
	var temp_search2 = null;
	function searchContacts(label){
		$.ajax({
			type: "POST",
			url: '<?= dims::getInstance()->getScriptEnv(); ?>',
			data: {
					'submenu': '1',
					'mode': 'contact',
					'action' : 'search_contact',
					'id_tiers' : '<?= $this->get('id'); ?>',
					'label_search_contact' : $('div#add_contact input.desktop_editbox_search').val()
				},
			dataType: 'html',
			success: function(data){
				$('div#add_contact div#res_search_contact').html(data);
				clearTimeout(temp_search2);
			},
		});
	}
</script>
