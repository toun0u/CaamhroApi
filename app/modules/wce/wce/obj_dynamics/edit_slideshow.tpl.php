<?php

?>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" name="save_obj">
		<input type="hidden" name="action" value="<? echo module_wce::_DYN_SLID_SAVE; ?>" />
		<input type="hidden" name="id" value="<? echo $this->fields['id']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
						</td>
						<td>
							<input type="text" name="obj_nom" value="<? echo $this->fields['nom']; ?>" />
						</td>
					</tr>
					<tr>
                        <td class="label">
                            <? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
                        </td>
                        <td>
                            <textarea name="obj_description"><?php echo $this->fields['description']; ?></textarea>
                        </td>
                    </tr>
					<tr>
						<td class="label">
							<? echo ucfirst($_SESSION['cste']['MODEL']); ?>
						</td>
						<td>
							<select name="obj_template">
								<option value=""><? echo $_SESSION['cste']['_DIMS_LABEL_UNDEFINED']; ?></option>
								<?php
                                foreach(wce_slideshow::getTemplates() as $tpl) {
                                    $sel = '';
                                    if($tpl == $this->fields['template']) {
                                        $sel = 'selected=true';
                                    }
                                    echo '<option '.$sel.' value="'.$tpl.'">'.$tpl.'</option>';
                                }
                                ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label">
							<? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>
						</td>
						<td>
							<input style="width: 60px;" type="text" name="obj_color" id="slideshow_color" value="<?php echo $this->fields['color']; ?>" rel="requis" />
							<a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('slideshow_color', event);">
								<img id="slideshow_color_img" src="./common/img/colorpicker/colorpicker.png" align="top" border="0">
							</a>
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