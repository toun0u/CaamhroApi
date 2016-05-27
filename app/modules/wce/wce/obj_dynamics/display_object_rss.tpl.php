<div class="title_h3">
    <h3><? echo $this->fields['label']; ?> : <? echo $_SESSION['cste']['_RSS_LIST']; ?></h3>
</div>

<div class="lien_modification">
	<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=edit_rss&id=".$this->fields['id']; ?>">
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
			<? echo $_SESSION['cste']['_DIMS_LABEL_WEB_ADDRESS']; ?>
		</td>
		<td class="title_table_accueil">
			<?= $_SESSION['cste']['_NB_ELEMENTS']; ?>
		</td>
		<td class="title_table" style="width: 75px;">
			<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</td>
	</tr>
	<?
	$lst = $this->getLightAttribute('rss');
	if (count($lst) > 0){
		$class = 'class="table_ligne1"';
		foreach($lst as $obj){
			?>
			<tr <? echo $class; ?>>
				<td>
					<?= $obj->get('title'); ?>
				</td>
				<td>
					<?= $obj->get('url'); ?>
				</td>
				<td>
					<?= count($obj->getRssCache()); ?>
				</td>
				<td class="actions">
					<?
					$complement = "";
					?>
					<!--<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=edit_rss&id=".$this->fields['id']."&rss=".$obj->get('id'); ?>">
						<img src="<? echo module_wce::getTemplateWebPath('gfx/icon_mini_modif.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
					</a>-->
					<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=update_rss&id=".$this->fields['id']."&rss=".$obj->get('id'); ?>">
						<img src="/common/img/reload.png" alt="<? echo $_SESSION['cste']['_RSS_LABELTAB_MODIFY']; ?>" title="<? echo $_SESSION['cste']['_RSS_LABELTAB_MODIFY']; ?>" />
					</a>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=del_rss&id=".$this->fields['id']."&rss=".$obj->get('id'); ?>','<? echo $_SESSION['cste']['ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');">
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
