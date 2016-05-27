<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
if(isset($elem->fields) && $elem->fields['id_parent'] != '' && $elem->fields['id_parent'] > 0){
    ?>
    <div class="sub_menu">
        <a href="<?= $view->get('url_finder')."&sa=articles&id=".$elem->get('id'); ?>" <?php if($this->get('sa') == 'articles') echo 'class="selected"';?>>
            <div><?php echo dims_constant::getVal('_LIST_OF_ARTICLES'); ?> (<?= count($view->get('lst_articles')); ?>)</div>
        </a>
        <a href="<?= $view->get('url_finder')."&sa=properties&id=".$elem->get('id'); ?>" <?php if($this->get('sa') == 'properties') echo 'class="selected"';?>>
            <div><?php echo dims_constant::getVal('_DIMS_PROPERTIES'); ?></div>
        </a>
        <a href="<?= $view->get('url_finder')."&sa=vignettes&id=".$elem->get('id'); ?>" <?php if($this->get('sa') == 'vignettes' || $this->get('sa') == 'newVign') echo 'class="selected"';?>>
            <div><?php echo dims_constant::getVal('_THUMBNAILS'); ?></div>
        </a>
        <a href="<?= $view->get('url_finder')."&sa=chplibres&id=".$elem->get('id'); ?>" <?php if($this->get('sa') == 'chplibres' || $this->get('sa') == 'editchamp') echo 'class="selected"';?>>
            <div><?php echo dims_constant::getVal('_FREE_FIELDS'); ?></div>
        </a>
<!--         <a href="<?= $view->get('url_finder')."&sa=selections&id=".$elem->get('id'); ?>" <?php if($this->get('sa') == 'selections' || $this->get('sa') == 'selection_edit') echo 'class="selected"';?>>
            <div><?php echo dims_constant::getVal('CATA_SELECTIONS'); ?></div>
        </a>
 -->    </div>
    <?
}
?>
