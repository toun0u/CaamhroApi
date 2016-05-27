<?php
$view = view::getInstance();
$promo = $view->get('promo');
$form = $this->getForm();
?>

<h1><?= dims_constant::getVal('ARTICLES_CONCERNED'); ?></h1>

<?php
show_guide(dims_constant::getVal('VOUS_POUVEZ_EGALEMENT_PRECISER_LA_REDUCTION'));
?>

<div class="sub_bloc">
	<div class="sub_bloc_form">
		<label for="articles_file"><?= $this->get_field_label('articles_file'); ?></label>
		<?= $this->get_field_html('articles_file'); ?>

		<br/>
		<?= $this->get_field_html('articles_keep'); ?>
		<label for="articles_keep"><?= $this->get_field_label('articles_keep'); ?></label>
	</div>
</div>

<br/>
<div class="sub_bloc">
	<div class="sub_bloc_form">
		<div id="add_fields">
			<?= $form->text_field(array(
				'name'		=> 'search_article',
				'classes'	=> 'temp_message w300p',
				'value'		=> dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION')
			));
			?>
			<?= $form->hidden_field(array(
				'name'		=> 'ref_article'
			));
			?>
			<div id="ac_references" class="ac_container" style="display:none;">
				<ul id="ul_ac_references">
				</ul>
			</div>

			<?= $form->text_field(array(
				'name'		=> 'promo_prix',
				'classes'	=> 'temp_message',
				'value'		=> dims_constant::getVal('PU_HT')
			));
			?>
			<a href="javascript:void(0);" onclick="javascript:addComponent();" title="<?= dims_constant::getVal('ADD_COMPONENT');?>">
				<img src="<?=  $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
			</a>
		</div>

		<table class="tableau">
			<tr id="promo_headings">
				<td class="w25 title_tableau"><?= dims_constant::getVal('REF'); ?>.</td>
				<td class="w30 title_tableau"><?= dims_constant::getVal('_DESIGNATION'); ?></td>
				<td class="w30 title_tableau"><?= dims_constant::getVal('PU_HT'); ?> €</td>
				<td class="w2 title_tableau"><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></td>
			</tr>
			<?php
			$content_for_promo_compo = '';
			foreach ($promo->getAllArticles() as $object) {
				$article = $object['article'];
				$content_for_promo_compo .= '<input type="hidden" id="promo_hf_'.$article->get('id').'" name="promo_composition['.$article->get('id').']" value="'.$object['prix'].'"/>';
				?>
				<tr id="promo_row_<?= $article->get('id'); ?>">
					<td><?= $article->getReference(); ?></td>
					<td><?= $article->getLabel(); ?></td>
					<td id="prix_<?= $article->get('id'); ?>" class="right"><?= $object['prix']; ?></td>
					<td class="center">
						<a href="javascript:void(0);" onclick="javascript:if(confirm('<?= dims_constant::getVal('_DELETE'); ?>')) removeComponent(<?= $article->get('id'); ?>);" title="<?= dims_constant::getVal('_DELETE'); ?>">
							<img src="<?=  $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" /></a>
					</td>
				</tr>
				<?php
			}
			?>
		</table>

		<div id="promo_compo" style="display:none;"><?= $content_for_promo_compo; ?></div>
	</div>
</div>

<script type="text/javascript">
	$('#search_article').focus(function(){
		if($(this).val() == '<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
		if ( event.which == 13 ) {
			event.preventDefault();
			addComponent();
		}
	})
	.dims_autocomplete( { c : 'promotions', a : 'ac_articles' }, 3, 500, '#ref_article', '#ac_references', '#ul_ac_references', '<li>${reference} - ${label}</li>', '<?php echo addslashes(dims_constant::getVal('NO_REFERENCE')); ?>', null );

	$('#promo_prix').focus(function(){
		if($(this).val() == '<?= addslashes(dims_constant::getVal('PU_HT')); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= addslashes(dims_constant::getVal('PU_HT')); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
		if ( event.which == 13 ) {
			event.preventDefault();
			addComponent();
		}
	});

	function addComponent() {
		var ref = $('#ref_article').val();
		var prix = $("#promo_prix").val();
		var tpl = '<tr id="promo_row_${id}"><td>${ref}</td><td>${label}</td><td id="prix_${id}" class="right">${prix}</td><td class="promo_actions center"></td></tr>';

		if (ref != '' && prix != '') {
			$.ajax({
				type: 'GET',
				url: 'admin.php',
				data: {
					'c'		: 'promotions',
					'a'		: 'js_article_info',
					'id'	: ref,
					'prix'	: prix
				},
				async: 'false',
				dataType: 'json',
				success: function(data) {
					var row = $.tmpl(tpl, data);
					$('#promo_headings').after(row);
					$('#promo_row_'+ref+' td.promo_actions').append('<a href="javascript:void(0);" onclick="javascript:removeComponent('+ref+');" title="<?= dims_constant::getVal('_DELETE'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" /></a>');
					row.effect('highlight', {color: '#FC8617'}, 1500);
					$('#promo_compo').append('<input type="hidden" id="promo_hf_'+ref+'" name="promo_composition['+ref+']" value="'+prix+'"/>');
				}
			});
		}

		// On vide les champs
		$('#promo_prix').val('<?= dims_constant::getVal('PU_HT'); ?>').addClass('temp_message');
		$('#search_article').val('<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>').addClass('temp_message').focus();
	}

	function removeComponent(id){
		 $('#promo_row_'+id).remove();
		 $('#promo_hf_'+id).remove();
	}
</script>
