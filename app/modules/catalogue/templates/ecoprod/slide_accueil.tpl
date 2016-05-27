<div class="slide_articles">
    <table>
        <tr>
            <td class="image" colspan="2">
                <a href="index.php?op=fiche_article&ref={$article.ref}">
                    <img src="/photos/50x50/{$article.photo}" center top/>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="index.php?op=fiche_article&ref={$article.ref}">{$article.designation}</a><br />
                {if $aff_prix}
					{if ($article.qte1 != '' && $article.qte1 != 0 && $article.remise1 != '' && $article.remise1 != 0)}
						<a onclick="javascript:return void(0);" onmouseover="javascript:get_degressif('{$article.ref}');" href="#"><img border="0" src="/modules/catalogue/img/degressif.gif" class="degressif_img" /></a>
					{/if}
                {/if}
            </td>
        </tr>
        <tr>
            <td>
            	{if $aff_prix}
                <div class="tarif_articles">
                    <p>{$article.prix} € HT</p>
                </div>
                <div class="promo_article">
                    {if isset($article.promo)}
                    <div class="promo_art_ht">
                        <img src="/modules/catalogue/img/puce_promos.png"/>
                        <p>{$article.prix_brut} € HT</p>
                    </div>
                    {/if}
                </div>
                {/if}
            </td>
            <td>
                {if $article.dev_durable == 1}
                    <p class="eco_prod">
                        <img src="/modules/catalogue/img/icon_eco.png" alt="Eco produit" title="Eco produit" />
                    </p>
                {/if}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <a href="index.php?op=fiche_article&ref={$article.ref}">
                    <div class="stock_tarif">
                        <div class="stock_article">
                            {if $article.stock > 0}
                                <img src="/modules/catalogue/img/puce_dispo.png"/>
                                <p>En stock</p>
                            {else}
                                <img src="/modules/catalogue/img/puce_nondispo.png"/>
                                <p>Non dispo.</p>
                            {/if}
                        </div>
                    </div>
                </a>
            </td>
        </tr>
    </table>
</div>
