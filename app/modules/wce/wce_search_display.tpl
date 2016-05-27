{if isset($wceresult)}
<h2>R&eacute;sultat de la recherche {if isset($page.SEARCH)} {$page.SEARCH} {/if}</h2>
    <ul>
        {foreach from=$wceresult item=result}
            <li>
                <a class="wcepage" style="clear:both;" href="/{$result.LINK}" >
                    <h2 class="soustitre">Page {$result.TITLE}</h2>
                    <p class="texte">{$result.CONTENT}</p>
                 </a>
                {if !empty($result.CORRESP_DOC)}
                    <div style="float:left;width:100%;text-align: right">
                        Documents attach&eacute;s :
                    {foreach from=$result.CORRESP_DOC item=resdoc}
                    <a href="{$resdoc.link}">{$resdoc.filetype} {$resdoc.label}</a>
                    {/foreach}
                    </div>
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}
