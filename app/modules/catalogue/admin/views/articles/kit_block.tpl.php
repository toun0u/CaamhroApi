<?php
$view = view::getInstance();
$form = $this->getForm();
$article = $form->getObject();
?>
<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3>
			<?php echo $title; ?>
			<?= $this->get_field_html('kit_composition'); ?>
			<span id="article_kit">
				<?= $this->get_field_html('article_kit', '0'); ?>
				<label for="<?= $this->get_field_id('article_kit', '0'); ?>"><?= $this->get_field_label('article_kit', '0'); ?></label>

				<?= $this->get_field_html('article_kit', '1'); ?>
				<label for="<?= $this->get_field_id('article_kit', '1'); ?>"><?= $this->get_field_label('article_kit', '1'); ?></label>
			</span>
			<span class="legend legend08"><?= dims_constant::getVal('ACTIVATE_OPTION_IF_KIT'); ?></span>
		</h3>

		<?php
	}
	?>
	<div id="block_composition" class="sub_bloc_form" style="display:<?php if ($article->fields['kit']) echo 'block'; else echo 'none'; ?>">
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
				'name'		=> 'kit_qty',
				'classes'		=> 'temp_message',
				'value'		=> dims_constant::getVal('QUANTITY')
			));
			?>
			<a href="javascript:void(0);" onclick="javascript:addComponent();" title="<?= dims_constant::getVal('ADD_COMPONENT');?>">
				<img src="<?=  $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
			</a>
		</div>
		<table class="tableau" id="table_kit">
			<tr id="kit_headings">
				<td class="w10 title_tableau">
					&nbsp;
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('REF'); ?>
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('DESIGNATION'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('QUANTITY'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('PU_HT'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('_DUTY_FREE_AMOUNT'); ?>
				</td>
				<td class="w100p title_tableau">
					<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
				</td>
			</tr>

			<?php
			$kit = $view->get('kit');
			$total_kit = $win_kit = 0;
			$content_for_kit_compo = '';
			if( ! empty( $kit ) ){
				foreach( $kit as $k ){
					$subart = $k->getLightAttribute('component');
					$path = $subart->getVignette(20);
					$total_kit += $k->fields['quantity'] * $subart->getPUHT();
					$content_for_kit_compo .= '<input type="hidden" id="kit_hf_'.$subart->get('id').'" name="kit_composition['.$subart->get('id').']" value="'.$k->fields['quantity'].'"/>';
					?>
					<tr id="kit_row_<?= $subart->get('id');?>">
						<td>
							<?php if( ! is_null($path) ) { ?><img src="<?= $path; ?>" /><?php } ?>
						</td>
						<td><?= $subart->fields['reference']; ?></td>
						<td><?= $subart->fields['label']; ?></td>
						<td class="value_right"><?= $k->fields['quantity'] ; ?></td>
						<td class="value_right"><?= $subart->getPUHT(); ?></td>
						<td class="kit_row_total value_right"><?= $k->fields['quantity'] * $subart->getPUHT(); ?></td>
						<td class="kit_actions">
							<a href="javascript:void(0);" onclick="javascript:if(confirm('<?= addslashes(dims_constant::getVal('ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?')); ?>')) removeComponent(<?= $subart->get('id'); ?>);" title="<?= dims_constant::getVal('ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a>
						</td>
					</tr>
					<?php
				}
				$win_kit = $total_kit - $article->getPUHT();
			}

			?>

			<tr style="background-color: #FAFAFA;">
				<td colspan="6" style="border:0;text-align:right;">
					<strong><?= dims_constant::getVal('NOMINAL_TOTAL').' : '; ?></strong>
				</td>
				<td style="border:0;text-align:right;" id="total_kit"><?= $total_kit; ?></td>
			</tr>
			<tr style="background-color: #FAFAFA;">
				<td colspan="6" style="border:0;text-align:right;">
					<strong><?= dims_constant::getVal('BENEFIT_HT').' : '; ?></strong>
				</td>
				<td style="border:0;text-align:right;" id="win_kit"><?= $win_kit; ?></td>
			</tr>
		</table>
		<div id="kit_compo" style="display:none;"><?= $content_for_kit_compo; ?></div>
	</div>
</div>

<script type="text/javascript">
	$('#article_kit').buttonset();

	$("input[name='article_kit']").change(function(){
		if( $(this).val() == 1 )
			$("#block_composition").show();
		else $("#block_composition").hide();
	});

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
	.dims_autocomplete( { c : 'articles', a : 'ac_articles' }, 3, 500, '#ref_article', '#ac_references', '#ul_ac_references', '<li>${reference} - ${label}</li>', '<?php echo addslashes(dims_constant::getVal('NO_REFERENCE')); ?>', null );

	$('#kit_qty').focus(function(){
		if($(this).val() == '<?= addslashes(dims_constant::getVal('QUANTITY')); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= addslashes(dims_constant::getVal('QUANTITY')); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
		if ( event.which == 13 ) {
			event.preventDefault();
			addComponent();
		}
	});

	$('#article_putarif_0').focusout(function(){
		updateKitTotaux(null, null);
	});

	function getTotalKit(){
		var total = 0;
		$('td.kit_row_total').each(function(){
			var row_total = parseFloat($(this).text());
			if(row_total != null && row_total != 'undefined'){
				total += row_total;
			}
		});
		return total;
	}
	function updateKitTotaux(total, win){
		if(total == null && win == null && $("input[name='article_kit']:checked").val() == 1){
			//Dans ce cas on doit le calculer en bénéficiant de money_format via un appel ajax
			var puht = parseFloat($('#article_putarif_0').val());
			var total_kit = getTotalKit();
			$.ajax({
				type: "GET",
				url: 'admin.php',
				async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
				data : {
					'c' 	: 'articles',
					'a' 	: 'js_get_kit_amounts',
					'puht'	: puht,
					'total' : total_kit
				},
				dataType: "json",
				success: function(data){
					updateKitTotaux(data.kit_total, data.kit_win);
				}
			});
		}
		else{
			$('#total_kit').text(total);
			$('#win_kit').text(win);
		}
	}

	function removeComponent(id){
		 $('#kit_row_'+id).remove();
		 $('#kit_hf_'+id).remove();
		 updateKitTotaux(null, null);
	}

	function addComponent(){
		var ref = $("#ref_article").val();
		var qty = $("#kit_qty").val();
		var integer = /^\d*$/;
		var tpl_noimg = '<tr id="kit_row_${id}"><td></td><td>${ref}</td><td>${label}</td><td class="value_right">${quantity}</td><td class="value_right">${puht}</td><td class="kit_row_total value_right">${total}</td><td class="kit_actions"></td></tr>';
		var tpl_img = '<tr id="kit_row_${id}"><td><img src="${photo_path}" /></td><td>${ref}</td><td>${label}</td><td class="value_right">${quantity}</td><td class="value_right">${puht}</td><td class="kit_row_total value_right">${total}</td><td class="kit_actions"></td></tr>';

		if(qty != 'undefined' && ref != 'undefined' && qty != '' && ref != '' && qty != '<?= addslashes(dims_constant::getVal('QUANTITY')); ?>' && ref != '<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>' && qty.match(integer)){
			//On supprime éventuellement la ligne qui existerait déjà
			$('#kit_row_'+ref).remove();
			$('#kit_hf_'+ref).remove();
			var current_total = getTotalKit();
			//Toutes les conditions sont remplies pour pouvoir ajouter la ligne
			//Récupération des infos de l'article

			$.ajax({
				type: "GET",
				url: 'admin.php',
				async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
				data : {
					'c' 	: 'articles',
					'a' 	: 'js_article_info',
					'id'	: ref,
					'qty'	: qty,
					'puht'	: $('#article_putarif_0').val(),
					'total' : current_total
				},
				dataType: "json",
				success: function(data){
					var tpl_used = tpl_noimg;
					if(data.photo_path != null && data.photo_path != 'undefined'){
						tpl_used = tpl_img;
					}
					data.quantity = qty;
					var row = $.tmpl(tpl_used, data);
					$('#kit_headings').after(row);
					$('#kit_row_'+ref+' td.kit_actions').append('<a href="javascript:void(0);" onclick="javascript:if(confirm(\'<?= addslashes(dims_constant::getVal('ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?')); ?>\')) removeComponent('+ref+');" title="<?= dims_constant::getVal('ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a>');
					row.effect("highlight", {color:'#FC8617'}, 1500);
					$('#kit_compo').append('<input type="hidden" id="kit_hf_'+ref+'" name="kit_composition['+ref+']" value="'+qty+'"/>');

					//Mise à jour des totaux
					updateKitTotaux(data.kit_total, data.kit_win);

					//On vide les champs
					$('#kit_qty').val('<?= dims_constant::getVal('QUANTITY'); ?>').addClass('temp_message');
					$('#search_article').val('<?= dims_constant::getVal('REFERENCE').', '.dims_constant::getVal('DESIGNATION'); ?>').addClass('temp_message').focus();
				}
			});
		}
	}
</script>
