<?php
$view = view::getInstance();
?>
<div class="sub_bloc" id="<?= $this->getId(); ?>">
    <?php
    $title = $this->getTitle();
    if (!empty($title)) {
        ?>
        <h3><?php echo $title; ?></h3>
        <?php
    }
    ?>
    <div class="sub_bloc_form">
        <table>
            <?
            $first = true;
            foreach($view->get('languages') as $idlg => $lg){
                ?>
                <tr <?= (!$first)?'style="display:none;"':""; ?> <?= 'class="fam_desc desc_'.$idlg.'"'; ?>>
                    <td class="label_field"><label for="status"><?= $this->get_field_label('label_'.$idlg); ?></label></td>
                    <td class="value_field"><?= $this->get_field_html('label_'.$idlg); ?></td>
                </tr>
                <tr <?= (!$first)?'style="display:none;"':""; ?> <?= 'class="fam_desc desc_'.$idlg.'"'; ?>>
                    <td class="label_field"><label for="families"><?= $this->get_field_label('fck_description_'.$idlg); ?></label></td>
                    <td class="value_field"><?= $this->get_field_html('fck_description_'.$idlg); ?></td>
                </tr>
                <?
                $first = false;
            }
            ?>
            <tr>
                <td class="label_field"><label for="display_mode"><?= $this->get_field_label('display_mode'); ?></label></td>
                <td class="value_field"><?= $this->get_field_html('display_mode'); ?></td>
            </tr>
        </table>
    </div>
</div>
