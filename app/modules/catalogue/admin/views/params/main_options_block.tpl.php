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
				<td valign="top">
					<table>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_SYNCHRONIZED_CATALOG'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_synchronized">
									<?php echo $this->get_field_html('cata_synchronized', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_synchronized', '0'); ?>"><?php echo $this->get_field_label('cata_synchronized', '0'); ?></label>

									<?php echo $this->get_field_html('cata_synchronized', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_synchronized', '1'); ?>"><?php echo $this->get_field_label('cata_synchronized', '1'); ?></label>
								</span>

								<span id="info_cata_synchronized" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_synchronized" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_SYNCHRONIZED_CATALOG'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_synchronized').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_B2C_CATALOG'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_mode_B2C">
									<?php echo $this->get_field_html('cata_mode_B2C', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_mode_B2C', '0'); ?>"><?php echo $this->get_field_label('cata_mode_B2C', '0'); ?></label>

									<?php echo $this->get_field_html('cata_mode_B2C', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_mode_B2C', '1'); ?>"><?php echo $this->get_field_label('cata_mode_B2C', '1'); ?></label>
								</span>

								<span id="info_cata_mode_B2C" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_mode_B2C" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_B2C_CATALOG'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_mode_B2C').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_VISIBLE_NOT_CONNECTED'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_visible_not_connected">
									<?php echo $this->get_field_html('cata_visible_not_connected', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_visible_not_connected', '0'); ?>"><?php echo $this->get_field_label('cata_visible_not_connected', '0'); ?></label>

									<?php echo $this->get_field_html('cata_visible_not_connected', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_visible_not_connected', '1'); ?>"><?php echo $this->get_field_label('cata_visible_not_connected', '1'); ?></label>
								</span>

								<span id="info_cata_visible_not_connected" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_visible_not_connected" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_VISIBLE_NOT_CONNECTED'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_visible_not_connected').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_BRAND_MANAGEMENT'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_active_marques">
									<?php echo $this->get_field_html('cata_active_marques', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_active_marques', '0'); ?>"><?php echo $this->get_field_label('cata_active_marques', '0'); ?></label>

									<?php echo $this->get_field_html('cata_active_marques', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_active_marques', '1'); ?>"><?php echo $this->get_field_label('cata_active_marques', '1'); ?></label>
								</span>

								<span id="info_cata_active_marques" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_active_marques" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_BRAND_MANAGEMENT'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_active_marques').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_PRICES_DISPLAY_MODE'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_base_TTC">
									<?php echo $this->get_field_html('cata_base_ttc', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_base_ttc', '0'); ?>"><?php echo $this->get_field_label('cata_base_ttc', '0'); ?></label>

									<?php echo $this->get_field_html('cata_base_ttc', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_base_ttc', '1'); ?>"><?php echo $this->get_field_label('cata_base_ttc', '1'); ?></label>
								</span>

								<span id="info_cata_base_TTC" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_base_TTC" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_PRICES_DISPLAY_MODE'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_base_TTC').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_ALLOW_ORDERS_OUTSIDE_CATALOG'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_permit_horscata">
									<?php echo $this->get_field_html('cata_permit_horscata', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_permit_horscata', '0'); ?>"><?php echo $this->get_field_label('cata_permit_horscata', '0'); ?></label>

									<?php echo $this->get_field_html('cata_permit_horscata', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_permit_horscata', '1'); ?>"><?php echo $this->get_field_label('cata_permit_horscata', '1'); ?></label>
								</span>

								<span id="info_cata_permit_horscata" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_permit_horscata" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_ALLOW_ORDERS_OUTSIDE_CATALOG'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_permit_horscata').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('_CATA_CART_MANAGEMENT'); ?></label>
							</td>
							<td class="value_field">
								<span id="cart_management">
									<?php echo $this->get_field_html('cart_management', '0'); ?>
									<label for="<?php echo $this->get_field_id('cart_management', '0'); ?>"><?php echo $this->get_field_label('cart_management', '0'); ?></label>

									<?php echo $this->get_field_html('cart_management', '1'); ?>
									<label for="<?php echo $this->get_field_id('cart_management', '1'); ?>"><?php echo $this->get_field_label('cart_management', '1'); ?></label>
								</span>

								<span id="info_cart_management" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cart_management" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('_CATA_CART_MANAGEMENT'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cart_management').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('_CATA_ALLOW_EDIT_PERSONAL_INFOS'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_infos_persos_editable">
									<?php echo $this->get_field_html('cata_infos_persos_editable', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_infos_persos_editable', '1'); ?>"><?php echo $this->get_field_label('cata_infos_persos_editable', '1'); ?></label>

									<?php echo $this->get_field_html('cata_infos_persos_editable', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_infos_persos_editable', '0'); ?>"><?php echo $this->get_field_label('cata_infos_persos_editable', '0'); ?></label>
								</span>

								<span id="info_cata_infos_persos_editable" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_infos_persos_editable" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('_CATA_ALLOW_EDIT_PERSONAL_INFOS'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_infos_persos_editable').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>

						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('ALLOW_EDITING_OF_QUOTE_LINES'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_edit_quotelines">
									<?php echo $this->get_field_html('cata_edit_quotelines', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_edit_quotelines', '1'); ?>"><?php echo $this->get_field_label('cata_edit_quotelines', '1'); ?></label>

									<?php echo $this->get_field_html('cata_edit_quotelines', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_edit_quotelines', '0'); ?>"><?php echo $this->get_field_label('cata_edit_quotelines', '0'); ?></label>
								</span>

								<span id="info_cata_edit_quotelines" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_edit_quotelines" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('ALLOW_EDITING_OF_QUOTE_LINES'); ?></strong></p>
									<p>
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_edit_quotelines').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>

						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('LABEL_PATTERN'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_label_pattern">
									<?php echo $this->get_field_html('cata_label_pattern'); ?>
								</span>

								<span id="info_cata_label_pattern" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_label_pattern" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('LABEL_PATTERN'); ?> :</strong></p>
									<p>
										T: Type (Devis, Facture, Bons de commande) ;<br />
										Y: Année courante ;<br />
										X: Année d'exercice courant ;<br />
										*: Remplissage numérique.<br />
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_label_pattern').fadeOut();">
											Fermer
										</a>
									</p>
								</span>
							</td>
						</tr>

						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('FISCAL_YEAR'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_fiscal_year">
									<?php echo $this->get_field_html('cata_fiscal_year'); ?>
								</span>

								<span id="info_cata_fiscal_year" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_fiscal_year" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('FISCAL_YEAR'); ?></strong></p>
									<p>
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_fiscal_year').fadeOut();">
											Fermer
										</a>
									</p>
								</span>
							</td>
						</tr>

						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_DOCUMENTS_TEMPLATE'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_documents_template">
									<?php echo $this->get_field_html('cata_documents_template'); ?>
								</span>

								<span id="info_cata_documents_template" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_documents_template" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_DOCUMENTS_TEMPLATE'); ?></strong></p>
									<p>
										<?= dims_constant::getVal('CATA_DOCUMENTS_TEMPLATE_FOR_PRINT'); ?>
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_documents_template').fadeOut();">
											Fermer
										</a>
									</p>
								</span>
							</td>
						</tr>

					</table>
				</td>
				<td valign="top">
					<table>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_ACTIVATE_NEGATIVE_STOCK'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_negative_stocks">
									<?php echo $this->get_field_html('cata_negative_stocks', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_negative_stocks', '0'); ?>"><?php echo $this->get_field_label('cata_negative_stocks', '0'); ?></label>

									<?php echo $this->get_field_html('cata_negative_stocks', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_negative_stocks', '1'); ?>"><?php echo $this->get_field_label('cata_negative_stocks', '1'); ?></label>
								</span>

								<span id="info_cata_negative_stocks" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_negative_stocks" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_ACTIVATE_NEGATIVE_STOCK'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_negative_stocks').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_ACTIVATE_STOCK_QTY'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_show_stocks">
									<?php echo $this->get_field_html('cata_show_stocks', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_show_stocks', '0'); ?>"><?php echo $this->get_field_label('cata_show_stocks', '0'); ?></label>

									<?php echo $this->get_field_html('cata_show_stocks', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_show_stocks', '1'); ?>"><?php echo $this->get_field_label('cata_show_stocks', '1'); ?></label>
								</span>

								<span id="info_cata_show_stocks" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_show_stocks" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_ACTIVATE_STOCK_QTY'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_show_stocks').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_DETAIL_REMAININGS'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_detail_reliquats">
									<?php echo $this->get_field_html('cata_detail_reliquats', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_detail_reliquats', '0'); ?>"><?php echo $this->get_field_label('cata_detail_reliquats', '0'); ?></label>

									<?php echo $this->get_field_html('cata_detail_reliquats', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_detail_reliquats', '1'); ?>"><?php echo $this->get_field_label('cata_detail_reliquats', '1'); ?></label>
								</span>

								<span id="info_cata_detail_reliquats" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_detail_reliquats" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_DETAIL_REMAININGS'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_detail_reliquats').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_ALERT_IF_MIN_STOCK_REACHED'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_alert_stock_mini">
									<?php echo $this->get_field_html('cata_alert_stock_mini', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_alert_stock_mini', '0'); ?>"><?php echo $this->get_field_label('cata_alert_stock_mini', '0'); ?></label>

									<?php echo $this->get_field_html('cata_alert_stock_mini', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_alert_stock_mini', '1'); ?>"><?php echo $this->get_field_label('cata_alert_stock_mini', '1'); ?></label>
								</span>

								<span id="info_cata_alert_stock_mini" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_alert_stock_mini" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_ALERT_IF_MIN_STOCK_REACHED'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_alert_stock_mini').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_FAMILYS_DISPLAY'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_nav_style">
									<?php echo $this->get_field_html('cata_nav_style', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_nav_style', '0'); ?>"><?php echo $this->get_field_label('cata_nav_style', '0'); ?></label>

									<?php echo $this->get_field_html('cata_nav_style', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_nav_style', '1'); ?>"><?php echo $this->get_field_label('cata_nav_style', '1'); ?></label>
								</span>

								<span id="info_cata_nav_style" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_nav_style" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_FAMILYS_DISPLAY'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_nav_style').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_FAMILYS_DISPLAY'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_default_show_families">
									<?php echo $this->get_field_html('cata_default_show_families', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_default_show_families', '1'); ?>"><?php echo $this->get_field_label('cata_default_show_families', '1'); ?></label>

									<?php echo $this->get_field_html('cata_default_show_families', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_default_show_families', '0'); ?>"><?php echo $this->get_field_label('cata_default_show_families', '0'); ?></label>
								</span>

								<span id="info_cata_default_show_families" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_default_show_families" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('_CATA_DEFAULT_SHOW_FAMILIES'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_default_show_families').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?php echo dims_constant::getVal('CATA_FILTERS_OPERATING_MODE'); ?></label>
							</td>
							<td class="value_field">
								<span id="cata_filters_view">
									<?php echo $this->get_field_html('cata_filters_view', '1'); ?>
									<label for="<?php echo $this->get_field_id('cata_filters_view', '1'); ?>"><?php echo $this->get_field_label('cata_filters_view', '1'); ?></label>

									<?php echo $this->get_field_html('cata_filters_view', '0'); ?>
									<label for="<?php echo $this->get_field_id('cata_filters_view', '0'); ?>"><?php echo $this->get_field_label('cata_filters_view', '0'); ?></label>
								</span>

								<span id="info_cata_filters_view" class="info_link"><img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" /></span>
								<span id="popup_cata_filters_view" class="info_popup">
									<p><strong><?php echo dims_constant::getVal('CATA_FILTERS_OPERATING_MODE'); ?> :</strong></p>
									<p>
										Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
										<a title="Fermer le popup" href="javascript:void(0);" onclick="javascript:$('#popup_cata_filters_view').fadeOut();">Fermer</a>
									</p>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
