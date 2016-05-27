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
		</h3>

		<?php
	}
	?>
	<div id="block_netprices" class="sub_bloc_form">
		<div id="add_fields">
			<?= $form->text_field(array(
				'name'		=> 'search_client',
				'classes'	=> 'temp_message w300p',
				'value'		=> dims_constant::getVal('CLIENT')
			));
			?>
			<?= $form->hidden_field(array(
				'name'		=> 'ref_client'
			));
			?>
			<div id="ac_references" class="ac_container" style="display:none;">
				<ul id="ul_ac_references">
				</ul>
			</div>

			<?= $form->text_field(array(
				'name'		=> 'net_price',
				'classes'	=> 'temp_message',
				'value'		=> dims_constant::getVal('NET_PRICE_HT')
			));
			?>
			<?= dims_constant::getVal('_DIMS_OR'); ?>
			<?= $form->text_field(array(
				'name'		=> 'net_reduction',
				'classes'	=> 'temp_message',
				'value'		=> dims_constant::getVal('_DEDUCTION_POURC')
			));
			?>
			<a href="javascript:void(0);" onclick="javascript:addNetPrice();" title="<?= dims_constant::getVal('ADD_NET_PRICE');?>">
				<img src="<?=  $view->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
			</a>
		</div>
		<table class="tableau" id="table_kit">
			<tr id="net_price_headings">
				<td class="w10 title_tableau">
					&nbsp;
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('REF'); ?>
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('CLIENT'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('NET_PRICE_HT'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('_DEDUCTION_POURC'); ?>
				</td>
				<td class="w5 title_tableau">
					<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
				</td>
			</tr>
			<?php
			$net_prices = $view->get('net_prices');
			$content_for_hidden_fields = '';
			if( ! empty($net_prices) ){
				foreach($net_prices as $code => $pn){
					$content_for_hidden_fields .= '<input type="hidden" id="np_hf_'.$code.'" name="np_elements['.$code.']" value="'.$pn->fields['puht'].'"/>';
					$client = $pn->getLightAttribute('client');
					?>
					<tr id="np_row_<?= $client->getCode();?>">
						<td>
							<?php
							$path = $client->getVignette(20);
							if( ! is_null($path) ){
								?>
								<img src="<?= $path; ?>"/>
								<?php
							}
							?>
						</td>
						<td><?= $client->getCode();?></td>
						<td><?= $client->getName();?></td>
						<td class="value_right"><?= $pn->fields['puht']; ?></td>
						<td class="value_right"><?= round( (1 - ($pn->fields['puht'] / $article->getPUHT()) ) * 100 , 2); ?></td>
						<td class="np_actions">
							<a href="javascript:void(0);" onclick="javascript:if(confirm('<?= addslashes(dims_constant::getVal('SURE_TO_DELETE_NET_PRICE')); ?>')) removeNetPrice('<?= $client->getCode(); ?>');" title="<?= dims_constant::getVal('SURE_TO_DELETE_NET_PRICE'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</table>
		<div id="netprices_list" style="display:none;"><?= $content_for_hidden_fields; ?></div>
	</div>
</div>

<script type="text/javascript">
	$('#search_client').focus(function(){
		if($(this).val() == '<?= dims_constant::getVal('CLIENT'); ?>'){

			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= dims_constant::getVal('CLIENT'); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
	    if ( event.which == 13 ) {
	        event.preventDefault();
	        addNetPrice();
	    }
	})
	.dims_autocomplete( { c : 'clients', a : 'ac_clients' }, 2, 500, '#ref_client', '#ac_references', '#ul_ac_references', '<li>${label}</li>', '<?php echo addslashes(dims_constant::getVal('CATA_NO_CLIENT')); ?>', null );

	$('#net_price').focus(function(){
		if($(this).val() == '<?= addslashes(dims_constant::getVal('NET_PRICE_HT')); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= addslashes(dims_constant::getVal('NET_PRICE_HT')); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
	    if ( event.which == 13 ) {
	        event.preventDefault();
	        addNetPrice();
	    }
	});

	$('#net_reduction').focus(function(){
		if($(this).val() == '<?= addslashes(dims_constant::getVal('_DEDUCTION_POURC')); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
				$(this).val('<?= addslashes(dims_constant::getVal('_DEDUCTION_POURC')); ?>');
				$(this).addClass('temp_message');
			}
	})
	.keypress(function(event){
	    if ( event.which == 13 ) {
	        event.preventDefault();
	        addNetPrice();
	    }
	});

	function removeNetPrice(id_client){
		$('#np_row_'+id_client).remove();
	    $('#np_hf_'+id_client).remove();
	}

	function addNetPrice(){
		var ref 	  		= $("#ref_client").val();
		var net_price 		= $("#net_price").val();
		var net_reduction 	= $("#net_reduction").val();

		var floating_value = /^[-]?\d*[\.,]?\d*$/ ;

		var tpl_noimg = '<tr id="np_row_${ref}"><td></td><td>${ref}</td><td>${label}</td><td class="value_right">${net_price}</td><td class="value_right">${reduction}</td><td class="np_actions"></td></tr>';
		var tpl_img = '<tr id="np_row_${ref}"><td><img src="${photo_path}"/></td><td>${ref}</td><td>${label}</td><td class="value_right">${net_price}</td><td class="value_right">${reduction}</td><td class="np_actions"></td></tr>';

		if(
			(
				( net_price != 'undefined' && net_price != '' && net_price != '<?= addslashes(dims_constant::getVal('NET_PRICE_HT')); ?>' && net_price.match(floating_value) )
				||
				( net_reduction != 'undefined' && net_reduction != '' && net_reduction != '<?= addslashes(dims_constant::getVal('_DEDUCTION_POURC')); ?>' && net_reduction.match(floating_value) )
			)
			&& ref != 'undefined' && ref != '' && ref != '<?= dims_constant::getVal('CLIENT'); ?>' ){

			if(net_price == '<?= addslashes(dims_constant::getVal('NET_PRICE_HT')); ?>') net_price = -1;
			else net_price = net_price.replace(',','.');
			if(net_reduction == '<?= addslashes(dims_constant::getVal('_DEDUCTION_POURC')); ?>') net_reduction = -1;
			else net_reduction = net_reduction.replace(',','.');
			//On supprime éventuellement la ligne qui existerait déjà
	        $('#np_row_'+ref).remove();
	        $('#np_hf_'+ref).remove();

			//Toutes les conditions sont remplies pour pouvoir ajouter la ligne
			//Récupération des infos du client
			$.ajax({
	            type: "GET",
	            url: 'admin.php',
	            async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
	            data : {
        			'c' 			: 'articles',
        			'a' 			: 'js_prix_net_info',
        			'code_client'	: ref,
        			'id_article'	: <?= $article->get('id'); ?>,
        			'net_price'		: net_price,
        			'net_reduction'	: net_reduction,
	            },
	            dataType: "json",
	            success: function(data){
	            	var tpl_used = tpl_noimg;
	            	if(data.photo_path != null && data.photo_path != 'undefined'){
	            		tpl_used = tpl_img;
	            	}
	            	var row = $.tmpl(tpl_used, data);
	            	$('#net_price_headings').after(row);
	            	$('#np_row_'+ref+' td.np_actions').append('<a href="javascript:void(0);" onclick="javascript:if(confirm(\'<?= addslashes(dims_constant::getVal('SURE_TO_DELETE_NET_PRICE')); ?>\')) removeNetPrice(\''+ref+'\');" title="<?= dims_constant::getVal('SURE_TO_DELETE_NET_PRICE'); ?>"><img src="<?= $view->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a>');
	            	row.effect("highlight", {color:'#FC8617'}, 1500);
	            	$('#netprices_list').append('<input type="hidden" id="np_hf_'+ref+'" name="np_elements['+ref+']" value="'+data.net_price+'"/>');

	            	//On vide les champs
	            	$('#net_price').val('<?= dims_constant::getVal('NET_PRICE_HT'); ?>').addClass('temp_message');
	            	$('#net_reduction').val('<?= dims_constant::getVal('_DEDUCTION_POURC'); ?>').addClass('temp_message');
	            	$('#search_client').val('<?= dims_constant::getVal('CLIENT'); ?>').addClass('temp_message').focus();
	            }
	        });
		}
	}
</script>
