<?php
if(!isset($_SESSION['wiki']['lst_article']['categ'])) $_SESSION['wiki']['lst_article']['categ'] = 0;
$id_categ = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['wiki']['lst_article']['categ'],$_SESSION['wiki']['lst_article']['categ']);
$lstCateg = module_wiki::getCategForList($_SESSION['wiki']['lst_article']['categ']);
?>
<div class="sous_cadre_article">
	<div class="title_h3">
		<a onclick="javascript:toggleCateg();" class="lien_bas lk_categ" href="javascript:void(0);">
			<? echo $_SESSION['cste']['_MASK_CATEGORIES']; ?>
		</a><h3><? echo $_SESSION['cste']['_RSS_LABEL_CATEGORY']; ?></h3>
		<?
		if ($_SESSION['wiki']['lst_article']['categ'] == 0){
			?>
			<span class="filtre"><? echo $_SESSION['cste']['_ALLS_FEM']; ?></span>
			<?
		}else{
			$cat = new category();
			$cat->open($_SESSION['wiki']['lst_article']['categ']);
			$parents = explode(';',$cat->fields['parents']);
			foreach($parents as $id_par){
				$par = new category();
				$par->open($id_par);
				if ($par->isRoot()){
				?>
				<a class="filtre" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LST_ARTICLES."&id_categ=0"); ?>">
				<?
					echo $_SESSION['cste']['_ALLS_FEM'];
				}else{
				?>
				<a class="filtre" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LST_ARTICLES."&id_categ=".$par->fields['id']); ?>">
				<?
					echo $par->fields['label'];
				}
				?>
				</a>
				<span class="separator">
					&nbsp;>&nbsp;
				</span>
				<?
			}
			?>
			<span class="filtre"><? echo $cat->fields['label']; ?></span>
			<?
		}
		?>

	</div>
	<div class="aff_letter bloc_category">
		<?
		foreach($lstCateg as $letter => $categs){
			?>
			<div class="letter">
				<span class="letter"><? echo $letter?></span>
				<ul>
					<li>
						<? foreach($categs as $cat){ ?>
							<a class="lien_bleu" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LST_ARTICLES."&id_categ=".$cat->fields['id']); ?>">
								<? echo $cat->fields['label']; ?>
							</a>
						<? } ?>
					</li>
				</ul>
			</div>
			<?
		}
		?>
	</div>
</div>
<script type="text/javascript">
	function toggleCateg(){
		$('div.bloc_category').fadeToggle('fast',function(){
			if ($(this).is(':visible'))
				$('a.lk_categ').html('<? echo $_SESSION['cste']['_MASK_CATEGORIES']; ?>');
			else
				$('a.lk_categ').html('<? echo $_SESSION['cste']['_SHOW_CATEGORIES']; ?>');
		});
	}
</script>