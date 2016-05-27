<?php
$client = view::getInstance()->get('client');
$style = $client->fields['use_add_client']?'style="display:none;"':"";
?>
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
                <td class="label_field" style="width:40%;">
                    <label for="<?= $this->get_field_id('cli_use_add_client'); ?>">
                        <?= $this->get_field_label('cli_use_add_client'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_use_add_client'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_nom'); ?>">
                        <?= $this->get_field_label('cli_nom'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_nom'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_adr1'); ?>">
                        <?= $this->get_field_label('cli_adr1'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_adr1'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_adr2'); ?>">
                        <?= $this->get_field_label('cli_adr2'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_adr2'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_adr3'); ?>">
                        <?= $this->get_field_label('cli_adr3'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_adr3'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_cp'); ?>">
                        <?= $this->get_field_label('cli_cp'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_cp'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_ville'); ?>">
                        <?= $this->get_field_label('cli_ville'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_ville'); ?>
                </td>
            </tr>
            <tr class="sub_factu" <?= $style; ?>>
                <td class="label_field">
                    <label for="<?= $this->get_field_id('cli_id_pays'); ?>">
                        <?= $this->get_field_label('cli_id_pays'); ?>
                    </label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('cli_id_pays'); ?>
                </td>
            </tr>
        </table>
    </div>
</div>