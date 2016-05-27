<?
if(isset($this->childrens) && !is_null($this->childrens) && count($this->childrens)){
    $view = view::getInstance();
    $nextChildrens = null;
    ?>
    <div id="col_<?php echo $this->get('id'); ?>" class="browser_column">
        <ul>
            <?
            foreach($this->childrens as $child){
                $title = $child->getLabel();
                $selected = "";
                $ico = $view->getTemplateWebPath("/gfx/droite16_ns.png");
                if(isset($child->childrens) && !is_null($child->childrens)){
                    $nextChildrens = $child;
                    $selected = "selected";
                    $ico = $view->getTemplateWebPath("/gfx/droite16_s.png");
                }
                ?>
                <li class="elem <?php echo $selected; ?>" rel="<?php echo ""; ?>">
                    <table>
                        <tr>
                            <td>
                                <a title="<? echo $title; ?>" href="<? echo $view->get('url_finder')."&id=".$child->get('id'); ?>">
                                <?php
                                echo dims_strcut($title,50);
                                ?>
                                </a>
                            </td>
                            <td class="arrow">
                                <?php
                                if($child->hasChildren()){
                                    ?>
                                    <div class="has_children">
                                        <a title="<? echo $title; ?>" href="<? echo $view->get('url_finder')."&id=".$child->get('id'); ?>">
                                            <img src="<? echo $ico; ?>"/>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </li>
                <?
            }
            ?>
        </ul>
    </div>
    <?
    if(!is_null($nextChildrens)){
        $nextChildrens->display(DIMS_APP_PATH."modules/catalogue/admin/views/familles/famille_finder_node.tpl.php");
    }
}
?>