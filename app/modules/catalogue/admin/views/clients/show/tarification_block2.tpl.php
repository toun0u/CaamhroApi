<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?><h3><?= $title; ?></h3><?php
	}
	?>
	<div id="block_composition" class="sub_bloc_form">
		<div id="add_fields">
			<?= $this->get_field_html('ref'); ?>
			<?= $this->get_field_html('ref_article'); ?>
			<div id="ac_references" class="ac_container" style="display:none;">
				<ul id="ul_ac_references">
				</ul>
			</div>
			<?= $this->get_field_html('prix_net'); ?>
			<?= dims_constant::getVal('_DIMS_OR'); ?>
			<?= $this->get_field_html('deduction'); ?>
			<a href="javascript:void(0);" onclick="javascript:addComponent();" title="<?= dims_constant::getVal('ADD_COMPONENT');?>">
				<img src="<?=  view::getInstance()->getTemplateWebPath('gfx/ajouter16.png'); ?>" />
			</a>
		</div>
		<table class="tableau" id="table_kit" style="left:0px;">
			<tr id="kit_headings">
				<td class="w10 title_tableau">
					&nbsp
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('REF'); ?>
				</td>
				<td class="w25 title_tableau">
					<?= dims_constant::getVal('DESIGNATION'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('NET_PRICE_HT'); ?>
				</td>
				<td class="w10 title_tableau">
					<?= dims_constant::getVal('SOIT_REMISE'); ?> (&#37;)
				</td>
				<td class="w100p title_tableau">
					<?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
				</td>
			</tr>
			<?php
			$already = "";
			foreach(view::getInstance()->get('lst_prix') as $prix){
				if(!is_null($art = $prix->getArticle())){
					?>
					<tr id="kit_row_<?= $art->get('id'); ?>">
						<td>
							<?php
							if(!is_null($phot = $art->getWebPhoto(20))){
								?>
								<img src="<?= $phot; ?>" />
								<?php
							}
							?>
						</td>
						<td>
							<?= $art->fields['reference']; ?>
						</td>
						<td>
							<?= $art->getLabel(); ?>
						</td>
						<td class="value_right">
							<?= number_format($prix->fields['puht'],2,'.', ''); ?>
						</td>
						<td class="value_right">
							<?= number_format((($art->fields['putarif_0']-$prix->fields['puht'])*100)/$art->fields['putarif_0'],2); ?>
						</td>
						<td class="kit_actions">
							<a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:removeComponent(<?= $art->get('id'); ?>);" title="<?= dims_constant::getVal('ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>">
								<img src="<?= view::getInstance()->getTemplateWebPath('gfx/supprimer16.png'); ?>" />
							</a>
							<a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:editComponent(<?= $art->get('id'); ?>);" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>">
								<img src="<?= view::getInstance()->getTemplateWebPath('gfx/edit16.png'); ?>" />
							</a>
						</td>
					</tr>
					<?php
					$already .= '<input type="hidden" id="kit_hf_'.$art->get('id').'" name="kit_composition['.$art->get('id').']" value="'.number_format($prix->fields['puht'],2,'.', '').'"/>';
				}
			}
			?>
		</table>
		<div id="kit_compo" style="display:none;"><?= $already; ?></div>
	</div>
</div>
<script type="text/javascript">
	function addComponent(){
		var ref = $("#ref_article").val();
		var prix_ht = $("#prix_net").val();
		var deduction = $("#deduction").val();
		var num = /^\d+\.?\d*$/;
		var tpl_noimg = '<tr id="kit_row_${id}"><td></td><td>${ref}</td><td>${label}</td><td class="value_right">${remise_ht}</td><td class="value_right">${remise_pourc}</td><td class="kit_actions"></td></tr>';
		var tpl_img = '<tr id="kit_row_${id}"><td><img src="${photo_path}" /></td><td>${ref}</td><td>${label}</td><td class="value_right">${remise_ht}</td><td class="value_right">${remise_pourc}</td><td class="kit_actions"></td></tr>';

		if(ref != 'undefined' && ref != '' && ref != '<?= addslashes(dims_constant::getVal('_REF_ARTICLE')); ?>' &&
			((prix_ht != 'undefined' && prix_ht != '' && prix_ht != '<?= addslashes(dims_constant::getVal('NET_PRICE_HT')); ?>' && prix_ht.match(num)) ||
			(deduction != 'undefined' && deduction != '' && deduction != '<?= addslashes(dims_constant::getVal('_DEDUCTION_POURC')); ?>' && deduction.match(num)))){
			//On supprime éventuellement la ligne qui existerait déjà
			$('#kit_row_'+ref).remove();
			$('#kit_hf_'+ref).remove();
			//Toutes les conditions sont remplies pour pouvoir ajouter la ligne
			//Récupération des infos de l'article

			$.ajax({
				type: "GET",
				url: 'admin.php',
				async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
				data : {
					'c'     : 'articles',
					'a'     : 'js_article_info',
					'id'    : ref,
					'qty'   : 1,
					'remise_ht'  : prix_ht,
					'remise_pourc' : deduction
				},
				dataType: "json",
				success: function(data){
					var tpl_used = tpl_noimg;
					if(data.photo_path != null && data.photo_path != 'undefined'){
						tpl_used = tpl_img;
					}
					var row = $.tmpl(tpl_used, data);
					$('#kit_headings').after(row);
					$('#kit_row_'+ref+' td.kit_actions').append('<a href="javascript:void(0);" onclick="javascript:removeComponent('+ref+');" title="<?= dims_constant::getVal('ARE_YOU_SURE_YOU_TO_WANT_TO_DELETE_THIS_ELEMENT_?'); ?>"><img src="<?= view::getInstance()->getTemplateWebPath('gfx/supprimer16.png'); ?>" /></a>');
					$('#kit_row_'+ref+' td.kit_actions').append('<a href="javascript:void(0);" onclick="javascript:editComponent('+ref+');" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>"><img src="<?= view::getInstance()->getTemplateWebPath('gfx/edit16.png'); ?>" /></a>');
					row.effect("highlight", {color:'#FC8617'}, 1500);
					$('#kit_compo').append('<input type="hidden" id="kit_hf_'+ref+'" name="kit_composition['+ref+']" value="'+data.remise_ht+'"/>');

					//On vide les champs
					$("#prix_net").val('<?= dims_constant::getVal('NET_PRICE_HT'); ?>').addClass('temp_message');
					$("#deduction").val('<?= dims_constant::getVal('_DEDUCTION_POURC'); ?>').addClass('temp_message');
					$('#ref').val('<?= addslashes(dims_constant::getVal('_REF_ARTICLE')); ?>').addClass('temp_message').focus();
					$('#ref_article').val('');
				}
			});
		}
	}
	function removeComponent(id){
		 $('#kit_row_'+id).remove();
		 $('#kit_hf_'+id).remove();
	}
	function editComponent(id){
		$('#ref_article').val(id);
		$('#ref').val(jQuery.trim($('#kit_row_'+id+" td:eq(2)").html())).removeClass('temp_message');
		$('#prix_net').val(jQuery.trim($('#kit_row_'+id+" td:eq(3)").html())).removeClass('temp_message');
		$('#deduction').val(jQuery.trim($('#kit_row_'+id+" td:eq(4)").html())).removeClass('temp_message');
	}
	function selectedArticle(){
		var refVal = $("#ref_article").val();
		if(refVal != ''){
			$('#prix_net').val('').removeClass('temp_message');
			$('#deduction').val('').removeClass('temp_message');
			$.ajax({
				type: "GET",
				url: 'admin.php',
				async: false,//obligé pour Safari de jouer en synchrone, sinon ça passe pas.
				data : {
					'c'     : 'articles',
					'a'     : 'js_article_info',
					'id'    : refVal,
					'qty'   : 1
				},
				dataType: "json",
				success: function(data){
					$('#prix_net').val(data.puht);
				}
			});
		}else{
			$('#prix_net').val('<?= addslashes(dims_constant::getVal('NET_PRICE_HT')); ?>').addClass('temp_message');
			$('#deduction').val('<?= addslashes(dims_constant::getVal('_DEDUCTION_POURC')); ?>').addClass('temp_message');
		}
	}
	$('#ref').focus(function(){
		if($(this).val() == '<?= dims_constant::getVal('_REF_ARTICLE'); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
			$(this).val('<?= dims_constant::getVal('_REF_ARTICLE'); ?>');
			$(this).addClass('temp_message');
		}
	})
	.dims_autocomplete( { c : 'articles', a : 'ac_articles' }, 3, 500, '#ref_article', '#ac_references', '#ul_ac_references', '<li>${reference} - ${label}</li>', '<?php echo addslashes(dims_constant::getVal('NO_REFERENCE')); ?>', selectedArticle );
	$('#prix_net').focus(function(){
		if($(this).val() == '<?= dims_constant::getVal('NET_PRICE_HT'); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
			$(this).val('<?= dims_constant::getVal('NET_PRICE_HT'); ?>');
			$(this).addClass('temp_message');
		}
	})
	.keypress(function(event){
		if ( event.which == 13 ) {
			event.preventDefault();
			addComponent();
		}
	});;
	$('#deduction').focus(function(){
		if($(this).val() == '<?= dims_constant::getVal('_DEDUCTION_POURC'); ?>'){
			$(this).val('');
			$(this).removeClass('temp_message');
		}
	})
	.focusout(function(){
		if($(this).val() == ''){
			$(this).val('<?= dims_constant::getVal('_DEDUCTION_POURC'); ?>');
			$(this).addClass('temp_message');
		}
	})
	.keypress(function(event){
		if ( event.which == 13 ) {
			event.preventDefault();
			addComponent();
		}
	});;
</script>
