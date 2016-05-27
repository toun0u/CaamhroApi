<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
?>
<div style="clear: both;">
    <div class="new_info_article">
        <table>
            <tr>
                <td>
                    <img src="<?= $view->getTemplateWebPath('gfx/info32.png'); ?>" title="Info" alt="info"/>
                </td>
                <td>
                    <?= str_replace('{URL}',get_path('params', 'champs'),dims_constant::getVal('_INFOS_FAMILLES_VIGNETTES')); ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="sub_actions" style="padding-top: 10px;">
        <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=show&sa=newVign&id=".$elem->fields['id']; ?>">
            <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('_ADD_THUMBNAIL_FAM'); ?>" alt="<?= dims_constant::getVal('_ADD_THUMBNAIL_FAM'); ?>" />
            <span><?= dims_constant::getVal('_ADD_THUMBNAIL_FAM'); ?></span>
        </a>
    </div>
    <div class="thumbnails_fam">
        <?php
        $lstThumb = $view->get('thumbnails');
        $nbThumb = count($lstThumb);
        foreach($lstThumb as $thumb){
            $doc = $thumb->getDocfile();
            ?>
            <div class="thumbnail">
                <div class="actions_thumb">
                    <!--<a href="">
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" />
                    </a>-->
                    <a onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv()."?c=familles&a=delVign&id=".$elem->fields['id']."&doc=".$thumb->fields['id_doc']; ?>','<?= dims_constant::getVal('_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE'); ?>');" href="javascript:void(0);">
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/poubelle16.png"); ?>" />
                    </a>
                    <?
                    if($thumb->fields['position'] == 1){
                        ?>
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/gauche16_ns.png"); ?>" />
                        <?
                    }else{
                        ?>
                        <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=downVign&id=".$elem->fields['id']."&doc=".$thumb->fields['id_doc']; ?>">
                            <img src="<?php echo $this->getTemplateWebPath("/gfx/gauche16_s.png"); ?>" />
                        </a>
                        <?
                    }
                    if($thumb->fields['position'] == $nbThumb){
                        ?>
                        <img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_ns.png"); ?>" />
                        <?
                    }else{
                        ?>
                        <a href="<?= dims::getInstance()->getScriptEnv()."?c=familles&a=upVign&id=".$elem->fields['id']."&doc=".$thumb->fields['id_doc']; ?>">
                            <img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_s.png"); ?>" />
                        </a>
                        <?
                    }
                    ?>
                </div>
                <div class="thumb">
                    <img src="<?= $doc->getThumbnail(150); ?>" />
                </div>
                <div class="ref_thumb">
                    <img src="<?=$this->getTemplateWebPath("/gfx/pastille_verte16.png"); ?>" />
                    <span>
                        <?php
                        if(is_null($art = $thumb->getArticle())){
                            echo $doc->fields['name'];
                        }else{
                            echo 'R&eacute;f. <a href="'.dims::getInstance()->getScriptEnv()."?c=articles&a=show&id=".$art->get('id').'">'.$art->fields['reference'].'</a>';
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
