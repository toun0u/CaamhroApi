<?php
$view = view::getInstance();
$client = $view->get('client');
$group = $view->get('current');

$additional_js = <<< ADDITIONAL_JS

ADDITIONAL_JS;
$form = new Dims\form(array(
    'name'              => 'attach_user',
    'action'            => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services','sa'=>'attachsave')),
    'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
    'back_url'          => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services','grid'=>$group->get('id'))),
    'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
    'include_actions'   => true,
    'validation'        => false,
    'additional_js'     => $additional_js,
));

$form->addBlock ('default', dims_constant::getVal('_ATTACH_USER'));

$form->add_hidden_field(array(
    'name'          => 'grid',
    'value'         => $group->get('id'),
));

$desc_block = $form->getBlock('default');
?>
<div class="form_object_block">
    <?= $form->get_header(); ?>
    <?= $desc_block->get_field_html('grid'); ?>
    <div class="sub_bloc" id="<?= $desc_block->get('id'); ?>">
        <?php
        $title = $desc_block->getTitle();
        if (!empty($title)) {
            ?>
            <h3><?php echo $title; ?></h3>
            <?php
        }
        ?>
        <div class="sub_bloc_form">
            <table class="tableau">
                <tr>
                    <td class="w5 title_tableau"></td>
                    <td class="title_tableau">
                        <?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?>
                    </td>
                    <td class="w40 title_tableau">
                        <?= dims_constant::getVal('_LOGIN'); ?>
                    </td>
                </tr>
                <?php
                $already = $view->get('already');
                foreach($view->get('users') as $user){
                    ?>
                    <tr>
                        <td style="text-align:center;">
                            <?php
                            echo $form->checkbox_field(array(
                                'name'      => 'users_attach[]',
                                'value'     => $user->get('id'),
                                'checked'   => in_array($user->get('id'),$already)
                            ));
                            ?>
                        </td>
                        <td>
                            <?= $user->getFirstname()." ".$user->getLastname(); ?>
                        </td>
                        <td>
                            <?= $user->fields['login']; ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?= $form->displayActionsBlock(); ?>
    </div>
    <?= $form->close_form(); ?>
</div>