<div class="sub_bloc">
    <?php
    $title = $this->getTitle();
    if (!empty($title)) {
        ?>
        <h3>
            <?= $title; ?>
        </h3>
        <?php
    }
    ?>
    <div class="sub_bloc_form">
        <table>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('default_tva'); ?>"><?= $this->get_field_label('default_tva'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('default_tva'); ?>
                    <span id="info_<?= $this->get_field_id('default_tva'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('default_tva'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('default_tva'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('default_tva'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td class="label_field w20">
                    <label><?= dims_constant::getVal('_NET_PRICE_MANAGEMENT'); ?></label>
                </td>
                <td class="value_field">
                    <span id="gestion_prix_net">
                        <?= $this->get_field_html('gestion_prix_net', '0'); ?>
                        <label for="<?php echo $this->get_field_id('gestion_prix_net', '0'); ?>">
                            <?= $this->get_field_label('gestion_prix_net', '0'); ?>
                        </label>

                        <?= $this->get_field_html('gestion_prix_net', '1'); ?>
                        <label for="<?php echo $this->get_field_id('gestion_prix_net', '1'); ?>">
                            <?= $this->get_field_label('gestion_prix_net', '1'); ?>
                        </label>
                    </span>
                    <span id="info_gestion_prix_net" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_gestion_prix_net" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_NET_PRICE_MANAGEMENT'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_gestion_prix_net').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('default_tva'); ?>" class="mess_error"></div></td>
                <td></td>
                <td><div id="def_gestion_prix_net" class="mess_error"></div></td>
            </tr>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('remise_web'); ?>"><?= $this->get_field_label('remise_web'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('remise_web'); ?>
                    <span id="info_<?= $this->get_field_id('remise_web'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('remise_web'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('remise_web'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('remise_web'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td class="label_field w20">
                    <label><?= dims_constant::getVal('_MANAGEMENTS_DISCOUNTS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="gestion_escompte">
                        <?= $this->get_field_html('gestion_escompte', '0'); ?>
                        <label for="<?php echo $this->get_field_id('gestion_escompte', '0'); ?>">
                            <?= $this->get_field_label('gestion_escompte', '0'); ?>
                        </label>

                        <?= $this->get_field_html('gestion_escompte', '1'); ?>
                        <label for="<?php echo $this->get_field_id('gestion_escompte', '1'); ?>">
                            <?= $this->get_field_label('gestion_escompte', '1'); ?>
                        </label>
                    </span>
                    <span id="info_gestion_escompte" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_gestion_escompte" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_MANAGEMENTS_DISCOUNTS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_gestion_escompte').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('remise_web'); ?>" class="mess_error"></div></td>
                <td></td>
                <td><div id="def_gestion_escompte" class="mess_error"></div></td>
            </tr>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('devise'); ?>"><?= $this->get_field_label('devise'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('devise'); ?>
                    <span id="info_<?= $this->get_field_id('devise'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('devise'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('devise'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('devise'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('regles_remises'); ?>"><?= $this->get_field_label('regles_remises'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('regles_remises'); ?>
                    <span id="info_<?= $this->get_field_id('regles_remises'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('regles_remises'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('regles_remises'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('regles_remises'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('devise'); ?>" class="mess_error"></div></td>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('regles_remises'); ?>" class="mess_error"></div></td>
            </tr>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('command_mini'); ?>"><?= $this->get_field_label('command_mini'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('command_mini'); ?>
                    <span id="info_<?= $this->get_field_id('command_mini'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('command_mini'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('command_mini'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('command_mini'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('command_mini'); ?>" class="mess_error"></div></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('franco_port'); ?>"><?= $this->get_field_label('franco_port'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('franco_port'); ?>
                    <span id="info_<?= $this->get_field_id('franco_port'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('franco_port'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('franco_port'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('franco_port'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('supplement_hayon'); ?>"><?= $this->get_field_label('supplement_hayon'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('supplement_hayon'); ?>
                    <span id="info_<?= $this->get_field_id('supplement_hayon'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('supplement_hayon'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('supplement_hayon'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('supplement_hayon'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('franco_port'); ?>" class="mess_error"></div></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label_field w20">
                    <label for="<?= $this->get_field_id('tarif_transporteur'); ?>"><?= $this->get_field_label('tarif_transporteur'); ?></label>
                </td>
                <td class="value_field">
                    <?= $this->get_field_html('tarif_transporteur'); ?>
                    <span id="info_<?= $this->get_field_id('tarif_transporteur'); ?>" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_<?= $this->get_field_id('tarif_transporteur'); ?>" class="info_popup">
                        <p><strong><?= $this->get_field_label('tarif_transporteur'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('tarif_transporteur'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td></td>
                <td><div id="def_<?= $this->get_field_id('tarif_transporteur'); ?>" class="mess_error"></div></td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>
</div>
