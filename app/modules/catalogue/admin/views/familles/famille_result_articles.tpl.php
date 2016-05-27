<?php
$lstArticles = $this->get('lstArticles');
?>
<table class="tableau">
    <tr>
        <td style="width:25px;">
            &nbsp
        </td>
        <td>
            &nbsp
        </td>
        <td>
            &nbsp
        </td>
        <td>
            Réf.
        </td>
        <td>
            <?= dims_constant::getVal('_DESIGNATION'); ?>
        </td>
    </tr>
    <?php
    if(count($lstArticles)){
        foreach($lstArticles as $article){
            ?>
            <tr>
                <td>
                    <input type="checkbox" name="selection[]" value="<?= $article->get('id'); ?>" />
                </td>
                <td style="width:25px;text-align:center;">
                    <?php
                    if($article->fields['published']){
                        ?>
                        <img src="<?= $this->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" title="Cet article est publié" alt="Article publié" />
                        <?php
                    }
                    else{
                        ?>
                        <img src="<?= $this->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" title="Cet article n'est pas publié" alt="Article non publié" />
                        <?php
                    }
                    ?>
                </td>
                <td>
                    <?
                    if(!is_null($path = $article->getWebPhoto(20))){
                        ?>
                        <img src="<?= $path; ?>" />
                        <?
                    }
                    ?>
                </td>
                <td><?= $article->fields['reference']; ?></td>
                <td><?= $article->getLabel(); ?></td>
            <?php
        }
    }else{
        ?>
        <tr>
            <td colspan="6" style="text-align:center;">
                <?= dims_constant::getVal('NO_RESULT'); ?>
            </td>
        </tr>
        <?
    }
    ?>
</table>