<?php
$view = view::getInstance();
$langues = $view->get('langues');
$lang = $view->get('lang');

$additional_js = <<< ADDITIONAL_JS
$("div.radio_flag").click(function(){
    $("div.radio_flag").removeClass('selected');
    $(this).addClass('selected');
    $("input",$(this)).attr('checked',!$("input",$(this)).is(":checked"));
});
ADDITIONAL_JS;

$lstAlready = array();
foreach($langues as $lg){
    if($lg->get('id') != $lang->get('id'))
        $lstAlready[] = $lg->fields['ref'];
}

$form = new Dims\form(array(
    'name'              => 'lg_params',
    'action'            => get_path('params', 'lg_save'),
    'validation'        => true,
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('params', 'lg_index'),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'additional_js'     => $additional_js
));
if($lang->isNew())
    $form->addBlock('default', dims_constant::getVal('_CREATION_LANGUAGE'));
else
    $form->addBlock('default', dims_constant::getVal('_EDITION_LANGUAGE'));

$form->add_hidden_field(array(
    'name'      => 'id',
    'value'     => $lang->get('id')
));

$form->add_text_field(array(
    'id'        => 'lg_label',
    'name'      => 'lg_label',
    'value'     => $lang->fields['label'],
    'label'     => dims_constant::getVal('_DIMS_LABEL'),
    'mandatory' => true
));
$block1 = $form->getBlock('default');
echo $form->get_header();
?>
<div class="form_object_block">
    <?= $block1->get_field_html('id'); ?>
    <div class="sub_bloc">
        <?php
        $title = $block1->getTitle();
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
                        <label for="<?= $block1->get_field_id('lg_label'); ?>">
                            <?= $block1->get_field_label('lg_label'); ?>
                        </label>
                        <span class="required">*</span>
                    </td>
                    <td class="value_field">
                        <?= $block1->get_field_html('lg_label'); ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><div class="mess_error" id="def_<?= $block1->get_field_id('lg_label'); ?>"></div></td>
                </tr>
                <tr>
                    <td class="label_field">
                        <label>
                            <?= dims_constant::getVal('_FLAG'); ?>
                        </label>
                    </td>
                    <td class="value_field">
                        <?php
                            if ($handle = opendir(DIMS_APP_PATH.'/img/flag')) {
                                while (false !== ($entry = readdir($handle))) {
                                    if ($entry != '.' && $entry != '..'){
                                        $ref = substr($entry,5,2);
                                        if(!in_array($ref,$lstAlready)){
                                            ?>
                                            <div class="radio_flag<?= ($ref == $lang->fields['label'])?" selected":""; ?>">
                                                <img title="<?= $ref; ?>" alt="<?= $ref; ?>" src="/img/flag/<?= $entry; ?>" />
                                                <?php
                                                echo $form->radio_field(array(
                                                    'name'      => 'lg_ref',
                                                    'id'        => 'flag_'.$ref,
                                                    'value'     => $ref,
                                                    'additionnal_attributes' => 'style="display:none;"'
                                                ));
                                                ?>
                                            </div>
                                            <?
                                        }
                                    }
                                }
                            }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <?= $form->displayActionsBlock(); ?>
    </div>
</div>
<?php
echo $form->close_form();
?>
