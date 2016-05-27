<div class="title_h3">
    <h3><? echo $this->fields['label']; ?> : <? echo $_SESSION['cste']['_ARTICLE']; ?></h3>
</div>
<div class="lien_modification">
	<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_EDIT_ART."&id=".$this->fields['id']; ?>">
		<? echo $_SESSION['cste']['_DIMS_ADD']; ?>
		<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_ajout.png'); ?>" />
	</a>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;margin-bottom: 30px;">
	<tr>
		<td class="title_table_accueil">
			<? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?>
		</td>
		<td class="title_table_accueil">
			<? echo $_SESSION['cste']['_MODIFIED_AT_MASC']; ?>
		</td>
		<td class="title_table" style="width: 75px;">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$lst = $this->getLightAttribute('articles');
	if (count($lst) > 0){
		$class = 'class="table_ligne1"';
		foreach($lst as $obj){
			?>
			<tr <? echo $class; ?>>
				<td>
					<? echo $obj['title']; ?>
				</td>
				<td>
					<?
					$dd = dims_timestamp2local($obj['timestp_modify']);
					echo $dd['date'];
					?>
				</td>
				<td class="actions">
					<?
					$complement = "";
					switch ($obj['type']){
						case 'article':
							$complement = "&id_art=".$obj['id'];
							break;
						case 'heading':
							$complement = "&id_head=".$obj['id'];
							break;
					}
					?>
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_EDIT_ART."&id=".$this->fields['id'].$complement; ?>">
						<img src="<? echo module_wce::getTemplateWebPath('gfx/icon_mini_modif.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
					</a>
					<img src="<? echo module_wce::getTemplateWebPath('gfx/icon_mini_suppr.png'); ?>" alt="<? echo $_SESSION['cste']['_DELETE']; ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" />
				</td>
			</tr>
			<?
			$class = ($class == '')?'class="table_ligne1"':'';
		}
	}else{
		?>
		<tr>
			<td colspan="5">
				<? echo $_SESSION['cste']['_NO_OBJECT_PRESENT']; ?>
			</td>
		</tr>
		<?
	}
	?>
</table>