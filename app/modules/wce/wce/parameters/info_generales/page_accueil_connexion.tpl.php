<div class="title_h3">
    <h3>Pages d'accueil après connexion</h3>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;margin-bottom: 30px;">
    <tr>
        <td class="title_table_accueil">
            <? echo $_SESSION['cste']['_DIMS_LABEL_DOMAIN']; ?>
        </td>
        <td class="title_table_accueil">
            <? echo $_SESSION['cste']['_ARTICLE']; ?>
        </td>
        <td class="title_table">
            <? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
        </td>
    </tr>
	<?
	$trl = 'class="table_ligne1"';
	foreach($domaines as $r){
		?>
		<tr <? echo $trl; ?>>
			<td>
				<? echo $r['domain']; ?>
			</td>
			<td>
				<?
				if ($r['id_post_connexion_wce_article'] != '' && $r['id_post_connexion_wce_article'] > 0){
					$art = new wce_article();
					$art->open($r['id_post_connexion_wce_article']);
					?>
					<a href="javascript:void(0);">
						<? echo $art->fields['title']; ?>
					</a>
					<?
				}else{
					echo $_SESSION['cste']['_DIMS_LABEL_UNDEFINED'];
				}
				?>
			</td>
			<td class="actions">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_EDIT_ACCUEIL2."&id=".$r['id']; ?>">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_modif.png'); ?>" />
				</a>
			</td>
		</tr>
		<?
		$trl = ($trl == '')?'class="table_ligne1"':'';
	}
	?>
</table>