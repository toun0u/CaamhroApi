<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
$defaultArt_search = addslashes(dims_constant::getVal('_SEARCH'));
$searchArt = dims::getInstance()->getScriptEnv()."?c=familles&a=searchArticle&id=".$elem->fields["id"];
$additional_js = <<< ADD_JS
var tmpSearch = null;
function searchArticleThumb(){
    var val = $('input#art_keywords').val();
    if(val != '{$defaultArt_search}'){
        $.ajax({
            type: "GET",
            url: "{$searchArt}",
            async: true,
            dataType : "html",
            data : {
                "val" : val
            },
            success : function(data){
                $("div.result_thumb").html(data);
            }
        });
    }else
        $("div.result_thumb").html("");
    clearInterval(tmpSearch);
    tmpSearch = null;
}
var idShow = 'tr_type_0';
$('input[type="radio"][name="type"]').change(function(){
    $('tr#tr_'+$(this).attr('id')).show();
    $('tr#'+idShow).hide();
    idShow = 'tr_'+$(this).attr('id');
});
$('div.input_container input[type="image"]').click(function(event){
    event.preventDefault();
    $('form#new_vign').submit(function(){
        return false;
    });
    searchArticleThumb();
    clearInterval(tmpSearch);
    tmpSearch = null;
    return false;
});
$('input#art_keywords').focus(function(){
    //focus on
    if($(this).val() == '{$defaultArt_search}'){
        $(this).val('');
        $(this).removeClass('default');
    }
})
.keypress(function(event){
    if ( event.which == 13 ) {
        event.preventDefault();
        searchArticleThumb();
        clearInterval(tmpSearch);
        tmpSearch = null;
    }
})
.focusout(function(){
    //art_keywords out
    if($(this).val() == ''){
        $(this).val('{$defaultArt_search}');
        $(this).addClass('default');
    }
})
.keyup(function(){
    if(tmpSearch != null)
        clearInterval(tmpSearch);
    tmpSearch = setInterval(searchArticleThumb,1500);
});
ADD_JS;
$form = new Dims\form(array(
    'name'              => 'new_vign',
    'action'            => $view->get('action_path'),
    'validation'        => true,
    'back_name'         => dims_constant::getVal('_DIMS_CANCEL'),
    'back_url'          => $view->get('back_path'),
    'submit_value'      => dims_constant::getVal('_SAVE_SELECTION'),
    'include_actions'   => true,
    'additional_js'     => $additional_js,
    'enctype'           => true
));
$form->addBlock('default',dims_constant::getVal('_NEW_THUMBNAIL'));

// Position
$positions = array();
for($i=1;$i<=$view->get('nb_thumbnails')+1;$i++)
    $positions[$i] = $i;
$form->add_select_field(array(
    'name'          => 'vign_position',
    'id'            => 'vign_position',
    'label'         => dims_constant::getVal('_POSITION'),
    'options'       => $positions,
    'value'         => $view->get('nb_thumbnails')+1,
    'block'         => 'default'
));
// Type d'import
$form->add_radio_field(array(
    'name'          => 'type',
    'id'            => 'type_0',
    'value'         => 'import',
    'label'         => dims_constant::getVal('_IMPORT_A_PHOTO'),
    'checked'       => true,
    'block'         => 'default'
));
$form->add_radio_field(array(
    'name'          => 'type',
    'id'            => 'type_1',
    'value'         => 'art',
    'label'         => dims_constant::getVal('_USE_STICKER_ARTICLE'),
    'checked'       => false,
    'block'         => 'default'
));
// Importer une photo
$form->add_file_field(array(
    'name'          => 'file',
    'id'            => 'file',
    'block'         => 'default'
));
// Importer depuis un article
$form->add_text_field(array(
    'name'          => 'art_keywords',
    'id'            => 'art_keywords',
    'block'         => 'default',
    'classes'       => 'keywords_search default search_input',
    'value'         => $defaultArt_search
));

$desc_block = $form->getBlock('default');
?>
<div class="form_object_block">
    <?= $form->get_header(); ?>
    <div class="sub_bloc" id="<?= $desc_block->getId(); ?>">
        <?php
        $title = $desc_block->getTitle();
        if (!empty($title)) {
            ?>
            <h3><?php echo $title; ?></h3>
            <?php
        }
        ?>
        <div class="sub_bloc_form">
            <table>
                <tr>
                    <td class="label_field">
                        <label for="status">
                            <?= $desc_block->get_field_label('vign_position'); ?>
                        </label>
                    </td>
                    <td class="value_field">
                        <?= $desc_block->get_field_html('vign_position'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="value_field" style="text-align:right;">
                        <?= $desc_block->get_field_html('type', 0); ?>
                    </td>
                    <td class="label_field" style="text-align:left;">
                        <label for="<?= $desc_block->get_field_id('type', 0); ?>">
                            <?= $desc_block->get_field_label('type', 0); ?>
                        </label>
                    </td>
                </tr>
                <tr id="tr_<?= $desc_block->get_field_id('type', 0); ?>">
                    <td></td>
                    <td class="value_field">
                        <?= $desc_block->get_field_html('file'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="value_field" style="text-align:right;">
                        <?= $desc_block->get_field_html('type', 1); ?>
                    </td>
                    <td class="label_field" style="text-align:left;">
                        <label for="<?= $desc_block->get_field_id('type', 1); ?>">
                            <?= $desc_block->get_field_label('type', 1); ?>
                        </label>
                    </td>
                </tr>
                <tr id="tr_<?= $desc_block->get_field_id('type', 1); ?>" style="display:none;">
                    <td></td>
                    <td class="value_field">
                        <div class="input_container">
                            <?= $desc_block->get_field_html('art_keywords'); ?>
                            <input type="image" src="<?= $view->getTemplateWebPath('gfx/loupe16.png');?>" value=""/>
                        </div>
                        <div class="result_thumb"></div>
                    </td>
                </tr>
            </table>
        </div>
        <?= $form->displayActionsBlock(); ?>
    </div>
    <?= $form->close_form(); ?>
</div>
