<div class="title_h3">
	<h3><? echo $_SESSION['cste']['_DOMAIN_NAMES']; ?></h3>
</div>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_PARAM); ?>" name="save_domain">
		<input type="hidden" name="sub" value="<? echo module_wce::_PARAM_CONF; ?>" />
		<input type="hidden" name="action" value="<? echo module_wce::_PARAM_CONF_SAVE_DOMAIN; ?>" />
		<input type="hidden" name="id" value="<? echo $this->fields['id']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_DOMAIN']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['domain']; ?>" name="dom_domain" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								Version mobile
							</label>
						</td>
						<td>
							<input type="checkbox" value="1" name="dom_mobile" <? echo ($this->fields['mobile'])?"checked=true":""; ?> />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_SSLACCESS']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" value="1" name="dom_ssl" <? echo ($this->fields['ssl'])?"checked=true":""; ?> />
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>