# Create domain '*'
truncate dims_domain;
INSERT INTO `dims_domain` (`id`, `domain`, `access`, `ssl`, `webmail_http_code`) VALUES (NULL, '*', '2', '0', NULL);

# Create workspaces
truncate dims_workspace;
INSERT INTO `dims_workspace` (`id`, `id_workspace`, `label`, `code`, `system`, `protected`, `parents`, `iprules`, `macrules`, `admin_template`, `web_template`, `depth`, `mustdefinerule`, `typegroup`, `admin`, `public`, `web`, `admin_domainlist`, `title`, `meta_description`, `meta_keywords`, `meta_author`, `meta_copyright`, `meta_robots`, `web_domainlist`, `ssl`, `project`, `planning`, `contact`, `tickets`, `newsletter`, `background`, `sitemap`, `email`, `signature`, `email_noreply`, `id_tiers`, `contact_intel`, `contact_docs`, `contact_tags`, `contact_comments`, `contact_activeent`, `contact_outlook`, `code_of_conduct`, `share_info`, `id_lang`, `newsletter_sender_email`, `newsletter_id_domain`, `newsletter_header_registration`, `newsletter_footer_registration`, `newsletter_accepted_subject`, `newsletter_accepted_content`, `newsletter_unsubscribe_subject`, `newsletter_unsubscribe_content`, `newsletter_message_registration`, `events_sender_email`, `events_signature`, `events_mail2_subject`, `events_mail2_content`, `events_mail3_subject`, `events_mail3_content`, `events_mail4_subject`, `events_mail4_content`, `events_mail5_subject`, `events_mail5_content`, `events_mail6_subject`, `events_mail6_content`, `events_mail7_subject`, `events_mail7_content`, `events_mail8_subject`, `events_mail8_content`, `events_mail9_subject`, `events_mail9_content`, `events_mail10_subject`, `events_mail10_content`, `events_mail11_subject`, `events_mail11_content`, `events_mail1_subject`, `events_mail1_content`, `id_workspace_feedback`) VALUES (NULL, '0', 'system', NULL, '1', '0', '0', NULL, NULL, NULL, NULL, '1', '0', NULL, '1', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '0', '1', '0', NULL, NULL, NULL, NULL, NULL, '0', '1', '1', '1', '1', '1', '1', '0', '1', '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO `dims_workspace` (`id`, `id_workspace`, `label`, `code`, `system`, `protected`, `parents`, `iprules`, `macrules`, `admin_template`, `web_template`, `depth`, `mustdefinerule`, `typegroup`, `admin`, `public`, `web`, `admin_domainlist`, `title`, `meta_description`, `meta_keywords`, `meta_author`, `meta_copyright`, `meta_robots`, `web_domainlist`, `ssl`, `project`, `planning`, `contact`, `tickets`, `newsletter`, `background`, `sitemap`, `email`, `signature`, `email_noreply`, `id_tiers`, `contact_intel`, `contact_docs`, `contact_tags`, `contact_comments`, `contact_activeent`, `contact_outlook`, `code_of_conduct`, `share_info`, `id_lang`, `newsletter_sender_email`, `newsletter_id_domain`, `newsletter_header_registration`, `newsletter_footer_registration`, `newsletter_accepted_subject`, `newsletter_accepted_content`, `newsletter_unsubscribe_subject`, `newsletter_unsubscribe_content`, `newsletter_message_registration`, `events_sender_email`, `events_signature`, `events_mail2_subject`, `events_mail2_content`, `events_mail3_subject`, `events_mail3_content`, `events_mail4_subject`, `events_mail4_content`, `events_mail5_subject`, `events_mail5_content`, `events_mail6_subject`, `events_mail6_content`, `events_mail7_subject`, `events_mail7_content`, `events_mail8_subject`, `events_mail8_content`, `events_mail9_subject`, `events_mail9_content`, `events_mail10_subject`, `events_mail10_content`, `events_mail11_subject`, `events_mail11_content`, `events_mail1_subject`, `events_mail1_content`, `id_workspace_feedback`) VALUES (NULL, '1', 'Dims', NULL, '0', '0', '0;1', NULL, NULL, 'dims_v5', NULL, '2', '0', NULL, '1', '0', '0', NULL, 'Dims Portal', NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '0', '1', '0', NULL, NULL, NULL, NULL, NULL, '0', '1', '1', '1', '1', '1', '1', '0', '1', '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');

# Activate workspace and domain
truncate dims_workspace_domain;
INSERT INTO `dims_workspace_domain` (`id_workspace`, `id_domain`, `access`) VALUES ('2', '1', '2');

# Create group user
truncate dims_group;
INSERT INTO `dims_group` (`id`, `id_group`, `label`, `system`, `protected`, `parents`, `depth`, `id_workspace`, `shared`, `code`, `reference`) VALUES (NULL, '0', 'system', '1', '1', '0', '1', '0', '0', NULL, NULL), (NULL, '1', 'Users', '0', '1', '0;1', '2', '1', '0', NULL, NULL);

# Create admin account
truncate dims_user;
INSERT INTO `dims_user` (`id`, `id_type`, `id_ldap`, `lastname`, `firstname`, `login`, `password`, `date_creation`, `date_expire`, `email`, `phone`, `fax`, `comments`, `address`, `mobile`, `service`, `function`, `postalcode`, `city`, `country`, `ticketsbyemail`, `color`, `timezone`, `defaultworkspace`, `presentation`, `jabberId`, `id_contact`, `lastconnexion`, `beforelastconnexion`, `background`, `lang`, `id_group`, `code_of_conduct`) VALUES (NULL, '-1', NULL, 'Admin', 'user', 'admin', MD5('admin'), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, '0', '0', NULL, NULL, '0', '0', '0', NULL, '1', '0', '0');

# Associate admin user and group
truncate dims_group_user;
INSERT INTO `dims_group_user` (`id_user`, `id_group`, `adminlevel`) VALUES ('1', '2', '99');

# Associate admin user and workspace
truncate dims_workspace_user;
INSERT INTO `dims_workspace_user` (`id_user`, `id_workspace`, `id_profile`, `adminlevel`, `activesearch`, `activeticket`, `activeprofil`, `activeannot`, `activecontact`, `activeproject`, `activeplanning`, `activenewsletter`, `activeevent`, `activeeventstep`, `activeeventemail`) VALUES ('1', '2', '0', '99', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0');

# Create modules
TRUNCATE TABLE `dims_module`;
INSERT INTO `dims_module` (`id`, `label`, `id_module_type`, `id_workspace`, `active`, `public`, `shared`, `herited`, `adminrestricted`, `viewmode`, `transverseview`, `autoconnect`, `sitemap`) VALUES (NULL, 'system', '1', NULL, '1', '0', '0', '0', '0', '1', '0', '0', NULL), (NULL, 'CMS', '2', '2', '1', '0', '0', '0', '0', '1', '0', '0', NULL);
INSERT INTO `dims_module` (`id`, `label`, `id_module_type`, `id_workspace`, `active`, `public`, `shared`, `herited`, `adminrestricted`, `viewmode`, `transverseview`, `autoconnect`, `sitemap`) VALUES (NULL, 'Docs', '3', '2', '1', '0', '0', '0', '0', '1', '0', '0', NULL), (NULL, 'Forms', '4', '2', '1', '0', '0', '0', '0', '1', '0', '0', NULL);

# module workspace
TRUNCATE dims_module_workspace;
INSERT INTO `dims_module_workspace` (`id_module`, `id_workspace`, `position`, `blockposition`, `visible`, `autoconnect`) VALUES ('2', '2', '1', 'left', '1', '0'), ('3', '2', '2', 'left', '1', '0');
INSERT INTO `dims_module_workspace` (`id_module`, `id_workspace`, `position`, `blockposition`, `visible`, `autoconnect`) VALUES ('4', '2', '3', 'left', '1', '0');

# Activate default WCE heading
INSERT INTO `dims_mod_wce_heading` (`id`, `label`, `description`, `template`, `id_heading`, `parents`, `depth`, `position`, `color`, `posx`, `posy`, `visible`, `linkedpage`, `url`, `url_window`, `free1`, `free2`, `id_module`, `id_user`, `id_workspace`, `timestp_modify`, `urlrewrite`, `fckeditor`, `picto`, `colour`, `linkedheading`, `id_lang`) VALUES (NULL, 'Root', NULL, NULL, '0', '0', '1', '1', NULL, '0', '0', '0', '0', NULL, '0', NULL, NULL, '2', '1', '2', '0', NULL, NULL, NULL, NULL, '0', '1');

