<?php
$lst = $this->getLightAttribute('corresp');

?>
<form method="POST" action="/admin.php?import_op=<? echo _OP_SAVE_PREFERENCE; ?>">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_import", $this->fields['id']);
	?>
	<input type="hidden" value="<? echo $this->fields['id']; ?>" name="id_import" />
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th style="padding:5px;">
				<? echo $_SESSION['cste']['_EXCEL_FILE']; ?>
			</th>
			<th style="padding:5px;">
				<? echo $_SESSION['cste']['_DIMS_LABEL_CONTACT']; ?>
				<input name="type_<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>" checked=true type="checkbox" value="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>" class="active_type" />
				<?
					$token->field("type_".dims_const::_SYSTEM_OBJECT_CONTACT);
				?>
			</th>
			<th style="padding:5px;">
				<? echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?>
				<input name="type_<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>" checked=true type="checkbox" value="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>" class="active_type" />
				<?
					$token->field("type_".dims_const::_SYSTEM_OBJECT_TIERS);
				?>
			</th>
		</tr>
		<?
		// traitement des tags à importer
		$arrayTags=array();

		foreach($lst as $key => $val){
			if (substr($key,0,4)==="tag ") {
				//echo "Tag trouve : ".substr($key,4)."<br>";
				$arrayTags[]=substr($key,4);
				unset($lst[$key]);
			}
		}



		// on nettoie les tags deja crees
		$this->deleteTags();

		// on ajoute les tags non connus
		foreach ($arrayTags as $tag) {
			$this->checkTag($tag);
			echo "Tag trouvé :".$tag."<br>";
		}

		foreach($lst as $key => $val){

			?>
			<tr>
				<td>
					<? echo $key; ?>
				</td>
				<td>
					<?
					if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT])){
						?>
						<select name="field_ct_<? echo $key; ?>" rel="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>">
							<?
								$token->field("field_ct_".$key);
							?>
							<option value="dims_nan" rel="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>">--</option>
						<?
						foreach($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT] as $val2){
							$sel = "";
							if (isset($val[dims_const::_SYSTEM_OBJECT_CONTACT]) && $val[dims_const::_SYSTEM_OBJECT_CONTACT] == $val2['id_mtf'])
								$sel = " selected=true ";
							?>
							<option <? echo $sel; ?> ref="<? echo $val2['namefield']; ?>" value="<? echo $val2['id_mtf']; ?>"><? echo (isset($_SESSION['cste'][$val2['titlefield']])?$_SESSION['cste'][$val2['titlefield']]:$val2['titlefield']).(($val2['protected'])?" *":""); ?></option>
							<?
						}
						?>
						</select>
						<?
					}
					?>
				</td>
				<td>
					<?

					if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS])){
						?>
						<select name="field_tiers_<? echo $key; ?>" rel="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>">
							<?
								$token->field("field_tiers_".$key);
							?>
							<option value="dims_nan" rel="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>">--</option>
						<?
						foreach($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS] as $val2){
							$sel = "";
							if (isset($val[dims_const::_SYSTEM_OBJECT_TIERS]) && $val[dims_const::_SYSTEM_OBJECT_TIERS] == $val2['id_mtf']) {

								$sel = " selected=true ";
							}
							?>
							<option <? echo $sel; ?> ref="<? echo $val2['namefield']; ?>" value="<? echo $val2['id_mtf']; ?>"><? echo (isset($_SESSION['cste'][$val2['titlefield']])?$_SESSION['cste'][$val2['titlefield']]:$val2['titlefield']).(($val2['protected'])?" *":""); ?></option>
							<?
						}
						?>
						</select>
						<?
					}
					if (isset($val[dims_const::_SYSTEM_OBJECT_TIERS])){

					}
					?>
				</td>
			</tr>
			<?
		}
		?>
		<tr>
			<td></td>
			<td id="valid_<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>"></td>
			<td id="valid_<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right;">
				<input onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv()."?import_op="._OP_DELETE_IMPORT."&id_import=".$this->fields['id']; ?>';" type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
				<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_EXECUTE']; ?>" />
			</td>
		</tr>
	</table>
	<?
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('input.active_type[type="checkbox"]').click(function(){
			if ($(this).is(':checked'))
				$('select[rel="'+$(this).val()+'"]').attr('disabled',false);
			else
				$('select[rel="'+$(this).val()+'"]').attr('disabled',true);
		});
		$('select').change(function(){checkType();});
		checkType();
	});
	function checkType(){
		var firstname = false;
		var lastname = false;
		$('select[rel="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>"]').each(function(){
			var ref = $('option:selected',$(this)).attr('ref');
			if (ref == 'firstname')
				firstname = true;
			if (ref == 'lastname')
				lastname = true;
		});
		if (firstname && lastname)
			$('td#valid_<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>').html('Ok');
		else
			$('td#valid_<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>').html('Manque Prénom ou Nom');

		var intitule = false;
		$('select[rel="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>"]').each(function(){
			var ref = $('option:selected',$(this)).attr('ref');
			if (ref == 'intitule')
				intitule = true;
		});
		if (intitule)
			$('td#valid_<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>').html('Ok');
		else
			$('td#valid_<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>').html('Manque Intitule');
	}
</script>