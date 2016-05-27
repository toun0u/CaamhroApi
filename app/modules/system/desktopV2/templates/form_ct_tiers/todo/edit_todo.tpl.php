<?php
$formId = uniqid();
?>
<form method="POST" action="<?= $this->getLightAttribute('save_url'); ?>" id="<?= $formId; ?>">
	<?php
	$listUsers = "";
	$content = $inputHidden = "";
	$nbLinked = 0;
	if(!$this->isNew()){
		$this->initDestinataires();
		$lstDest = $this->getListDestinataires();

		foreach($lstDest as $todoDest){
			$listUsers .= 'listUsers['.$nbLinked.'] = "'.$todoDest->get('id').'"; ';
			$nbLinked ++;
			$us = user::find_by(array('id'=>$todoDest->get('id_user')),null,1);
			if(!empty($us)){
				$inputHidden .= '<input type="hidden" name="user_id[]" value="'.$us->get('id').'" />';
				ob_start();
				$us->setLightAttribute('extended',true);
				$us->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/user/display_mini_user.tpl.php');
				$content .= ob_get_contents();
				ob_end_clean();
			}
		}
		?>
		<input type="hidden" name="id_todo" value="<?= $this->get('id'); ?>" />
		<?php
	}
	?>
	<table style="width:100%;">
		<tr>
			<td rowspan="2" style="width:20%;">
				<textarea name="todo_content" placeholder="<?= $_SESSION['cste']['_ENTER_YOUR_TEXT_HERE']; ?>" style="resize:none;width:99%;height:75px;"><?= $this->get('content'); ?></textarea>
			</td>
			<td style="height: 20px;vertical-align: top;" class="add-users">
				<img style="cursor:pointer;" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/humans.png" /> <span style="vertical-align:top;">(<span class="count-user-add"><?= $nbLinked; ?></span>)</span>
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top;">
				<?php
				$limitDate = "";
				if($this->get('date') != "0000-00-00 00:00:00"){
					$d = explode(" ",$this->get('date'));
					if(count($d) == 2){
						$dd = explode("-",$d[0]);
						$limitDate = $dd[2]."/".$dd[1]."/".$dd[0];
					}
				}
				?>
				<input type="text" value="<?= $limitDate; ?>" placeholder="<?= $_SESSION['cste']['_NOT_DUE']; ?>" readonly="true" style="<?= ($limitDate!='')?'width:75px;':''; ?>border:0px;background:#FFFFFF;color:#424242;vertical-align: top;" name="todo_date" id="todo_date_<?= $formId; ?>" />
				<?php if($limitDate != ''){ ?>
				<span><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer16.png" class="clear-todo-datepicker" style="cursor:pointer;" title="<?= $_SESSION['cste']['REINITIALISER']; ?>" alt="<?= $_SESSION['cste']['REINITIALISER']; ?>" /></span>
				<?php } ?>
			</td>
		</tr>
	</table>
	<div style="display:none;" class="added-user-input"><?= $inputHidden; ?></div>
	<div class="actions" style="float:none;">
		<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= $this->getLightAttribute('back_url'); ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<script type="text/javascript">
	if(typeof(window['searchUserTodo']) == "undefined"){
		var tmpSearchTodo = null;
		window['searchUserTodo'] = function searchUserTodo(label, lstUsers, divRes){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					'dims_op': 'desktopv2',
					'action': 'searchUser',
					'val': label,
					'lu[]': lstUsers,
				},
				dataType: 'html',
				success: function(data){
					var hDoc = parseInt($(document).height()),
						top = parseInt(divRes.parents('div.dims-todo-popup:first').position().top);
					divRes.html(data);
					if(top+parseInt(divRes.parents('div.dims-todo-popup:first').outerHeight()) > hDoc-10){
						divRes.parents('div.dims-todo-popup:first').css({'top':(hDoc-10-parseInt(divRes.parents('div.dims-todo-popup:first').outerHeight()))});
					}
				},
			});
		}
		clearTimeout(tmpSearchTodo);
	}
	$(document).ready(function(){
		var idPopup = null;
		$('form#<?= $formId; ?> input#todo_date_<?= $formId; ?>').datepicker({
			dateFormat: "dd/mm/yy",
			minDate: 0,
			showOn: "button",
			buttonImage: "<?= _DESKTOP_TPL_PATH; ?>/gfx/common/planning16.png",
			buttonImageOnly: true,
			buttonText: "<?= $_SESSION['cste']['_TICKET_LIMIT_TIME_VALIDATION']; ?>",
		}).change(function(){
			if(jQuery.trim($(this).val()) != ''){
				if($('img',$(this).parents('td:first')).length <= 1){
					$(this).css({"width": "75px"});
					$(this).parents('td:first').append('<span><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer16.png" class="clear-todo-datepicker" style="cursor:pointer;" title="<?= $_SESSION['cste']['REINITIALISER']; ?>" alt="<?= $_SESSION['cste']['REINITIALISER']; ?>" /></span>');
				}
			}else{
				$('img:last',$(this).parents('td:first')).remove();
				$(this).css({"width": "auto"});
			}
		});
		$('form#<?= $formId; ?>').delegate('img.clear-todo-datepicker','click',function(){
			$('form#<?= $formId; ?> input#todo_date_<?= $formId; ?>').val('').trigger('change');
		});
		$('img.ui-datepicker-trigger',$('form#<?= $formId; ?> input#todo_date_<?= $formId; ?>').parents('td:first')).css({"float":"left","margin-right":"5px","cursor":"pointer"});
		$('form#<?= $formId; ?>').delegate('td.add-users','click',function(event){
			var listUsers = new Array;
			<?= $listUsers; ?>
			if(idPopup == null || !$('div#popup_container div#'+idPopup).length){
				idPopup = dims_getUniqId();
				var popup = '	<div style="display: none;" class="dims-todo-popup" id="'+idPopup+'">\
									<div class="todo-dests">\
										<h3>\
											<?= $_SESSION['cste']['_DIMS_LABEL_DESTS']; ?>\
											<a href="javascript:void(0);" onclick="javascript:$(this).parents(\'div#'+idPopup+':first\').hide();">\
												<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />\
											</a>\
										</h3>\
										<div class="todo-added">\
											<?= str_replace(array("\r\n","\n","'"),array("\\\r\n","\\\n","\'"),$content); ?>\
										</div>\
										<div class="todo-search"><input type="text" placeholder="<?= $_SESSION['cste']['_TYPE_A_NAME']; ?>" /></div>\
										<div class="todo-results"></div>\
									</div>\
								</div>';
				$('div#popup_container').append(popup);

				// Touts les events
				$('div#popup_container div.dims-todo-popup#'+idPopup).delegate('div.todo-search input','keyup',function(event){ // recherche d'user
					if(jQuery.trim($(this).val()) != ''){
						$(this).css('font-style','normal');
						clearTimeout(tmpSearchTodo);
						tmpSearchTodo = setTimeout('searchUserTodo("'+jQuery.trim($(this).val())+'", '+listUsers+', $("div#popup_container div#'+idPopup+' div.todo-results"))' , 2000);
					}else{
						$(this).css('font-style','italic');
					}
					var keycode = event.keyCode;
					if(keycode == 13){ // enter
						event.preventDefault();
						clearTimeout(tmpSearchTodo);
						searchUserTodo(jQuery.trim($(this).val()), listUsers, $('div#popup_container div#'+idPopup+" div.todo-results"));
					}
				}).delegate('div.todo-search input','keydown',function(event){
					var keycode = event.keyCode;
					if(keycode == 13){ // enter
						event.preventDefault();
					}
				}).delegate('div.todo-results div.display-user','click',function(){ // add user
					if(jQuery.inArray($(this).attr('dims-data-value'),listUsers)<0){
						$('tr:first',$(this)).append('<td style="width:20px;"><a href="javascript:void(0);" class="del-added-user"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a></td>');
						$(this).appendTo($('div#popup_container div.dims-todo-popup#'+idPopup+' div.todo-added'));
						listUsers.push($(this).attr('dims-data-value'));
						$('form#<?= $formId; ?> div.added-user-input').append('<input type="hidden" name="user_id[]" value="'+$(this).attr('dims-data-value')+'" />');
						$('div#popup_container div.dims-todo-popup#'+idPopup+' div.todo-results').empty();
						$('div#popup_container div.dims-todo-popup#'+idPopup+' div.todo-search input').val('');
					$('form#<?= $formId; ?> span.count-user-add').text($('form#<?= $formId; ?> div.added-user-input input[name^=\"user_id\"]').length);
					}
				}).delegate('div.todo-added a.del-added-user','click',function(){ // del user
					var val = $(this).parents('div.display-user:first').attr('dims-data-value');
					listUsers = jQuery.grep(listUsers, function(value){
						return value != val;
					});
					$(this).parents('div.display-user:first').remove();
					$('div#popup_container div#'+idPopup+" div.todo-search input").trigger({type:'keyup',keyCode:13});
					$('form#<?= $formId; ?> div.added-user-input input[value="'+val+'"]').remove();
					$('form#<?= $formId; ?> span.count-user-add').text($('form#<?= $formId; ?> div.added-user-input input[name^=\"user_id\"]').length);
				});
			}else{
				$('div#popup_container div.dims-todo-popup#'+idPopup+' div.todo-added div.display-user').each(function(){
					listUsers.push($(this).attr('dims-data-value'));
				});
			}
			$('div#popup_container div#'+idPopup+" div.todo-search input").val(""); // on remet à 0 la recherche
			$('div#popup_container div#'+idPopup+" div.todo-results").empty();
			var hDoc = parseInt($(document).height()),
				top = parseInt(event.pageY);
			$('div#popup_container div#'+idPopup).css({'visibility':'visible','display':'block','top':event.pageY,'left':event.pageX});
			if(top+parseInt($('div#popup_container div#'+idPopup).outerHeight()) > hDoc-10){
				$('div#popup_container div#'+idPopup).css({'top':(hDoc-10-parseInt($('div#popup_container div#'+idPopup).outerHeight()))});
			}
			$('div#popup_container div#'+idPopup+" div.todo-search input").focus();
		});
	});
</script>
