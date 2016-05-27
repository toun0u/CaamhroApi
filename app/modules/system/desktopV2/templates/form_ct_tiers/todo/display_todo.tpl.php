<?php
$this->initDestinataires();
$lstCt = $this->getListDestinataires();
$inCatalogue = $this->getLightAttribute('in_catalogue');
?>
<div class="show-todo<?php if($this->get('state') == todo::TODO_STATE_VALIDATED){ echo " validated"; }?>" dims-data-value="<?= $this->get('id_globalobject'); ?>">
	<div>
		<input type="checkbox" <?= (!isset($lstCt[$_SESSION['dims']['userid']]))?"disabled=true":""; ?> value="<?= $this->get('id_globalobject'); ?>" <?php if($this->get('state') == todo::TODO_STATE_VALIDATED){?>checked="true"<?php } ?> />
		<span class="content"><?= $this->get('content'); ?></span>
		<span class="actions-todo">
			<?php
			if($this->get('state') != todo::TODO_STATE_VALIDATED && $this->get('user_from') == $_SESSION['dims']['userid']){
				?>
				<a href="javascript:void(0);" class="edit"><img src="<?= _DESKTOP_TPL_PATH; ?>gfx/contact/pencil.png" /></a>
				<a onclick="javascript:dims_confirmlink('<?= $this->getLightAttribute('remove_url'); ?>&id_todo=<?= $this->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');" href="javascript:void(0);" class="remove"><img src="<?= _DESKTOP_TPL_PATH; ?>gfx/contact/remove_black.png" /></a>
				<?php
			}
			?>
		</span>
	</div>
	<div style="padding-top:5px;padding-bottom: 3px;">
		<?php
		echo $_SESSION['cste']['_DIMS_LABEL_FROM'];

		if (!$inCatalogue) {
			if($this->get('user_from') == $_SESSION['dims']['userid']){
				echo " <a href=\"".dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$_SESSION['dims']['user']['id_contact']."\">".$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF']."</a>";
			}else{
				$user = user::find_by(array('id'=>$this->get('user_from')),null,1);
				if(!empty($user)){
					echo " <a href=\"".dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$user->get('id_contact')."\">".$user->get('firstname')." ".$user->get('lastname')."</a>";
				}
			}
		} else {
			echo '<strong>';
			if ($this->get('user_from') == $_SESSION['dims']['userid']) {
				echo " ".$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF'];
			} else {
				$user = user::find_by(array('id'=>$this->get('user_from')),null,1);
				if (!empty($user)) {
					echo " ".$user->get('firstname')." ".$user->get('lastname');
				}
			}
			echo '</strong>';
		}

		$cts = contact::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'account_id'=>array_keys($lstCt)), ' ORDER BY firstname, lastname ');
		$lstCt2 = array();

		if (!$inCatalogue) {
			$uct = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=";
			foreach($cts as $c){
				if($c->get('id') == $_SESSION['dims']['user']['id_contact']){
					$lstCt2[] = '<a href="'.$uct.$c->get('id').'">'.$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF'].'</a>';
				}else{
					$lstCt2[] = '<a href="'.$uct.$c->get('id').'">'.$c->getLabel().'</a>';
				}
			}
		} else {
			foreach ($cts as $c) {
				if ($c->get('id') == $_SESSION['dims']['user']['id_contact']) {
					$lstCt2[] = '<strong>'.$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF'].'</strong>';
				}else{
					$lstCt2[] = '<strong>'.$c->getLabel().'</strong>';
				}
			}
		}
		echo " ".$_SESSION['cste']['POUR']." ".implode(', ',$lstCt2);
		$dd = dims_timestamp2local($this->get('timestp_modify'));
		echo " ".$_SESSION['cste']['SINGLE_THE']." ".$dd['date']." - ";
		if($this->get('date') != "0000-00-00 00:00:00"){
			echo $_SESSION['cste']['_MATURITY']." : ".$_SESSION['cste']['SINGLE_THE']." ";
			$d = explode(" ",$this->get('date'));
			if(count($d) == 2){
				$dd = explode("-",$d[0]);
				echo $dd[2]."/".$dd[1]."/".$dd[0];
			}
		}else{
			echo ucfirst($_SESSION['cste']['_NOT_DUE']);
		}
		if($this->get('state') == todo::TODO_STATE_VALIDATED && $this->get('date_validation') != '0000-00-00 00:00:00'){
			$d = explode(" ",$this->get('date_validation'));
			if(count($d) == 2){
				$dd = explode("-",$d[0]);
				echo "<span class=\"validated-by\"> - ".$_SESSION['cste']['_VALIDATED_THE']." ".$dd[2]."/".$dd[1]."/".$dd[0];
				$user = user::find_by(array('id'=>$this->get('user_by')),null,1);
				if(!empty($user)){
					echo " ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM'])." <a href=\"".dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$user->get('id_contact')."\">".$user->get('firstname')." ".$user->get('lastname')."</a></span>";
				}
			}
		}
		?>
	</div>
</div>
<?php if(isset($lstCt[$_SESSION['dims']['userid']])){ ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('div.show-todo[dims-data-value="<?= $this->get('id_globalobject'); ?>"] input[type="checkbox"]').change(function(){
			if($(this).attr('checked') != undefined && $(this).attr('checked') == "checked"){
				$(this).parents('div.show-todo:first').addClass('validated');
				$("span.actions-todo",$(this).parents('div.show-todo:first')).remove();
				$("div:last",$(this).parents('div.show-todo:first')).append('<?= "<span class=\"validated-by\"> - ".$_SESSION['cste']['_VALIDATED_THE']." ".date("d/m/Y")." ".$_SESSION['dims']["user"]['firstname']." ".$_SESSION['dims']["user"]['lastname']; ?></span>')
				$.ajax({
					type: "POST",
					url: "<?= dims::getInstance()->getScriptEnv(); ?>",
					data: {
						'dims_op': 'desktopv2',
						'action': 'valid_todo',
						'id' : <?= $this->get('id_globalobject'); ?>,
					},
					dataType: "html",
					async: false,
					success: function(data){
					},
					error: function(data){}
				});
			}else{
				$(this).parents('div.show-todo:first').removeClass('validated');
				$("div:last span.validated-by",$(this).parents('div.show-todo:first')).remove();
				var actions = '	<span class="actions-todo">\
									<a href="javascript:void(0);" class="edit"><img src="<?= _DESKTOP_TPL_PATH; ?>gfx/contact/pencil.png" /></a>\
									<a onclick="javascript:dims_confirmlink(\'<?= $this->getLightAttribute('remove_url'); ?>&id_todo=<?= $this->get('id'); ?>\',\'<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>\');" href="javascript:void(0);" class="remove"><img src="<?= _DESKTOP_TPL_PATH; ?>gfx/contact/remove_black.png" /></a>\
								</span>';
				$(this).parents('div:first').append(actions);
				$.ajax({
					type: "POST",
					url: "<?= dims::getInstance()->getScriptEnv(); ?>",
					data: {
						'dims_op': 'desktopv2',
						'action': 'unvalid_todo',
						'id' : <?= $this->get('id_globalobject'); ?>,
					},
					dataType: "html",
					async: false,
					success: function(data){
					},
					error: function(data){}
				});
			}
		});
		$('div.show-todo[dims-data-value="<?= $this->get('id_globalobject'); ?>"]').delegate('a.edit','click',function(){
			$.ajax({
				type: "POST",
				url: "<?= dims::getInstance()->getScriptEnv(); ?>",
				data: {
					<?php
					if (!$inCatalogue) {
						?>
						'submenu': '1',
						'mode': '<?= $this->getLightAttribute('mode'); ?>',
						'action': 'edit_todo',
						'id' : <?= $this->get('id_globalobject'); ?>,
						'id_ct': <?= $this->getLightAttribute('id_ct'); ?>,
						<?php
					} else {
						$client = $this->getLightAttribute('client');
						?>
						'c': 'clients',
						'a': 'show',
						'sc': 'crm',
						'sa': 'edit_todo',
						'id_todo' : <?= $this->get('id_globalobject'); ?>,
						'id' : <?= $client->get('id_client'); ?>,
						<?php
					}
					?>
				},
				dataType: "html",
				async: false,
				success: function(data){
					$('div.show-todo[dims-data-value="<?= $this->get('id_globalobject'); ?>"]').html(data);
				},
				error: function(data){}
			});
		});
	});
</script>
<?php } ?>
