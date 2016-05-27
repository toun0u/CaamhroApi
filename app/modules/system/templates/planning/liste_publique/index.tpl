<link href="modules/system/templates/planning/liste_publique/styles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/j/functions.js"></script>
    <div class="contener_reunions">
        <div class="title_reunions" style="margin-top:10px;">
            <img src="modules/system/templates/planning/liste_publique/gfx/calendrier2.png" border="0">
            <a>
                Réunion à venir
            </a>
        </div>
        {foreach from=$liste_reunions key=k item="reunion"}
            <div class="reunion">
                <div class="libelle">
                    <span>{$reunion.label}</span>
                </div>
                <table>
                    <tr>
                        <td class="date">
                            Le <font style="color: #009EE1;">{$reunion.date_deb.jour}</font> / {$reunion.date_deb.mois} / <font style="color: #009EE1;">{$reunion.date_deb.annee}</font> au <font style="color: #009EE1;">{$reunion.date_fin.jour}</font> / {$reunion.date_fin.mois} / <font style="color: #009EE1;">{$reunion.date_fin.annee}</font> de <font style="color: #009EE1;">{$reunion.date_deb.heure}:{$reunion.date_deb.min}</font> à <font style="color: #009EE1;">{$reunion.date_fin.heure}:{$reunion.date_fin.min}</font> à <b>{$reunion.lieu}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="teaser">
                            <span class="label_reunion">Résumé : </span>
                            <p>{$reunion.teaser}<br/><a href="javascript:void(0);" onclick="javascript:dims_switchdisplay('descriptif_{$k}');"><img src="modules/system/templates/planning/liste_publique/gfx/filtrer.png" border="0" align="left">En savoir plus</a></p>
                        </td>
                    </tr>
                </table>
                <div id="descriptif_{$k}" style="display: none;">
                    <div class="content_actu">
                        <span class="label_reunion">Descriptif : </span>
                        <p>{$reunion.description}</p>
                    </div>
                    {if $reunion.docs|@count > 0}
                        <div class="content_docs">
                           <span class="label_reunion">Documents disponibles : </span>
                           <table>
                               {foreach from=$reunion.docs key=k item="doc"}
                               <tr>
                                   <td>
                                       {if $doc.type=='png' || $doc.type=='jpg' || $doc.type=='jpeg' || $doc.type=='bmp' || $doc.type=='gif'}
                                           {assign var='type' value="pict.png"}
                                       {elseif $doc.type=='pdf'}
                                           {assign var='type' value="pdf.png"}
                                       {else}
                                           {assign var='type' value="default.png"}
                                       {/if}
                                       <img src="modules/system/templates/planning/liste_publique/gfx/{$type}"/>
                                   </td>
                                   <td><a href="{$doc.path}">{$doc.nom}</a></td>
                               </tr>
                               {/foreach}
                           </table>
                       </div>
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>
</div>
