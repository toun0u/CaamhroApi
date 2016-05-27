<?php
if( !empty($_SESSION['dims']['wiki']['historic']) ){
?>
	<p>
		<img src="<?= module_wiki::getTemplateWebPath('/gfx/horloge.png'); ?>" alt="<?= $_SESSION['cste']['HISTORIC']; ?>" title="<?= $_SESSION['cste']['HISTORIC']; ?>"/>
		<select name="histo_articles" id="histo_articles" onchange="javascript:goto_article();">
			<?php
			if($_SESSION['dims']['wiki']['sub'] != module_wiki::_SUB_NEW_ARTICLE) echo '<option></option>';//pour permettre de resélectionner l'article courant si on est sur un de ses sous-onglets
			foreach($_SESSION['dims']['wiki']['historic'] as $art){
				echo '<option value="'.$art['id'].'">'.$art['title'].'</option>';
			}
			?>
		</select>
	</p>
	<script type="text/javascript">
		function goto_article(){
			document.location.href = "<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_ACTION_SHOW_ARTICLE.'&wce_mode=edit&articleid=');?>"+$('#histo_articles').val();
		}
	</script>
<?php
}