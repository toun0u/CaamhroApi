<?php

?>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" name="save_obj">
		<input type="hidden" name="action" value="<? echo module_wce::_DYN_OBJ_SAVE; ?>" />
		<input type="hidden" name="id" value="<? echo $this->fields['id']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
						</td>
						<td>
							<input type="text" name="obj_label" value="<? echo $this->fields['label']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DISPLAY']; ?>
						</td>
						<td>
							<select name="obj_template">
								<option value=""></option>
								<?
								$templates = array();
								$basepath = DIMS_APP_PATH.'templates'._DIMS_SEP.'objects';
								//dims_print_r($basepath);die("ici");

								if (!is_dir($basepath)) {
									dims_makedir($basepath);
								}

								$p = @opendir($basepath);

								while ($template = @readdir($p)) {
									$tplpath=realpath($basepath._DIMS_SEP.$template);
									if ($template != '.' && $template != '..' && is_dir($tplpath) && file_exists($tplpath._DIMS_SEP.'index.tpl') && file_exists($tplpath._DIMS_SEP.ucfirst($template).'Controller.php')) {
										$templates[strtolower ($template)] = $template;
									}
								}
								ksort($templates);
								foreach ($templates as $k => $template) {
									$optselected='';
									if ($this->fields['template']==$template) {
										$optselected = ' selected=true ';
									}
									echo '<option value="'.$template.'" '.$optselected.' >'.$template.'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label">
							Tenir compte de la date de fin de publication des articles ?
						</td>
						<td>
							<input type="checkbox" name="obj_pubfin_dependant" <? if ($this->fields['pubfin_dependant'] == 1) echo 'checked=true'; ?> value="1" />
						</td>
					</tr>
					<tr>
						<td class="label">
							Limite d'affichage des flux RSS pour cet objet (en heures)
						</td>
						<td>
							<input type="text" name="obj_limit_rss" size="2" value="<? echo $this->fields['limit_rss']; ?>" />
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_DEF; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>