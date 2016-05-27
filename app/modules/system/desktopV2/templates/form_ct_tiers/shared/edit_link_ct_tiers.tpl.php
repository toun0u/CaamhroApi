<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>" id="form-link-<?= $this->get('id'); ?>">
	<input type="hidden" name="submenu" value="1" />
	<input type="hidden" name="mode" value="<?= $this->getLightAttribute('mode'); ?>" />
	<input type="hidden" name="action" value="save_link" />
	<input type="hidden" name="id_tiers" value="<?= $this->get('id_tiers'); ?>" />
	<input type="hidden" name="id_ct" value="<?= $this->get('id_contact'); ?>" />
	<table>
		<tr>
			<td>
				<?= $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>
			</td>
			<td>
				<select name="lk_function">
					<option value="dims_nan"></option>
					<?php
					$sel = "SELECT 		DISTINCT function
							FROM 		".tiersct::TABLE_NAME."
							WHERE 		function != ''
							AND 		id_workspace = :idw
							GROUP BY 	function
							ORDER BY 	function";
					$params = array(
						':idw' => array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['workspaceid']),
					);
					$db = dims::getInstance()->getDb();
					$res = $db->query($sel,$params);
					while($r = $db->fetchrow($res)){
						if(trim($r['function']) == trim($this->get('function')))
							echo '<option value="'.trim($r['function']).'" selected=true>'.trim($r['function']).'</option>';
						else
							echo '<option value="'.trim($r['function']).'">'.trim($r['function']).'</option>';
					}
					?>
				</select>
				<a class="add-function-lk" href="javascript:void(0);"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png" /></a>
			</td>
		</tr>
		<tr>
			<td>
				<?= $_SESSION['cste']['_INFOS_START_DATE']; ?>
			</td>
			<td>
				<?php
				$dateDeb = "";
				if($this->get('date_deb') != '' && $this->get('date_deb')){
					$dateDeb = substr($this->get('date_deb'), 6,2)."/".substr($this->get('date_deb'), 4,2)."/".substr($this->get('date_deb'), 0,4);
				}
				?>
				<input type="text" value="<?= $dateDeb; ?>" name="date_deb" maxlength="10" style="width: 80px;" />
			</td>
		</tr>
		<tr>
			<td>
				<?= $_SESSION['cste']['_INFOS_END_DATE']; ?>
			</td>
			<td>
				<?php
				$dateFin = "";
				if($this->get('date_fin') != '' && $this->get('date_fin')){
					$dateFin = substr($this->get('date_fin'), 6,2)."/".substr($this->get('date_fin'), 4,2)."/".substr($this->get('date_fin'), 0,4);
				}
				?>
				<input type="text" value="<?= $dateFin; ?>" name="date_fin" maxlength="10" style="width: 80px;" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="actions" style="float:none;">
				<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
				<?= $_SESSION['cste']['_DIMS_OR']; ?>
				<a href="javascript:void(0);" onclick="javascript:$(this).parents('div.dims-link-popup:first').hide();">
					<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
				</a>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('form#form-link-<?= $this->get('id'); ?> select').chosen({width: "280px"});
	$('form#form-link-<?= $this->get('id'); ?> input[name="date_deb"]').datepicker({ 
		dateFormat: "dd/mm/yy",
		showOn: "both",
		buttonImage: "<?= _DESKTOP_TPL_PATH; ?>/gfx/common/planning16.png",
		buttonImageOnly: true,
		buttonText: "<?= $_SESSION['cste']['_INFOS_START_DATE']; ?>",
		onClose: function(date){
			$('form#form-link-<?= $this->get('id'); ?> input[type="text"]:last').datepicker("option", "minDate", date);
		},
	});
	$('form#form-link-<?= $this->get('id'); ?> input[name="date_fin"]').datepicker({ 
		dateFormat: "dd/mm/yy",
		minDate: '<?= $dateDeb; ?>',
		showOn: "both",
		buttonImage: "<?= _DESKTOP_TPL_PATH; ?>/gfx/common/planning16.png",
		buttonImageOnly: true,
		buttonText: "<?= $_SESSION['cste']['_TICKET_LIMIT_TIME_VALIDATION']; ?>",
	});
	$('form#form-link-<?= $this->get('id'); ?> img.ui-datepicker-trigger').css({"vertical-align":"sub", 'cursor':"pointer"});
	$('form#form-link-<?= $this->get('id'); ?>').delegate('a.add-function-lk','click',function(){
		var input = '<tr><td></td><td>\
						<input type="text" value="" class="input-function-lk" />\
						<a href="javascript:void(0);" class="valid-function-lk"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/check16.png" /></a>\
						<a href="javascript:void(0);" class="undo-function-lk"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/croix16.png" /></a>\
					</td></tr>';
		$(this).parents('tr:first').after(input);
		$(this).remove();
	}).delegate('input.input-function-lk','keydown',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	}).delegate('input.input-function-lk','keyup',function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
			$('form#form-link-<?= $this->get('id'); ?> a.valid-function-lk').click();
		}
	}).delegate('a.valid-function-lk','click',function(){
		var val = jQuery.trim($('input',$(this).parents('td:first')).val());
		if(val != ''){
			var opt = "<option value=\""+val+"\">"+val+"</option>";
			$('form#form-link-<?= $this->get('id'); ?> select').append(opt).val(val);
			$('form#form-link-<?= $this->get('id'); ?> select').trigger('liszt:updated');
			console.log($('form#form-link-<?= $this->get('id'); ?> select').val());
		}
		$('form#form-link-<?= $this->get('id'); ?> select').parents('td:first').append('<a class="add-function-lk" href="javascript:void(0);"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png" /></a>');
		$(this).parents('tr:first').remove();
	}).delegate('a.undo-function-lk','click',function(){
		$('form#form-link-<?= $this->get('id'); ?> select').parents('td:first').append('<a class="add-function-lk" href="javascript:void(0);"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png" /></a>');
		$(this).parents('tr:first').remove();
	});
});
</script>