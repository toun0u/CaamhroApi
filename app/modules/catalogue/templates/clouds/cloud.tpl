<div class="home-right-box-offer">
    <h2>{$cloud.nom}</h2>
    <div class="content">
        {foreach from=$cloud.elem item=elem}
            <a href="{$elem.lien}" style="color:{$elem.couleur};font-size:{$elem.niveau}%;">{$elem.titre}</a>
        {/foreach}
    </div>
</div>
