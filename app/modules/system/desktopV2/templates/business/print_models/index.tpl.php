<?php
$dims = dims::getInstance();
?>
<div class="admin_models">
	<h2><?= $_SESSION['cste']['PRINTING_MODELS_LIST']; ?></h2>
	<div class="sub">
		<div class="action">
			<a href="<?= $dims->getScriptEnv().'?models_action=edit';?>"><img src="<?= _DESKTOP_TPL_PATH.'/gfx/common/add.png';?>"/><?= $_SESSION['cste']['NEW_MODEL']; ?></a>
		</div>
		<?php
		if(count($models)){
			?>
			<table class="list_models">
				<tr><th><?= $_SESSION['cste']['_TYPE']; ?></th><th><?= $_SESSION['cste']['_DIMS_LABEL']; ?></th><th><?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></th><th class="actions"><?= $_SESSION['cste']['_DIMS_ACTIONS']; ?></th></tr>
				<?php
				foreach($models as $model){
					?>
					<tr>
						<td><?= $model->fields['type_label']; ?></td>
						<td><?= $model->getLabel(); ?></td>
						<td><?= nl2br($model->getDescription()); ?></td>
						<td>
							<a href="<?= $dims->getScriptEnv().'?models_action=edit&id='.$model->getId();?>"><?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></a>&nbsp;|&nbsp;
							<a href="javascript:void(0);" onclick="javascript:if(confirm('<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>')) document.location.href='<?= $dims->getScriptEnv().'?models_action=delete&id='.$model->getId();?>';"><?= $_SESSION['cste']['_DELETE']; ?></a>&nbsp;|&nbsp;
							<a  href="<?= dims_urlencode($dims->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id=".$model->fields['md5id']); ?>"><?= $_SESSION['cste']['_DIMS_DOWNLOAD']; ?></a>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		else{
			?>
			<div class="empty"><?= $_SESSION['cste']['NO_MODEL_FOR_THIS_WORKSPACE']; ?></div>
			<?php
		}
		?>
	</div>
</div>