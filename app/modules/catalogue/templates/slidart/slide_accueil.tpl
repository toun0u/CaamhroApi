<div class="slide_articles">
	<div style="float: left; margin-top: 10px; width: 100px; height: 100px; text-align: center;">
		<a href="index.php?op=fiche_article&ref={$article.reference}">
			<img src="/photos/100x100/{$article.image}"/>
		</a>
	</div>
	<div style="float: right; width: 60%; margin-top: 10px;">
		<a href="index.php?op=fiche_article&ref={$article.reference}">{$article.label|truncate:40:"..."}</a><br />
		{if $aff_prix && ($cata_mode_B2C || isset($switch_user_logged_in))}
			{if ($article.degressif)}
				<a onclick="javascript:return void(0);" onmouseover="javascript:get_degressif('{$article.reference}');" href="#"><img border="0" src="/modules/catalogue/img/degressif.gif" class="degressif_img" /></a>
			{/if}
		{/if}
	</div>
	{if $aff_prix && ($cata_mode_B2C || isset($switch_user_logged_in))}
	<div class="tarif_articles">
		<p style="color:#F7931E">{$article.prix} € {if $cata_base_ttc}TTC{else}HT{/if}</p>
	</div>
	<div class="promo_article">
		{if isset($article.promotion)}
		<div class="promo_art_ht">
			<p>{$article.prix_brut} € {if $cata_base_ttc}TTC{else}HT{/if}</p>
		</div>
		{/if}
	</div>
	{/if}

	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td style="color:#F7931E;">
				Ref : {$article.reference}
			</td>
			{if $cata_mode_B2C || isset($switch_user_logged_in)}
			<td style="text-align:right;">
				<input type="text" maxlength="4" size="2" value="1" id="qte_{$article.reference}" name="qte_{$article.reference}" style="text-align: right; padding: 0pt; margin: 0pt; width: 36px;" class="webinput">&nbsp;
			</td>
			<td style="text-align:left;">
				<a href="javascript:void(0);" onclick="javascript:dims_getelem('qte_{$article.reference}').value++"><img border="0" width="12" height="12" alt="Ajouter" src="/modules/catalogue/img/caddy_plus.png"></a><br>
				<a href="javascript:void(0);" onclick="javascript:if (dims_getelem('qte_{$article.reference}').value > 1) dims_getelem('qte_{$article.reference}').value--"><img border="0" width="12" height="12" alt="Retirer" src="/modules/catalogue/img/caddy_moins.png"></a>
			</td>
			<td>
				<a class="ajout_panier_btn" href="javascript:void(0)" onclick="javascript:dims_xmlhttprequest_todiv('/index.php', 'op=ajouter_panierart&pref={$article.reference}&qte='+dims_getelem('qte_{$article.reference}').value, '|', 'nbArtPanier', 'divpanier', 'refArticlePanier');">
					<img src="/modules/catalogue/img/panier_promo.png" style="width:32px; height: 32px;"/>
				</a>
			</td>
			{/if}
        </tr>
        <tr>

        </tr>
    </table>
</div>
