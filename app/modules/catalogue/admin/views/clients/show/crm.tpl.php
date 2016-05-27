<?php
$contact    = $this->get('contact');
$tier       = $this->get('tier');
$client     = $this->get('client');
global $dims_agenda_months;
?>
<div class="catalogue-crm">
<table style="width:100%;">
<?php if (trim($contact->get('comments')) != '') { ?>
<tr>
	<td style="vertical-align:top;">
		<?= nl2br(trim($contact->get('comments'))); ?>
	</td>
</tr>
<?php } ?>
<tr>
	<td class="actions">
		<input class="edit" type="button" value="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" onclick="javascript:document.location.href='<?= $contact->getLightAttribute('edit_url'); ?>';" />
	</td>
</tr>
</table>

<h2 class="tag">
<img src="<?=  $this->getTemplateWebPath('gfx/tag20.png'); ?>" />
<span>
	<?= dims_constant::getVal('TAGS'); ?>
</span>
</h2>
<div id="add_tag">
<a class="add" href="javascript:void(0);">
	<?php //<?= dims_constant::getVal('ADD_GROUP'); ?>
</a>
</div>
<div class="bloc_contact">
<?php
$myTags = $contact->getMyTags(tag::TYPE_DEFAULT);
foreach ($myTags as $t) {
?>
<span class="tag" dims-data-value="<?= $t->get('id'); ?>">
	<?= $t->get('tag'); ?>
</span>
<?php
}
$tagsTmp = tag::find_by(array('id_workspace' => $_SESSION['dims']['workspaceid'], 'type' => tag::TYPE_DURATION), ' ORDER BY tag ');
foreach ($tagsTmp as $t) {
$months = array();
$years = $t->getYearsContact($contact->get('id_globalobject'), $months);
$titleTmp = $t->get('tag');
if (count($months)) {
	foreach ($months as $y => $m) {
		$titleTmp .= "\n - ".$dims_agenda_months[$m]." $y";
	}
}
?>
<span class="tag-tmp" dims-undo-years="<?= implode(',', $years); ?>" dims-data-value="<?= $t->get('id'); ?>" title="<?= $titleTmp; ?>">
	<?= $t->get('tag').(count($years) ? ' ('.implode('/', $years).')' : ''); ?>
	<input type="hidden" />
</span>
<?php
}
?>
</div>

<h2 class="group">
<img src="<?=  $this->getTemplateWebPath('gfx/group20.png'); ?>" />
	<span>
		<?= dims_constant::getVal('GROUPS'); ?>
	</span>
</h2>
<div id="add_group">
	<a class="add" href="javascript:void(0);">
		<?php //<?= dims_constant::getVal('ADD_GROUP'); ?>
	</a>
</div>
<?php
$lstGr = ct_group::getGroupsLinked($contact->get('id_globalobject'), contact::MY_GLOBALOBJECT_CODE);
if (count($lstGr)) {
	?>
	<div id="linked_group" class="bloc_contact">
			<?= dims_constant::getVal('THIS_CUSTOMER_BELONGS_TO_THE_FOLLOWING_GROUPS'); ?>
			<?php
			$gr = array();
			foreach ($lstGr as $g) {
				$gr[] = $g->get('label');
			}
			echo implode(', ', $gr).'.';
			?>
	</div>
	<?php
	}
?>

<h2 class="contact">
	<balise id="todo">
		<img src="<?=  $this->getTemplateWebPath('gfx/todo20.png'); ?>" />
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
		$todo->setLightAttribute('save_url', get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'add_todo')));
		$todo->setLightAttribute('back_url', get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));
		$todo->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/todo/edit_todo.tpl.php');
		?>
	</div>
	<?php
	$todos = todo::getTodosObj($contact->get('id_globalobject'));
	if (count($todos)) {
		$i = 1;
		$nbMore = 0;
		$nbTodos = count($todos);
		foreach ($todos as $todo) {
			if ($i == 1) {
				?>
				<div class="display-more" <?= ($nbMore == 0) ? '' : 'style="display:none;"';
				?>>
				<?php
				$nbMore++;
			}
			$todo->setLightAttribute('id_ct', $contact->get('id'));
			$todo->setLightAttribute('mode', 'contact');
			$todo->setLightAttribute('remove_url', get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'delete_todo', 'id_todo' => $todo->get('id'))));
			$todo->setLightAttribute('in_catalogue', true);
			$todo->setLightAttribute('client', $client);

			$todo->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/todo/display_todo.tpl.php');
			if ($i == 5) {
				$i = 1;
				?>
				</div>
				<?php

			} else {
				$i++;
			}
		}
		if ($i > 1) {
			?>
			</div>
			<?php

		}
		if ($nbMore > 1) {
			?>
			<div class="show-me-more">
				<?= $_SESSION['cste']['SEE_MORE'];
			?>
			</div>
			<?php

		}
	} else {
		echo '<div class="display-more">'.$_SESSION['cste']['_NO_TODO_FOR_NOW'].'</div>';
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
	<a class="add" href="Javascript: void(0);" onclick="Javascript: dims_switchdisplay('add_linked_activity');">
		<?= ucfirst(strtolower($_SESSION['cste']['ENTER_NEW_BUSINESS_EVENT'])); ?>
	</a>
</div>
<form id="add_linked_activity" style="display: none;" action="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'add_activity')); ?>" method="post">
	<fieldset>
		<table class="w100">
			<tr>
				<td class="vatop">
					<table>
						<tr>
							<td><label class="title">Type</label></td>
							<td>
								<select class="w100" id="activity_type_id" name="activity_type_id" data-placeholder="Sélectionnez un type d'activité">
									<option value="-1"><?= $_SESSION['cste']['_DIMS_ALLS']; ?></option>
									<?php
									foreach(activity_type::getAllTypes() as $type) {
										?>
										<option value="<?= $type->getId(); ?>">
											<?= $type->fields['label']; ?>
										</option>
										<?php
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label class="title" for="activity_responsable">Responsable</label></td>
							<td>
								<select class="w100" id="activity_responsable" name="activity_responsable" data-placeholder="Sélectionnez un responsable">
									<option value="<?= $_SESSION['dims']['userid']; ?>" selected="selected">Vous-même</option>
									<?php
									// tous les utilisateurs du workspace sauf celui qui est connecté
									$db = dims::getInstance()->getDb();
									$rs = $db->query('
										SELECT u.id, u.firstname, u.lastname
										FROM dims_user u
										INNER JOIN dims_workspace_user wu
										ON wu.id_user = u.id
										AND wu.id_workspace = :idworkspace
										WHERE u.id != :iduser',
										array(
											':idworkspace'  => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
											':iduser'       => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
									));
									while ($row = $db->fetchrow($rs)) {
										echo '<option value="'.$row['id'].'">'.$row['firstname'].' '.$row['lastname'].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label class="title" for="activity_date_from">Date du</label></td>
							<td>
								<input type="text" id="activity_date_from" name="activity_date_from" value="<?= date('d/m/Y'); ?>" />
								<img style="vertical-align:middle" src="./common/img/calendar.png" alt="Date de début" onclick="javascript:dims_calendar_open('activity_date_from', event);" /> à
								<input type="text" name="activity_hour_from" value="08" class="w20p txtcenter" /> :
								<input type="text" name="activity_mins_from" value="00" class="w20p txtcenter" />
							</td>
						</tr>
						<tr>
							<td><label class="title" for="activity_date_to">au</label></td>
							<td>
								<input type="text" id="activity_date_to" name="activity_date_to" value="<?= date('d/m/Y'); ?>" />
								<img style="vertical-align:middle" src="./common/img/calendar.png" alt="Date de fin" onclick="javascript:dims_calendar_open('activity_date_to', event);" /> à
								<input type="text" name="activity_hour_to" value="18" class="w20p txtcenter" /> :
								<input type="text" name="activity_mins_to" value="00" class="w20p txtcenter" />
							</td>
						</tr>
					</table>
				</td>
				<td class="vatop">
					<table><tr>
							<td class="vatop"><label class="title" for="activity_label">Libellé</label></td>
							<td><input type="text" style="width:292px;" id="activity_label" name="activity_label" value="" /></td>
						</tr>
						<tr>
							<td class="vatop"><label class="title" for="activity_description">Complément</label></td>
							<td><textarea id="activity_description" name="activity_description" style="width: 292px;"></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<input type="submit" value="Ajouter" /> ou <a href="Javascript: void(0);" onclick="Javascript: dims_switchdisplay('add_linked_activity');">Annuler</a>
	</fieldset>
</form>
<div id="linked_activity" class="bloc_contact">
	<?php
    // a modifier pour rechercher dans la matrice
    $activities = dims_activity::find_by(array('tiers_id' => $tier->fields['id']), ' ORDER BY datejour desc');
    foreach($activities as $activity) {
        $activity->display(_DESKTOP_TPL_LOCAL_PATH . '/activity/view_activity_line.tpl.php');
    }
	?>
</div>

<?php
$curMonth = date('n');
$months = '';
foreach ($dims_agenda_months as $i => $m) {
	if ($i == $curMonth) {
		$months .= '<option selected=true value="'.$i.'">'.$m.'</option>';
	} else {
		$months .= '<option value="'.$i.'">'.$m.'</option>';
	}
}
$years = '';
$curYear = date('Y');
for ($i = $curYear - 10;$i <= $curYear + 1;$i++) {
	if ($i == $curYear) {
		$years .= '<option selected=true value="'.$i.'">'.$i.'</option>';
	} else {
		$years .= '<option value="'.$i.'">'.$i.'</option>';
	}
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
									<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=add_tmp_tag&id=<?= $contact->get('id'); ?>&id_tag='+$(this).attr('dims-data-value')+'">\
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
			document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=del_tmp_tag&id=<?= $contact->get('id'); ?>&id_tag='+$(this).attr('dims-data-value')+'&year='+$(this).attr('dims-data-year');
		});

		$(document).delegate('div#add_address a.add','click',function(){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'submenu': '1',
					'mode': 'address',
					'action' : 'view_edit',
					'id_ct' : '<?= $contact->get('id'); ?>',
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
					'id_ct': '<?= $contact->get('id'); ?>',
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
		$('div#add_todo').delegate('a.add','click',function() {
			$('div#linked_todo div:first').show();
			$('div#linked_todo div.display-more').hide();
			$('div#linked_todo div.show-me-more').hide();
		});
		$('div#linked_todo').delegate('div.show-me-more','click',function() {
			if($('div#linked_todo div.display-more:hidden').length){
				$('div#linked_todo div.display-more:hidden:first').show();
				if(!$('div#linked_todo div.display-more:hidden').length){
					$(this).text('<?= $_SESSION['cste']['SEE_LESS']; ?>');
				}
			} else {
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
					'id_ct' : '<?= $contact->get('id'); ?>',
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
</div>
