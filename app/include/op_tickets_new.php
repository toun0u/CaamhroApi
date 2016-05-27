<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="tickets_new">
	<form method="post" style="padding:0;margin:0;">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("dims_op",	"tickets_send");
			$token->field("ticket_title");
			$token->field("ticket_needed_validation");
		?>
		<input type="hidden" name="dims_op" value="tickets_send">
		<table cellpadding="2" cellspacing="0" style="width:100%">
					<?
					if (isset($_GET['id_object'])) {
					?>
				<input type="hidden" name="id_object" value="<? echo dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>">
				<?
					$token->field("id_object", dims_load_securvalue('id_object', dims_const::_DIMS_NUM_INPUT, true, true, true));
				?>
					<?
					}
					if (isset($_GET['id_record'])) {
					?>
				<input type="hidden" name="id_record" value="<? echo dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true, true, true); ?>">
				<?
					$token->field("id_record", dims_load_securvalue('id_record', dims_const::_DIMS_NUM_INPUT, true, true, true));
				?>
					<?
					}
					if (isset($_GET['object_label'])) {
					?>
				<input type="hidden" name="object_label" value="<? echo dims_load_securvalue('object_label', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?>">
				<?
					$token->field("object_label", dims_load_securvalue('object_label', dims_const::_DIMS_CHAR_INPUT, true, true, true));
				?>
				<tr><td style="font-weight:bold;">Objet / Ref</td></tr>
				<tr>
					<td><? echo dims_load_securvalue('object_label', dims_const::_DIMS_CHAR_INPUT, true, true, true); ?></td>
				</tr>
					<?
					}
					?>
			<tr><td style="font-weight:bold;">Titre</td></tr>
			<tr>
				<td><input type="text" name="ticket_title" class="text" value="" style="width:480px;"></td>
			</tr>
			<tr><td style="font-weight:bold;">Message</td></tr>
			<tr>
				<td>
<?
					require_once(DIMS_APP_PATH . '/FCKeditor/fckeditor.php');

					$oFCKeditor = new FCKeditor('fck_ticket_message');

					$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
					if ($basepath == '/')
						$basepath = '';

					$oFCKeditor->BasePath = "{$basepath}/FCKeditor/";

					// default value
					if (isset($quoted))
						$oFCKeditor->Value = $ticket->fields['message'];
					//$oFCKeditor->Value= $article->fields['content'];
					// width & height
					$oFCKeditor->Width = '100%';
					$oFCKeditor->Height = '160';

					$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/system/fckeditor/fckconfig.js";
					//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
					$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/";
					$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css";
					$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
					$oFCKeditor->Create('FCKeditor_1');
?>
				</td>
			</tr>
			<tr><td colspan="2" style="font-weight:bold;"><input type="checkbox" name="ticket_needed_validation" value="1">&nbsp;Validation requise</td></tr>
			<tr>
				<td><? dims_tickets_selectusers(false, null, 480); ?></td>
			</tr>
			<tr>
				<td style="text-align:right;">
					<input type="submit" class="flatbutton" value="Envoyer" style="font-weight:bold;">
					<input type="button" class="flatbutton" value="Annuler" onclick="dims_getelem('dims_popup').style.visibility='hidden';">
				</td>
			</tr>
		</table>
		<?
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
	</form>
</div>
<?
die();
?>
