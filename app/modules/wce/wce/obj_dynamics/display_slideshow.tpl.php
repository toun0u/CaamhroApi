<div class="title_h3">
    <h3><? echo $this->fields['nom']; ?> : Slideshow</h3>
</div>
<div class="lien_modification">
	<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_EDIT_ELEM."&id=".$this->fields['id']; ?>">
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
		<td class="title_table" style="text-align: center;">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$lst = $this->getElements();
	$nbElem = count($lst);
	if ($nbElem > 0){
		$class = 'class="table_ligne1"';
		foreach($lst as $obj){
			?>
			<tr <? echo $class; ?>>
				<td>
					<? echo $obj->fields['titre']; ?>
				</td>
				<td>
					<?
					$dd = dims_timestamp2local($obj->fields['timestp_modify']);
					echo $dd['date'];
					?>
				</td>
				<td class="actions" style="width: 85px;">
				<? $complement = ""; ?>
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_EDIT_ELEM."&id=".$this->fields['id']."&id_obj=".$obj->fields['id']; ?>">
						<img src="<? echo module_wce::getTemplateWebPath('gfx/icon_mini_modif.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
					</a>
					<?
					if($obj->fields['position'] <= 1){
						?>
						<img src="<? echo module_wce::getTemplateWebPath('gfx/arrow-up-dis.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_UP']; ?>" title="<? echo $_SESSION['cste']['_DIMS_UP']; ?>" />
						<?
					}else{
						?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_UP_ELEM."&id=".$this->fields['id']."&id_obj=".$obj->fields['id']; ?>">
							<img src="<? echo module_wce::getTemplateWebPath('gfx/arrow-up.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_UP']; ?>" title="<? echo $_SESSION['cste']['_DIMS_UP']; ?>" />
						</a>
						<?
					}
					if($obj->fields['position'] >= $nbElem){
						?>
						<img src="<? echo module_wce::getTemplateWebPath('gfx/arrow-bottom-dis.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_UP']; ?>" title="<? echo $_SESSION['cste']['_DIMS_UP']; ?>" />
						<?
					}else{
						?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_DOWN_ELEM."&id=".$this->fields['id']."&id_obj=".$obj->fields['id']; ?>">
							<img src="<? echo module_wce::getTemplateWebPath('gfx/arrow-bottom.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_DOWN']; ?>" title="<? echo $_SESSION['cste']['_DIMS_DOWN']; ?>" />
						</a>
						<?
					}
					?>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_DEL_ELEM."&id=".$this->fields['id']."&id_obj=".$obj->fields['id']; ?>','<? echo $_SESSION['cste']['ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');">
						<img src="<? echo module_wce::getTemplateWebPath('gfx/icon_mini_suppr.png'); ?>" alt="<? echo $_SESSION['cste']['_DELETE']; ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" />
					</a>
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