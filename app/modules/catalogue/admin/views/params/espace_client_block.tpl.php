<div class="sub_bloc">
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
            <tr>
                <td class="label_field w30">
                    <label><?= dims_constant::getVal('_ACTIVE_CART'); ?></label>
                </td>
                <td class="value_field">
                    <span id="active_cart">
                        <?= $this->get_field_html('active_cart', '0'); ?>
                        <label for="<?php echo $this->get_field_id('active_cart', '0'); ?>">
                            <?= $this->get_field_label('active_cart', '0'); ?>
                        </label>

                        <?= $this->get_field_html('active_cart', '1'); ?>
                        <label for="<?php echo $this->get_field_id('active_cart', '1'); ?>">
                            <?= $this->get_field_label('active_cart', '1'); ?>
                        </label>
                    </span>

                    <span id="info_active_cart" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_active_cart" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_ACTIVE_CART'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_active_cart').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field w30">
                    <label><?= dims_constant::getVal('_DIMS_LABEL_CONT_INFPERS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="personal_informations">
                        <?= $this->get_field_html('personal_informations', '0'); ?>
                        <label for="<?php echo $this->get_field_id('personal_informations', '0'); ?>">
                            <?= $this->get_field_label('personal_informations', '0'); ?>
                        </label>

                        <?= $this->get_field_html('personal_informations', '1'); ?>
                        <label for="<?php echo $this->get_field_id('personal_informations', '1'); ?>">
                            <?= $this->get_field_label('personal_informations', '1'); ?>
                        </label>
                    </span>

                    <span id="info_personal_informations" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_personal_informations" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_DIMS_LABEL_CONT_INFPERS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_personal_informations').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field w30">
                    <label><?= dims_constant::getVal('_QUEUED_COMMANDS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="wait_commandes">
                        <?= $this->get_field_html('wait_commandes', '0'); ?>
                        <label for="<?php echo $this->get_field_id('wait_commandes', '0'); ?>">
                            <?= $this->get_field_label('wait_commandes', '0'); ?>
                        </label>

                        <?= $this->get_field_html('wait_commandes', '1'); ?>
                        <label for="<?php echo $this->get_field_id('wait_commandes', '1'); ?>">
                            <?= $this->get_field_label('wait_commandes', '1'); ?>
                        </label>
                    </span>

                    <span id="info_wait_commandes" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_wait_commandes" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_QUEUED_COMMANDS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_wait_commandes').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_ORDERS_HISTORY'); ?></label>
                </td>
                <td class="value_field">
                    <span id="history_cmd">
                        <?= $this->get_field_html('history_cmd', '0'); ?>
                        <label for="<?php echo $this->get_field_id('history_cmd', '0'); ?>">
                            <?= $this->get_field_label('history_cmd', '0'); ?>
                        </label>

                        <?= $this->get_field_html('history_cmd', '1'); ?>
                        <label for="<?php echo $this->get_field_id('history_cmd', '1'); ?>">
                            <?= $this->get_field_label('history_cmd', '1'); ?>
                        </label>
                    </span>

                    <span id="info_history_cmd" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_history_cmd" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_ORDERS_HISTORY'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_history_cmd').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_CATA_EXCEPTIONAL_ORDERS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="exceptional_orders">
                        <?= $this->get_field_html('exceptional_orders', '0'); ?>
                        <label for="<?php echo $this->get_field_id('exceptional_orders', '0'); ?>">
                            <?= $this->get_field_label('exceptional_orders', '0'); ?>
                        </label>

                        <?= $this->get_field_html('exceptional_orders', '1'); ?>
                        <label for="<?php echo $this->get_field_id('exceptional_orders', '1'); ?>">
                            <?= $this->get_field_label('exceptional_orders', '1'); ?>
                        </label>
                    </span>

                    <span id="info_exceptional_orders" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_exceptional_orders" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_CATA_EXCEPTIONAL_ORDERS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_exceptional_orders').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_DELIVERY_NOTES'); ?></label>
                </td>
                <td class="value_field">
                    <span id="bon_livraison">
                        <?= $this->get_field_html('bon_livraison', '0'); ?>
                        <label for="<?php echo $this->get_field_id('bon_livraison', '0'); ?>">
                            <?= $this->get_field_label('bon_livraison', '0'); ?>
                        </label>

                        <?= $this->get_field_html('bon_livraison', '1'); ?>
                        <label for="<?php echo $this->get_field_id('bon_livraison', '1'); ?>">
                            <?= $this->get_field_label('bon_livraison', '1'); ?>
                        </label>
                    </span>

                    <span id="info_bon_livraison" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_bon_livraison" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_DELIVERY_NOTES'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_bon_livraison').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_REMAININGS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="remainings">
                        <?= $this->get_field_html('remainings', '0'); ?>
                        <label for="<?php echo $this->get_field_id('remainings', '0'); ?>">
                            <?= $this->get_field_label('remainings', '0'); ?>
                        </label>

                        <?= $this->get_field_html('remainings', '1'); ?>
                        <label for="<?php echo $this->get_field_id('remainings', '1'); ?>">
                            <?= $this->get_field_label('remainings', '1'); ?>
                        </label>
                    </span>

                    <span id="info_remainings" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_remainings" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_REMAININGS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_remainings').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_INVOICES'); ?></label>
                </td>
                <td class="value_field">
                    <span id="invoices">
                        <?= $this->get_field_html('invoices', '0'); ?>
                        <label for="<?php echo $this->get_field_id('invoices', '0'); ?>">
                            <?= $this->get_field_label('invoices', '0'); ?>
                        </label>

                        <?= $this->get_field_html('invoices', '1'); ?>
                        <label for="<?php echo $this->get_field_id('invoices', '1'); ?>">
                            <?= $this->get_field_label('invoices', '1'); ?>
                        </label>
                    </span>

                    <span id="info_invoices" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_invoices" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_INVOICES'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_invoices').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_CATA_ACCOUNT_STATEMENTS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="account_statements">
                        <?= $this->get_field_html('account_statements', '0'); ?>
                        <label for="<?php echo $this->get_field_id('account_statements', '0'); ?>">
                            <?= $this->get_field_label('account_statements', '0'); ?>
                        </label>

                        <?= $this->get_field_html('account_statements', '1'); ?>
                        <label for="<?php echo $this->get_field_id('account_statements', '1'); ?>">
                            <?= $this->get_field_label('account_statements', '1'); ?>
                        </label>
                    </span>

                    <span id="info_account_statements" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_account_statements" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_CATA_ACCOUNT_STATEMENTS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_account_statements').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_RAPID_ENTRY'); ?></label>
                </td>
                <td class="value_field">
                    <span id="saisie_rapide">
                        <?= $this->get_field_html('saisie_rapide', '0'); ?>
                        <label for="<?php echo $this->get_field_id('saisie_rapide', '0'); ?>">
                            <?= $this->get_field_label('saisie_rapide', '0'); ?>
                        </label>

                        <?= $this->get_field_html('saisie_rapide', '1'); ?>
                        <label for="<?php echo $this->get_field_id('saisie_rapide', '1'); ?>">
                            <?= $this->get_field_label('saisie_rapide', '1'); ?>
                        </label>
                    </span>

                    <span id="info_saisie_rapide" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_saisie_rapide" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_RAPID_ENTRY'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_saisie_rapide').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_BASKETS_KINDS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="panier_type">
                        <?= $this->get_field_html('panier_type', '0'); ?>
                        <label for="<?php echo $this->get_field_id('panier_type', '0'); ?>">
                            <?= $this->get_field_label('panier_type', '0'); ?>
                        </label>

                        <?= $this->get_field_html('panier_type', '1'); ?>
                        <label for="<?php echo $this->get_field_id('panier_type', '1'); ?>">
                            <?= $this->get_field_label('panier_type', '1'); ?>
                        </label>
                    </span>

                    <span id="info_panier_type" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_panier_type" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_BASKETS_KINDS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_panier_type').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_SCHOOL_LISTS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="school_lists">
                        <?= $this->get_field_html('school_lists', '0'); ?>
                        <label for="<?php echo $this->get_field_id('school_lists', '0'); ?>">
                            <?= $this->get_field_label('school_lists', '0'); ?>
                        </label>

                        <?= $this->get_field_html('school_lists', '1'); ?>
                        <label for="<?php echo $this->get_field_id('school_lists', '1'); ?>">
                            <?= $this->get_field_label('school_lists', '1'); ?>
                        </label>
                    </span>

                    <span id="info_school_lists" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_school_lists" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_SCHOOL_LISTS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_school_lists').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('CATA_STATISTICS'); ?></label>
                </td>
                <td class="value_field">
                    <span id="statistics">
                        <?= $this->get_field_html('statistics', '0'); ?>
                        <label for="<?php echo $this->get_field_id('statistics', '0'); ?>">
                            <?= $this->get_field_label('statistics', '0'); ?>
                        </label>

                        <?= $this->get_field_html('statistics', '1'); ?>
                        <label for="<?php echo $this->get_field_id('statistics', '1'); ?>">
                            <?= $this->get_field_label('statistics', '1'); ?>
                        </label>
                    </span>

                    <span id="info_statistics" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_statistics" class="info_popup">
                        <p><strong><?= dims_constant::getVal('CATA_STATISTICS'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_statistics').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label_field">
                    <label><?= dims_constant::getVal('_CATA_HIERARCHY_VALIDATION'); ?></label>
                </td>
                <td class="value_field">
                    <span id="hierarchy_validation">
                        <?= $this->get_field_html('hierarchy_validation', '0'); ?>
                        <label for="<?php echo $this->get_field_id('hierarchy_validation', '0'); ?>">
                            <?= $this->get_field_label('hierarchy_validation', '0'); ?>
                        </label>

                        <?= $this->get_field_html('hierarchy_validation', '1'); ?>
                        <label for="<?php echo $this->get_field_id('hierarchy_validation', '1'); ?>">
                            <?= $this->get_field_label('hierarchy_validation', '1'); ?>
                        </label>
                    </span>

                    <span id="info_hierarchy_validation" class="info_link">
                        <img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
                    </span>
                    <span id="popup_hierarchy_validation" class="info_popup">
                        <p><strong><?= dims_constant::getVal('_CATA_HIERARCHY_VALIDATION'); ?> :</strong></p>
                        <p>
                            Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
                            <a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_hierarchy_validation').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
                        </p>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>
