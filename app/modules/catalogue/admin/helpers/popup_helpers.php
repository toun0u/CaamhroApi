<?php
function _constructStandardPopup($id = "dims_popup",$title = "", $content = ""){
    $view = view::getInstance();
    ?>
    <h3>
        <?= $title; ?>
        <img onclick="javascript:dims_closeOverlayedPopup(<?= $id; ?>);" alt="<?= dims_constant::getVal("_DIMS_CLOSE"); ?>" title="<?= dims_constant::getVal("_DIMS_CLOSE"); ?>" src="<? echo $view->getTemplateWebPath("/gfx/supprimer16.png"); ?>" />
    </h3>
    <div class="content">
        <?= $content; ?>
    </div>
    <div class="actions">
        <input onclick="javascript:dims_closeOverlayedPopup(<?= $id; ?>);" type="button" value="<?= dims_constant::getVal("_DIMS_CLOSE"); ?>" />
    </div>
    <?
}
?>