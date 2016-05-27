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
		<h3><?php echo $title; ?></h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<div class="price_stock">
			<div>
				<table>
					<tr>
						<td class="label_field "><label for="article_putarif_0"><?= $this->get_field_label('article_putarif_0'); ?></label></td>
						<td class="value_field "><?= $this->get_field_html('article_putarif_0'); ?></td><td class=""><img src="<?=  $view->getTemplateWebPath('gfx/euro16.png'); ?>" /></td>
						<td class="label_field"><label for="article_qte"><?= $this->get_field_label('article_qte'); ?></label></td>
						<td class="value_field"><?= $this->get_field_html('article_qte'); ?></td>

					</tr>
					<tr>
						<td class="label_field "><label for="article_ctva"><?= $this->get_field_label('article_ctva'); ?></label></td>
						<td class="value_field "><?= $this->get_field_html('article_ctva'); ?></td><td class=""></td>
						<td class="label_field "><label for="article_qte_mini"><?= $this->get_field_label('article_qte_mini'); ?></label></td>
						<td class="value_field "><?= $this->get_field_html('article_qte_mini'); ?></td>
					</tr>
					<tr>
						<td class="label_field "><label for="article_rempromo_1"><?= $this->get_field_label('article_rempromo_1'); ?></label></td>
						<td class="value_field "><?= $this->get_field_html('article_rempromo_1'); ?></td><td class=""><img src="<?=  $view->getTemplateWebPath('gfx/pourcent16.png'); ?>" /></td>
						<td class="label_field "><label for="article_uvente"><?= $this->get_field_label('article_uvente'); ?></label></td>
						<td class="value_field "><?= $this->get_field_html('article_uvente'); ?></td>
					</tr>
					<tr>
						<td class="label_field"><label for="article_poids"><?= $this->get_field_label('article_poids'); ?></label></td>
						<td class="value_field"><?= $this->get_field_html('article_poids'); ?></td>
						<td></td>
						<td class="label_field"><label for="article_shipping_costs"><?= $this->get_field_label('article_shipping_costs'); ?></label></td>
						<td class="value_field"><?= $this->get_field_html('article_shipping_costs'); ?></td>
					</tr>
					<tr>
						<td class="label_field"><label for="article_txt_delai_livraison_stock"><?= $this->get_field_label('article_txt_delai_livraison_stock'); ?></label></td>
						<td class="value_field"><?= $this->get_field_html('article_txt_delai_livraison_stock'); ?></td>
						<td></td>
						<td class="label_field"><label for="article_txt_delai_livraison_rupture"><?= $this->get_field_label('article_txt_delai_livraison_rupture'); ?></label></td>
						<td class="value_field"><?= $this->get_field_html('article_txt_delai_livraison_rupture'); ?></td>
					</tr>
					<tr>
						<td class="label_field"><label for="article_degressif"><?= $this->get_field_label('article_degressif'); ?></label></td>
						<td class="value_field" coslpan="3"><?= $this->get_field_html('article_degressif'); ?></td>
					</tr>
					<tr id="row_degressif" <?php if(! $article->isDegressif() ) echo 'style="display:none"'; ?>>
						<td></td>
						<td class="value_field" coslpan="3">
							<table id="tab_degressifs" class="tableau dont_fill">
								<tr class="filter_fields">
									<th class="w50p">
										<?= $form->text_field(array(
											'name'		=> 'degress_qty',
											'classes'		=> 'temp_message',
											'value'		=> dims_constant::getVal('SHORT_QUANTITY')
										));
										?>
									</th>
									<th class="w100p">
										<?= $form->text_field(array(
											'name'		=> 'degress_price',
											'classes'		=> 'temp_message',
											'value'		=> dims_constant::getVal('PU_HT')
										));
										?>
									</th>
									<th class="label_left w20p">
										<a href="javascript:void(0);" onclick="javascript:addDegressifPrice();" title="<?= dims_constant::getVal('ADD_DEGRESS_PRICE');?>">
											<img src="<?=  $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
										</a>
									</th>
								</tr>
								<tr id="degress_header">
									<td class="title_tableau">
										<?= dims_constant::getVal('QUANTITY'); ?>
									</td>
									<td class="title_tableau">
										<?= dims_constant::getVal('PU_HT'); ?>
									</td>
									<td class="title_tableau">
										<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
									</td>
								</tr>
								<?php
								if( ! $article->isNew() ){
									$degressifs = $view->get('degressifs');
									if( ! empty($degressifs) ){
										foreach($degressifs as $qty => $value){
											$qty = intval($qty);
											?>
											<tr id="degress_<?= $qty; ?>" class="degress_tr">
												<td><?= $qty; ?></td>
												<td class="value_right"><?= $value; ?></td>
												<td><a href="javascript:void(0);" onclick="javascript:editDegressif(<?= $qty; ?>, <?= $value; ?>);" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>" /></a><a href="javascript:void(0);" onclick="javascript:if(confirm('<?= addslashes(dims_constant::getVal('SURE_DELETE_DEGRESSIF')); ?>')) {removeDegressif(<?= $qty; ?>);}" title="<?= dims_constant::getVal('_DELETE'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a></td>
											</tr>
											<?php
										}
									}
								}
								?>
							</table>
							<div id="degress_error"></div>
							<div id="degress_values" style="display:none">
								<?php
								if( ! $article->isNew() ){
									$degressifs = $view->get('degressifs');
									if( ! empty($degressifs) ){
										foreach($degressifs as $qty => $value){
											$qty = intval($qty);
											?>
											<input class="ipth_degressifs" id="ipth_<?= $qty; ?>" type="hidden" name="degressifs[<?= $qty; ?>]" value="<?= $value; ?>" />
											<?php
										}
									}
								}
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#article_degressif').change(function(){
		if($(this).is(':checked')){
			$('#row_degressif').show();
		}
		else{
			$('#degress_values').html('');
			$('#row_degressif').hide();
		}
	});

	$('#degress_qty').focus(function(){
		if($(this).val() == '<?= addslashes(dims_constant::getVal('SHORT_QUANTITY')); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= addslashes(dims_constant::getVal('SHORT_QUANTITY')); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
	    if ( event.which == 13 ) {
	        event.preventDefault();
	        addDegressifPrice();
	    }
	});

	$('#degress_price').focus(function(){
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
	        addDegressifPrice();
	    }
	});

	function editDegressif(qty, price){
		$("#degress_qty").val(qty).removeClass('temp_message');
		$("#degress_price").val(price).removeClass('temp_message').focus();
	}

	function removeDegressif(qty){
		$("#degress_"+qty).remove();
		$("#ipth_"+qty).remove();
	}

	function addDegressifPrice(){
		var qty = $("#degress_qty").val();
		var price = $("#degress_price").val();
		var floating_value = /^[-]?\d*[\.,]?\d*$/ ;
		var integer = /^\d*$/;

		if(qty != 'undefined' && price != 'undefined' && qty != '' && price != '' && qty != '<?= addslashes(dims_constant::getVal('SHORT_QUANTITY')); ?>' && price != '<?= addslashes(dims_constant::getVal('PU_HT')); ?>'){
			if(qty.match(integer) && price.match(floating_value)){
				qty = parseInt(qty);
				price = price.replace(',', '.');
				$('#degress_'+qty).remove();//On supprime la ligne sur la quantité si elle existe déjà
				var lst = new Array();
				var keys = new Array();
				//Récupération des éléments existants dans la table
				var i = 0;
				$('tr.degress_tr').each(function(){
					lst[parseInt($('td:nth-child(1)', this).text())] = $('td:nth-child(2)', this).text();
					keys[i] = parseInt($('td:nth-child(1)', this).text());
					i++;
				});

				if(keys.length < 12){
					//On ajoute le nouvel élément à la liste
					lst[qty] = price;
					keys[i] = qty;

					$('tr.degress_tr').remove();
					$('input.ipth_degressifs').remove();

					//tri à bulle en javascript sur les clefs
					if(keys.length > 1){
						for(var i=keys.length - 1; i>0 ; i--){
							for(var j=0; j < i; j++){
								if(parseInt(keys[j]) > parseInt(keys[i])){
									var temp = keys[j];
									keys[j] = keys[i];
									keys[i] = temp;
								}
							}
						}
					}

					//Ensuite on reconstruit le tableau de résultats
					for(var i=0; i<keys.length; i++){
						$('#tab_degressifs').append('<tr id="degress_'+keys[i]+'" class="degress_tr"><td>'+keys[i]+'</td><td class="value_right">'+lst[keys[i]]+'</td><td><a href="javascript:void(0);" onclick="javascript:editDegressif('+keys[i]+', '+lst[keys[i]]+');" title="<?= addslashes(dims_constant::getVal('_DIMS_LABEL_EDIT'));?>"><img src="<?= $view->getTemplateWebPath('gfx/edit16.png'); ?>" /></a><a href="javascript:void(0);" onclick="javascript:if(confirm(\'<?= addslashes(dims_constant::getVal('SURE_DELETE_DEGRESSIF')); ?>\')) {removeDegressif('+keys[i]+');}" title="<?= addslashes(dims_constant::getVal('_DELETE'));?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a></td></tr>');
						$('#degress_values').append('<input class="ipth_degressifs" id="ipth_'+keys[i]+'" type="hidden" name="degressifs['+keys[i]+']" value="'+lst[keys[i]]+'" />');
					}

					$('#degress_'+qty).effect("highlight", {color:'#FC8617'}, 1500);
					$("#degress_qty").val('<?= addslashes(dims_constant::getVal('SHORT_QUANTITY')); ?>');
					$("#degress_price").val('<?= addslashes(dims_constant::getVal('PU_HT')); ?>');
					$("#degress_price").addClass('temp_message');
					$("#degress_qty").focus();
				}
				else{
					$('#degress_error').text('<?= dims_constant::getVal('MAX_DISCOUNT_QTY_REACHED'); ?>');
				}
			}
		}
	}
</script>
