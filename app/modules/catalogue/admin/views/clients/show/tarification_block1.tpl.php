<div class="sub_bloc">
    <?php
    $title = $this->getTitle();
    if (!empty($title)) {
        ?><h3><?= $title; ?></h3><?php
    }
    ?>
    <div class="sub_bloc_form">
        <table>
            <tr>
                <td valign="top">
                    <table>
                        <tr>
                            <td class="label_field">
                                <label for="<?= $this->get_field_id('cli_escompte'); ?>"><?= $this->get_field_label('cli_escompte'); ?></label>
                            </td>
                            <td class="value_field">
                                <?= $this->get_field_html('cli_escompte'); ?>
                                <span style="color:#FD661F;">&#37;</span>
                            </td>
                            <td class="label_field">
                                <label for="<?= $this->get_field_id('cli_minimum_cde'); ?>"><?= $this->get_field_label('cli_minimum_cde'); ?></label>
                            </td>
                            <td class="value_field">
                                <?= $this->get_field_html('cli_minimum_cde'); ?>
                                <span style="color:#FD661F;">&euro;</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label_field">
                                <label for="<?= $this->get_field_id('franco'); ?>"><?= $this->get_field_label('franco'); ?></label>
                            </td>
                            <td class="value_field">
                                <?= $this->get_field_html('franco'); ?>
                                <span style="color:#FD661F;">&euro;</span>
                            </td>
                            <td class="label_field label_top">
                                <label for="<?= $this->get_field_id('means_of_payment[]'); ?>"><?= $this->get_field_label('means_of_payment[]'); ?></label>
                            </td>
                            <td class="value_field">
                                <?= $this->get_field_html('means_of_payment[]'); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="sub_form">
                        <div class="form_buttons">
                        <div>
                            <a href="<?= get_path('clients', 'show',array('id'=>view::getInstance()->get('client')->get('id_client'),'sc'=>'tarification','sa'=>'init')); ?>">
                                <?= dims_constant::getVal('_RESET_DEFAULTS'); ?>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
