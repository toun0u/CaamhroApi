<?php
$view = view::getInstance();
?>
<div class="sub_bloc" id="<?= $this->getTitle(); ?>">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3><?= $title; ?></h3>
		<?php
	}
	?>

	<?= $this->get_field_html('id_article'); ?>

	<div class="sub_bloc_form">
		<table>
			<tr>
				<td class="label_field"><label for="selection_id"><?= $this->get_field_label('selection_id'); ?></label></td>
				<td class="value_field"><?= $this->get_field_html('selection_id'); ?></td>
			</tr>
			<tr>
				<td class="label_field"><label for"reference_search"><?= dims_constant::getVal('CATA_ADD_REFERENCE'); ?></label></td>
				<td class="value_field">
					<input type="text" id="reference_search" name="reference_search" placeholder="Référence ou mots-clés" class="w200p" autocomplete="off" />
					<a id="addArticle" href="javascript:void(0);" title="<?= dims_constant::getVal('_ADD');?>">
						<img src="<?= $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
					</a>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div id="ac_references" class="ac_container" style="display:none;">
						<ul id="ul_ac_references"></ul>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#addArticle').click(function() {
			var id_article = $('#id_article').val();
			console.log( id_article );
		});
	});
</script>
