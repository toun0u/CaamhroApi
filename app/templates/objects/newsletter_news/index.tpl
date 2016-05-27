<table cellpadding="0" cellspacing="0" style="width:100%;">
    {foreach from=$object_articles key=k item=article}
        <tr>
            <td style="margin-left: 10px; font-weight:bold; color:#424242; font-size: 17px;" colspan="2"><br/>
                {$article.title}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 12px;"><br/>
                Le <font style="color: #424242;">{$article.day}</font> {$article.month}. <font style="color: #424242;">{$article.year}</font> à <font style="color: #424242;">{$article.hour}:{$article.minute}</font></a>
            </td>
        </tr>
        <tr>
        {if $article.path!=''}
            <td style="width:15%;padding-right: 20px;"><br/>
                <img src="{$article.path}" alt="{$article.title}" title="{$article.title}" />
            </td>
            <td style="width:85%; vertical-align: top; font-size: 12px; color: #424242;">
                {$article.description}
                    {if $article.link!=''}
                    <a style="color:#424242" href="{$article.link}">{if $article.link_mode=="interne"}Lire la suite ...{else}Consulter la source...{/if}</a>
            </td>
        {/if}
        {else}
            <td colspan="2" style="font-size: 12px; color: #424242;"><br/>
                {$article.description}
                    {if $article.link!=''}
                        <br><a style="color:#424242" href="{$article.link}">{if $article.link_mode=="interne"}Lire la suite ...{else}Consulter la source...{/if}</a>
                    {/if}
            </td>
        {/if}
        <tr>
            <td style="text-align: right; border-bottom: 1px solid #424242; font-size: 12px;" colspan="2" >
                <font style="color: #BFBFBF;">Source :</font> <font style="color: #424242; font-weight: bold;">URPS</font>
            </td>
        </tr>
    {/foreach}
</table>
