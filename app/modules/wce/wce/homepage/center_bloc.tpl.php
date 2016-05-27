<div class="content_droite">
	<div class="zone_graphe">
		<?
		require_once module_wce::getTemplatePath("homepage/display_stats_consultations.tpl.php");
		?>
	</div>
	<div class="zone_art_consultation">
		<div class="cadre cadre_article_droite cadre_fixed_height" id="statistiques">
			<h2><? echo $_SESSION['cste']['_STATS_5_MOST_POPULAR_ARTICLES']; ?></h2>
			<span class="sous_titre"><? echo $_SESSION['cste']['_LAST_30_DAYS']; ?></span>
			<div class="graph">
				<table cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<?
							$lstBestArticles = module_wce::bestArticles();
							foreach($lstBestArticles as $article){
								?>
								<td>
									<div class="barre_graph">
										<? echo $article->getLightAttribute('meter'); ?>
									</div>
								</td>
								<?
							}
							?>
						</tr>
						<tr>
							<?
							foreach($lstBestArticles as $article){
								?>
								<td>
									<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_DEF."&articleid=".$article->fields['id']."&headingid=".$article->fields['id_heading']; ?>" class="lien_bleu">
										<? echo $article->fields['title']; ?>
									</a>
								</td>
								<?
							}
							?>
						</tr>
					</tbody>
				</table>
			</div>
			<!--<a href="#" class="lien_bas">Voir toutes les statistiques</a>-->
		</div>
		<script type="text/javascript">
			function resizeStatsAccueil(){
				var maxHeight = $('div#statistiques').height()-($('div#statistiques h2').outerHeight(true) + $('div#statistiques span.sous_titre').innerHeight() + $('div#statistiques div.graph tr:last td:first').innerHeight() + 25);
				$('div.graph div.barre_graph:first').css('height', maxHeight+'px');
				var max = $('div.graph div.barre_graph:first').text();
				$('div.graph div.barre_graph:not(:first)').each(function(){
					var val = $(this).text();
					var height = maxHeight*val/max;
					$(this).css({'height': height+'px', "margin-top": (maxHeight-height)+"px"});
				});
			}
			$(document).ready(function(){
				resizeStatsAccueil();
				$(window).resize(function(){resizeStatsAccueil();});
			});
		</script>
	</div>
</div>
<div class="lien_stats">
	<a href="<? echo module_wce::get_url(module_wce::_SUB_STATS); ?>">
		<? echo $_SESSION['cste']['_SEE_ALL_STATS']; ?>
	</a>
</div>
