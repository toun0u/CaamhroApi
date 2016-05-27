<?php
	require_once(DIMS_APP_PATH . '/include/class_todo.php');
	$id_todo=dims_load_securvalue('id_todo',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$element=dims_load_securvalue('element',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$moduleid=dims_load_securvalue('moduleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
	$recordid=dims_load_securvalue('recordid',dims_const::_DIMS_NUM_INPUT,true,true,false);

	$userid=dims_load_securvalue('userid',dims_const::_DIMS_NUM_INPUT,true,true,false);

	// initialisation de la structure todo
	$_SESSION['dims']['todo'] = array();
	$_SESSION['dims']['todo']['objectid']=$element;
	$_SESSION['dims']['todo']['recordid']=$recordid;
	$_SESSION['dims']['todo']['moduleid']=$moduleid;

	if ($id_todo==0) {
		echo $skin->open_widgetbloc($_DIMS['cste']['_ADDTO_DO'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '');
	}
	else {
		echo $skin->open_widgetbloc($_DIMS['cste']['_ADDTO_DO'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', '','', '', '', '', '', '', '');
	}

	$_SESSION['obj'][$element]['enabledfavorites']=true;

	if ($userid>0) {
		$_SESSION['obj'][$element]['users'][$userid]=$userid;

	}
?>
	<form name="form_todo" action="/admin.php?dims_op=save_todo" method="post">
	<input type="hidden" name="element" value="<? echo $element;?>">
	<table width="100%" cellpadding="0" cellspacing="2" style="background-color:#FFFFFF;padding-top:20px;padding-bottom:20px;">
		<tr>
			<td><? echo $_DIMS['cste']['_TYPE'];?></td>
			<td>
							<?
							if ($userid>0) {
									$sel1='checked';
									$sel0='';
							}
							else {
									$sel1='';
									$sel0='checked';
							}
							?>
							<input type="radio" onchange="javascript:validChangeTypeaddTodo();" name="todo_type" value="0" <? echo $sel0; ?>><? echo $_DIMS['cste']['_PERSO']; ?>
							<input type="radio" onchange="javascript:validChangeTypeaddTodo();" name="todo_type" value="1" <? echo $sel1; ?>><? echo $_DIMS['cste']['_USERS']; ?>
			</td>

			<td style="width:10%;text-align:left"><? echo $_DIMS['cste']['_FORM_TASK_PRIORITY'];?></td>
			<td style="width:30%;">
							<input type="radio" name="todo_priority" value="0"><? echo $_DIMS['cste']['_DIMS_LOW']; ?>
							<input type="radio" name="todo_priority" value="1" checked><? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_N']; ?>
							<input type="radio" name="todo_priority"  value="2"><? echo $_DIMS['cste']['_DIMS_HIGH']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?
			echo $_DIMS['cste']['_CONTENT'];
			?>
			</td>
			<td width="90%"  colspan="3">
				<textarea id="todo_content" name="todo_content" style="width:100%;height:60px;" tabindex="3"></textarea>
			</td>
			<td>
		</tr>
	</table>
	<?
	if ($userid>0) {
		echo '<div id="contentswitchtodo" style="visisibility:visible;display:block;width:100%;background-color:#FFFFFF;float:left;">';
	}
	else {
		echo '<div id="contentswitchtodo" style="visisibility:hidden;display:none;width:100%;background-color:#FFFFFF;float:left;">';
	}

	require_once(DIMS_APP_PATH . '/modules/system/form_searchusers.php');
	?>
	</div>
	<div style="padding:0px;overflow:auto;clear:both;width:100%;background-color:#FFFFFF;text-align:right">
	<input type="button" onclick="javascript:dims_hidepopup();" value="<? echo $_DIMS['cste']['_DIMS_CLOSE']; ?>" tabindex="5"/>
	<input type="submit" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>" tabindex="4"/>
</div>
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("element",	$element);
		$token->field("todo_type");
		$token->field("todo_priority");
		$token->field("todo_content");
		$token->field($_DIMS['cste']['_DIMS_SAVE']);
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
<?php
	echo $skin->close_widgetbloc();
?>
