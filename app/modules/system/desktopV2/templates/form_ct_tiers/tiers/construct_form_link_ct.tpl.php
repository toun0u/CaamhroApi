<?php
$mode = 'contact';
if($id_ct != '' && $id_ct > 0){
	$ct = contact::find_by(array('id'=>$id_ct, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
}elseif($id_tiers != '' && $id_tiers > 0){
	$ct = tiers::find_by(array('id'=>$id_tiers, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
	$mode = 'company';
	$id_ct = $id;
	$id = $id_tiers;
}
if(empty($ct)){
	$ct = new contact();
	$ct->init_description();
}
?>

<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=<?= $mode; ?>&action=add_link_ct_tiers" id="link_to_ct">
	<input type="hidden" name="id_ct" value="<?= $id_ct; ?>" />
	<input type="hidden" name="id_tiers" value="<?= $id; ?>" />
	<label for="function">
		<?= str_replace('{DIMS_TEXT}', $ct->getLabel(), $_SESSION['cste']['_FUNCTION_WITHIN_THIS_STRUCTURE']); ?>
	</label><br />
	<select name="function" style="width:25%;">
		<option value="dims_nan"></option>
		<?php
		require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
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
		while($r = $db->fetchrow($res)){
			?>
			<option value="<?= $r['function']; ?>"><?= $r['function']; ?></option>
			<?php
		}
		?>
	</select>
	<div style="margin-top:10px;">
		<input type="submit" class="submit" value="<?= $_SESSION['cste']['_DIMS_VALID']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="javascript:void(0);" class="undo_lk">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('form#link_to_ct select[name="function"]').after('<img onclick="javascript:addFunction2(this);" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png" style="cursor:pointer;" />');
		$('form#link_to_ct select[name="function"]').chosen({width:"25%"});
		$('form#link_to_ct a.undo_lk').click(function(){
			var keydown = jQuery.Event('keydown');
			keydown.keyCode = 13;
			keydown.which = 13;
			$('div#add_tiers input.desktop_editbox_search').trigger(keydown);
		});
	});
	if(window['addFunction2'] == undefined){
		window['addFunction2'] = function addFunction2(img){
			if($('div.add_function', $(img).parents('form#link_to_ct')).length <= 0){
				var input = '<div class="add_function"><input type="text" name="bis_function" style="width:350px;" /><img onclick="javascript:validNewFunction2(this);" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/check16.png" style="cursor:pointer;" /><img style="cursor:pointer;" onclick="javascript:$(this).parents(\'div.add_function:first\').hide();" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/croix16.png" /></div>';
				$(img).after(input);
				$('div.add_function input',$(img).parents('td:first')).focus();
			}
			$('div.add_function input', $(img).parents('form#link_to_ct')).val('');
			$('div.add_function', $(img).parents('form#link_to_ct')).show();
		}
	}
	if(window['validNewFunction2'] == undefined){
		window['validNewFunction2'] = function validNewFunction2(img){
			if($('input',$(img).parents('div.add_function')).val() != ''){
				var opt = "<option value=\""+$('input',$(img).parents('div.add_function')).val()+"\">"+$('input',$(img).parents('div.add_function')).val()+"</option>";
				$('select[name="function"]').append(opt);
				$('select[name="function"]',$(img).parents('form#link_to_ct')).val($('input',$(img).parents('div.add_function')).val());
				$('select[name="function"]').trigger('liszt:updated');
				$('div.add_function', $(img).parents('form#link_to_ct')).hide();
			}
		}
	}
</script>
