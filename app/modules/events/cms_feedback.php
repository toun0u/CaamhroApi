<div id="content2_2">
	<div class="title"><? echo $_DIMS['cste']['_DIMS_LABEL_FEEDBACK']; ?></div>
	<div class="historic"></div>
</div>
<div id="contener2">
	<div id="content2_3">
		<table cellpadding="0" cellspacing="0" style="width:99%;">
				<tr>
					<td>
						<div style="margin-bottom: 10px; overflow: hidden;">
							<img src="./common/modules/events/img/icon_feedback_mini.png" border="0" style="float:left;margin-right: 10px;" alt="feedback" title="feedback">
								<span style="float: left;line-height:33px;font-family:trebuchet MS;font-size: 16px;color: #424242">
									<? echo $_DIMS['cste']['_DIMS_EVT_LEAVE_YOUR_FEEDBACK']; ?>
								</span>
						</div>
					</td>
				</tr>
		</table>
		<table class="table_feedback" cellpadding="0" cellspacing="0">
			<tr>
				<td class="title_form"><?= dims_constant::getVal('_SUBJECT'); ?> <span style="color:red">*</span></td>
				<td>
					<select name="fonction">
						<option value="enseignant"><?= dims_constant::getVal('SUGGESTION'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="title_form_desc"><?= dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'); ?> <span style="color:red">*</span></td>
				<td>
					<textarea rows="3" name="commentaires"></textarea>
				</td>
			</tr>
			<tr>
				<td class="title_form"><?= dims_constant::getVal('_FORM_TASK_PRIORITY'); ?> <span style="color:red">*</span></td>
				<td>
					<select name="fonction">
						<option value="enseignant"><?= dims_constant::getVal('_DIMS_LOW'); ?></option>
						<option value="etudiant"><?= dims_constant::getVal('MEDIUM'); ?></option>
						<option value="ingenieur"><?= dims_constant::getVal('HIGH'); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<table class="table_feedback action" cellpadding="0" cellspacing="0">
			<tr>
				<td style="text-align: right;">
					<span style="color: #CC262C">* <? echo $_DIMS['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span>
					<input class="boutton_submit" type="submit" value="<? echo $_DIMS['cste']['_DIMS_SEND']; ?>" />
					or
					<a href="/index.php?article_id=<? echo $_SESSION['wce'][$_SESSION['dims']['moduleid']]['articleid']; ?>" style="color: #CC262C;"><?= dims_constant::getVal('_DIMS_LABEL_CANCEL'); ?></a>
				</td>
			</tr>
		</table>
	</div>
</div>
