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
	'tiers'=>false,
	'mail'=>false,
);
?>
<table style="width:100%;">
	<tr>
		<td style="width:110px;vertical-align:top;" rowspan="<?= (trim($this->get('comments')) != '')?5:4; ?>">
			<?php
			$file = $this->getPhotoPath(100);//real_path
			if(file_exists($file)){
				?>
				<img class="picture" src="<?= $this->getPhotoWebPath(100); ?>">
				<?php
			}
			else{
				?>
				<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/human100.png">
				<?php
			}
			?>
		</td>
		<th style="font-size:15px;height:20px;border:0px;text-align:left;">
			<?= $_SESSION['cste']['_THE_MAINS_PLUG']." : ".(($this->get("civilite")!='')?$this->get("civilite")." ":"").(($this->get("title")!='')?$this->get("title")." ":"").$this->get("firstname")." ".$this->get("lastname"); ?>
		</th>
	</tr>
	<tr>
		<td style="height:15px;">
			<?php
			$infos = array();
			if($this->get('email') != '')
				$infos[] = '<a href="mailto:'.$this->get('email').'">'.$this->get('email').'</a>';
			if($this->get('email2') != '')
				$infos[] = '<a href="mailto:'.$this->get('email2').'">'.$this->get('email2').'</a>';
			if($this->get('email') == '' && $this->get('email2') == ''){
				$missingInfos['mail'] = true;
			}
			if($this->get('mobile') != ''){
				$infos[] ="<span data-phone=".$this->get('mobile')." data-callname=".$this->get('firstname')."&nbsp;".$this->get('lastname').">".$this->get('mobile')."(Tél)</span>";
			}else
				$missingInfos['phone'] = true;
			if($this->get('fax') != '')
				$infos[] = $this->get('fax')." (Fax)";
			if($this->get('num_enregistrement') != '')
				$infos[] = $this->get('num_enregistrement')." (".$_SESSION['cste']['_REGISTRATION_NO'].")";
			if($this->get('facebook') != '')
				$infos[] = '<a href="'.$this->get('facebook').'" target="_blank">'.$this->get('facebook').'</a>';
			if($this->get('twitter') != '')
				$infos[] = '<a href="'.$this->get('twitter').'" target="_blank">'.$this->get('twitter').'</a>';
			if($this->get('linkedin') != '')
				$infos[] = '<a href="'.$this->get('linkedin').'" target="_blank">'.$this->get('linkedin').'</a>';
			if($this->get('google_plus') != '')
				$infos[] = '<a href="'.$this->get('google_plus').'" target="_blank">'.$this->get('google_plus').'</a>';
			if($this->get('viadeo') != '')
				$infos[] = '<a href="'.$this->get('viadeo').'" target="_blank">'.$this->get('viadeo').'</a>';
			print implode(' - ',$infos);

			?>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:top;">
		<?php
		if (dims::getInstance()->isModuleTypeEnabled('catalogue')) {
			include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
			$user = $this->getLinkedUser();

			if(!$user->isNew()) {
				$db = dims::getInstance()->getDb();
				$sql = 'SELECT      c.*
						FROM        dims_user u
						INNER JOIN  dims_group_user gu
						ON          gu.id_user = u.id
						INNER JOIN  dims_mod_cata_client c
						ON          c.dims_group = gu.id_group
						WHERE       u.id = :userid';

				$res = $this->db->query($sql, array(
					':userid' => array('type' => PDO::PARAM_INT, 'value' => $user->getId()),
				));

				if($db->numrows($res)) {
					$catalogueclient = new client();
					$catalogueclient->openFromResultSet($db->fetchrow($res));
					?>
					<div class="customerCard">
						<a href="/admin.php?dims_mainmenu=catalogue&c=clients&a=show&id=<?= $catalogueclient->getId(); ?>&sc=services">
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
		}
		?>
		</td>
	</tr>
	<?php if(trim($this->get('comments')) != ''){ ?>
	<tr>
		<td style="vertical-align:top;">
			<?= nl2br(trim($this->get('comments'))); ?>
		</td>
	</tr>
	<?php } ?>
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
	<?php
	$lstGr = ct_group::getGroupsLinked($this->get('id_globalobject'),contact::MY_GLOBALOBJECT_CODE);
	?>
	<tr>
		<td colspan="2" <?= (count($lstGr))?"":'style="padding-bottom:10px;"'; ?>>
			<?php
			$tagsTmp = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'type'=>tag::TYPE_DURATION), ' ORDER BY tag ');
			foreach($tagsTmp as $t){
				$months = array();
				$years = $t->getYearsContact($this->get('id_globalobject'),$months);
				$titleTmp = $t->get('tag');
				if(count($months)){
					foreach ($months as $y => $m) {
						$titleTmp .= "\n - ".$dims_agenda_months[$m]." $y";
					}
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
	<?php
	if(count($lstGr)){
	?>
	<tr>
		<td colspan="2" style="padding-bottom:10px;">
			<?= $_SESSION['cste']['_DIMS_LABEL_CT_GROUP']; ?> :&nbsp;
			<?php
			$gr = array();
			foreach($lstGr as $g){
				$gr[] = $g->get('label');
			}
			echo implode(' / ',$gr);
			?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2" class="actions">
			<input class="edit" type="button" value="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=edit&id=".$this->get('id'); ?>';" />
			<input class="delete" type="button" value="<?= $_SESSION['cste']['_DELETE']; ?>" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=delete&id=<?= $this->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');" />
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
		<?= $_SESSION['cste']['_DIMS_LABEL_ENTERPRISES']." / ".$_SESSION['cste']['_COMPANIES_CT'] ; ?>
	</span>
</h2>
<div id="add_tiers">
	<a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['_ADD_A_STRUCTURE']; ?>
	</a>
</div>
<div id="linked_tiers" class="bloc_contact">
	<?php
	$lstComp = $this->getAllCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR');
	if(count($lstComp)){
		foreach($lstComp as $tiers){
			$tiers->setLightAttribute('mode','contact');
			$tiers->setLightAttribute('id_ct',$this->get('id'));
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
		$missingInfos['tiers'] = true;
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
			$addr->setLightAttribute('mode','contact');
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
if(empty($catalogueclient)){
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
	$folder->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=add_file&id_ct=".$this->get('id'));
	$folder->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$this->get('id'));
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
			$doc->setLightAttribute('mode','contact');
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

<!--<h2 class="contact">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/planning.png" />
	<span>
		<?= $_SESSION['cste']['ACTIVITIES']." / ".$_SESSION['cste']['_BUSINESS_ACTION']; ?>
	</span>
</h2>
<div id="add_activity">
	<a class="add" href="javascript:void(0);">
		<?= $_SESSION['cste']['ENTER_NEW_BUSINESS_ACTIVITY']; ?>
	</a>
</div>
<div id="linked_activity" class="bloc_contact">
	<?php
	//$this->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/planning/display_mini_planning.tpl.php');
	?>
</div>-->

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
		$todo->setLightAttribute('save_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=add_todo&id_ct=".$this->get('id'));
		$todo->setLightAttribute('back_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$this->get('id'));
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
			$todo->setLightAttribute('mode','contact');
			$todo->setLightAttribute('remove_url',dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=delete_todo&id_ct=".$this->get('id'));
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

<h2 class="contact">
	<balise id="todo">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/contact/mailing.png" />
		<span>
			<?= $_SESSION['cste']['_DIMS_LABEL_MAILINGLIST']; ?>
		</span>
	</balise>
</h2>
<div id="add_mailinglist">

</div>
<div id="linked_mailing" class="bloc_contact">

	<?php
	$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_CONTACT;
	require_once DIMS_APP_PATH.'modules/system/desktopV2/templates/concepts/context_tags/mailinglist.tpl.php';

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
if($missingInfos['phone'] || ($missingInfos['tiers'] && $missingInfos['adr']) || $missingInfos['mail']){
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
			<?php if($missingInfos['mail']){ ?>
			<tr>
				<td colspan="2">
					<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=edit&id=".$this->get('id'); ?>"><?= $_SESSION['cste']['_NO_MAIL_FILLED']; ?></a>
				</td>
			</tr>
			<?php } ?>
			<?php if($missingInfos['phone']){ ?>
			<tr>
				<td colspan="2">
					<a href="<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=edit&id=".$this->get('id'); ?>"><?= $_SESSION['cste']['_NO_NUM_TEL_PROVIDE']; ?></a>
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
			<?php if($missingInfos['tiers']){ ?>
			<tr>
				<td colspan="2">
					<a onclick="javascript:$('div#add_tiers a.add').click();" href="javascript:void(0);"><?= $_SESSION['cste']['_ADD_A_STRUCTURE']; ?></a>
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
									<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=add_tmp_tag&id=<?= $this->get('id'); ?>&id_tag='+$(this).attr('dims-data-value')+'">\
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
			document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=del_tmp_tag&id=<?= $this->get('id'); ?>&id_tag='+$(this).attr('dims-data-value')+'&year='+$(this).attr('dims-data-year');
		});

		$(document).delegate('div#add_address a.add','click',function(){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'submenu': '1',
					'mode': 'address',
					'action' : 'view_edit',
					'id_ct' : '<?= $this->get('id'); ?>',
					'type': <?= contact::MY_GLOBALOBJECT_CODE; ?>,
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
		$(document).delegate('div#add_tiers a.add','click',function(){
			var data = '<h2 style="margin-bottom:10px;color:#686868;"><?= $_SESSION['cste']['_NEW_STRUCTURE']; ?> / <?= strtolower($_SESSION['cste']['_COMPANY_CT']); ?></h2>\
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
			if(keycode == 27){ // echap
				clearTimeout(temp_search);
				$('div#add_tiers').html('<a class="add" href="javascript:void(0);"><?= $_SESSION['cste']['_ADD_A_STRUCTURE']; ?></a>');
				event.preventDefault();
				return true;
			}
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
			$('div#add_tiers').html('<a class="add" href="javascript:void(0);"><?= $_SESSION['cste']['_ADD_A_STRUCTURE']; ?></a>');
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
					'type': <?= contact::MY_GLOBALOBJECT_CODE; ?>,
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
		$('div#add_todo').delegate('a.add','click',function(){
			$('div#linked_todo div:first').show();
			$('div#linked_todo div.display-more').hide();
			$('div#linked_todo div.show-me-more').hide();
		});
		$('div#linked_todo').delegate('div.show-me-more','click',function(){
			if($('div#linked_todo div.display-more:hidden').length){
				$('div#linked_todo div.display-more:hidden:first').show();
				if(!$('div#linked_todo div.display-more:hidden').length){
					$(this).text('<?= $_SESSION['cste']['SEE_LESS']; ?>');
				}
			}else{
				$('div#linked_todo div.display-more').hide();
				$('div#linked_todo div.display-more:hidden:first').show();
				$(this).text('<?= $_SESSION['cste']['SEE_MORE']; ?>');
			}
		});
		$('div#linked_todo div:first form').submit(function(){
			$('div#linked_todo div:first form input').attr('disabled',false);
			return true;
		});

		$('div#add_mailinglist').delegate('a.add','click',function(){
			$('div#linked_mailing div:first').show();
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
					'id_ct' : '<?= $this->get('id'); ?>',
					'label_search_tiers' : $('div#add_tiers input.desktop_editbox_search').val(),
				},
			dataType: 'html',
			success: function(data){
				$('div#add_tiers div#res_search_tiers').html(data);
				clearTimeout(temp_search);
			},
		});
	}
</script>
