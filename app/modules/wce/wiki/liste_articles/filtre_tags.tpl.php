<?php
if(!empty($_SESSION['wiki']['lst_article']['filters']['tags'])){
	$display = 'block';
}
else $display = 'none';
?>
<div class="title_h3">
	<a onclick="javascript:toggleTags();" class="lien_bas lk_tags" href="javascript:void(0);">
		<? if( $display == 'none') echo $_SESSION['cste']['_SHOW_TAGS']; else echo $_SESSION['cste']['_MASK_TAGS']; ?>
	</a>
	<h3><? echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></h3>
</div>
<div class="zone_filtre bloc_tags" style="display:<?= $display;?>;">
	<p><label for="teg_search"><?= $_SESSION['cste']['SEARCH_A_TAG']; ?></label><input type="text" name="tag_search" id="tag_search" /></p>

	<ul id="tags" class="filtres_tags">
		<?php
		if(!empty($_SESSION['wiki']['lst_article']['filters']['tags'])){
			foreach($_SESSION['wiki']['lst_article']['filters']['tags'] as $id => $tag){
				?>
				<li><?= $tag; ?><a class="close">x</a><input type="hidden" style="display:none;" value="<?= $id; ?>" name="tags[]"></li>
				<?php
			}
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	function toggleTags(){
		$('div.bloc_tags').fadeToggle('fast',function(){
			if ($(this).is(':visible'))
				$('a.lk_tags').html('<? echo $_SESSION['cste']['_MASK_TAGS']; ?>');
			else
				$('a.lk_tags').html('<? echo $_SESSION['cste']['_SHOW_TAGS']; ?>');
		});
	}

	$.ajax({
			type: "POST",
			url: "admin.php",
			data: {
				'dims_op' : 'wiki',
				'op_wiki' : 'get_tags',
				'mode'	  : 'complexe'
			},
			dataType: "json",
			async: false,
			success: function(data){
				$('#tag_search').autocomplete({
					source: data.availableTags,
					select: function(event,ui){
						var el = "";
						el  = "<li>\n";
						el += ui.item.value + "\n";
						el += "<a class=\"close\">x</a>\n";
						el += "<input type=\"hidden\" style=\"display:none;\" value=\""+data.ids[ui.item.value]+"\" name=\"tags[]\">\n";
						el += "</li>\n";
						$('#tags').append(el);
						$('#tag_search').val("");
						document.form_filter_articles.submit();
						//return false;//sans ça ça ne vide pas le champ de recherche
					}
				});

				$('#tags').click(function(e){
					if (e.target.tagName == 'A') {
						// Removes a tag when the little 'x' is clicked.
						// Event is binded to the UL, otherwise a new tag (LI > A) wouldn't have this event attached to it.
						$(e.target).parent().remove();
						document.form_filter_articles.submit();
					}
					else {
						// Sets the focus() to the input field, if the user clicks anywhere inside the UL.
						// This is needed because the input field needs to be of a small size.
						tag_input.focus();
					}
				});
			},
			error: function(data){
			}
		});

</script>