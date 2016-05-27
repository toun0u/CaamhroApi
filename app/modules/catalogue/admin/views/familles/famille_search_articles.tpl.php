<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
$defaultArt_search = addslashes(dims_constant::getVal('_SEARCH'));
$searchArt = dims::getInstance()->getScriptEnv()."?c=familles&a=searchArticleAttach&id=".$elem->fields["id"];
$additional_js = <<< ADD_JS
var tmpSearch = null;
function searchArticle(){
    clearInterval(tmpSearch);
    tmpSearch = null;
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
                $("div.result_art").html(data);
            }
        });
    }else
        $("div.result_art").html("");
}

$('div.input_container input[type="image"]').click(function(event){
    event.preventDefault();
    $('form#new_vign').submit(function(){
        return false;
    });
    searchArticle();
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
        searchArticle();
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
    tmpSearch = setInterval(searchArticle,1500);
});
ADD_JS;
$form = new Dims\form(array(
    'name'              => 'search_art',
    'action'            => $view->get('action_path'),
    'back_name'         => dims_constant::getVal('_DIMS_CANCEL'),
    'back_url'          => $view->get('back_path'),
    'submit_value'      => dims_constant::getVal('_SAVE_SELECTION'),
    'include_actions'   => true,
    'additional_js'     => $additional_js,
    'validation'        => false
));
$form->addBlock('default',dims_constant::getVal('_ATTACH_ARTICLES'));
$form->add_text_field(array(
    'name'          => 'art_keywords',
    'id'            => 'art_keywords',
    'block'         => 'default',
    'classes'       => 'keywords_search default search_input',
    'value'         => $defaultArt_search
));
$form->add_hidden_field(array(
    'name'          => 'selection'
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
            <div class="input_container">
                <?= $desc_block->get_field_html('art_keywords'); ?>
                <input type="image" src="<?= $view->getTemplateWebPath('gfx/loupe16.png');?>" value=""/>
            </div>
        </div>
        <div class="result_art"></div>
        <?= $form->displayActionsBlock(); ?>
    </div>
    <?= $form->close_form(); ?>
</div>
