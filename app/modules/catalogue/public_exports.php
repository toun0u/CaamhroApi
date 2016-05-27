<?php
echo $skin->open_simplebloc("Module d'export de données","100%");

$action = dims_load_securvalue('exp_action', dims_const::_DIMS_CHAR_INPUT, true, true);
switch($action){
	case 'clients':
		if(file_exists(DIMS_APP_PATH."tmp_mysql/clients.csv")) unlink(DIMS_APP_PATH."tmp_mysql/clients.csv");
		$sql = "SELECT
					'ID',
					'Code client',
					'Civilite',
					'Nom',
					'Login',
					'Mot de passe',
					'Adresse 1',
					'Adresse 2',
					'Adresse 3',
					'Code postal',
					'Ville',
					'Telephone 1',
					'Telephone 2',
					'Fax',
					'Mobile',
					'Email',
					'Nom de livraison',
					'Adresse de livraison 1',
					'Adresse de livraison 2',
					'Adresse de livraison 3',
					'CP - Ville de livraison',
					'Code tarif 1',
					'Code tarif 2'

				UNION

				SELECT

					IFNULL(id_client,''),
					IFNULL(code_client,''),
					IFNULL(civilite,''),
					IFNULL(nom,''),
					IFNULL(login,''),
					IFNULL(librcha2,''),
					IFNULL(adr1,''),
					IFNULL(adr2,''),
					IFNULL(adr3,''),
					IFNULL(cp,''),
					IFNULL(ville,''),
					IFNULL(tel1,''),
					IFNULL(tel2,''),
					IFNULL(fax,''),
					IFNULL(port,''),
					IFNULL(email,''),
					IFNULL(liv_nom,''),
					IFNULL(liv_adr1,''),
					IFNULL(liv_adr2,''),
					IFNULL(liv_adr3,''),
					IFNULL(liv_cp_ville,''),
					IFNULL(code_tarif_1,''),
					IFNULL(code_tarif_2,'')

				INTO OUTFILE '".DIMS_APP_PATH."tmp_mysql/clients.csv' FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n'
				FROM dims_mod_cata_client";

		global $db;
		$db->query($sql);
		dims_downloadfile(DIMS_APP_PATH."tmp_mysql/clients.csv", 'clients.csv', true);
		dims_redirect($dims->getScriptEnv());
		break;
	case 'familles':
		if(file_exists(DIMS_APP_PATH."tmp_mysql/familles.csv")) unlink(DIMS_APP_PATH."tmp_mysql/familles.csv");
		$sql = "SELECT
					'ID',
					'Label',
					'ID PARENT',
					'Famille parent',
					'Parents',
					'Code'

				UNION

				SELECT

					IFNULL(f.id_famille,''),
					IFNULL(fl1.label,''),
					IFNULL(f.id_parent,''),
					IFNULL(fl2.label,''),
					REPLACE(IFNULL(f.parents,''), ';', '-'),
					IFNULL(f.code,'')


				INTO OUTFILE '".DIMS_APP_PATH."tmp_mysql/familles.csv' FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n'
				FROM dims_mod_cata_famille f
				INNER JOIN dims_mod_cata_famille_lang fl1 ON fl1.id_famille_1 = f.id_famille AND fl1.lang='french'
				LEFT JOIN dims_mod_cata_famille_lang fl2 ON fl2.id_famille_1 = f.id_parent AND fl2.lang='french'
				WHERE f.id_parent != 0";

		global $db;
		$db->query($sql);
		dims_downloadfile(DIMS_APP_PATH."tmp_mysql/familles.csv", 'familles.csv', true);
		dims_redirect($dims->getScriptEnv());
		ob_clean();
		die();
		break;

	case 'articles':
		ob_end_flush();
		if(file_exists(DIMS_APP_PATH."tmp_mysql/articles.csv")) unlink(DIMS_APP_PATH."tmp_mysql/articles.csv");
		$sql = "SELECT
					'ID',
					'Designation',
					'Ref.',
					'ID Famille',
					'Famille',
					'PU Tarif 1',
					'PU Tarif 2',
					'PU Tarif 3',
					'PU Tarif 4',
					'Prix TTC',
					'Code TVA',
					'Nom de fichier'

				UNION

				SELECT

					IFNULL(a.id_article, ''),
					IFNULL(al.label, ''),
					IFNULL(a.reference, ''),
					IFNULL(af.id_famille, ''),
					IFNULL(fl.label, ''),
					IFNULL(a.putarif_1, ''),
					IFNULL(a.putarif_2, ''),
					IFNULL(a.putarif_3, ''),
					IFNULL(a.putarif_4, ''),
					IFNULL(a.puttc, ''),
					IFNULL(a.ctva, ''),
					IFNULL(a.image, '')

				INTO OUTFILE '".DIMS_APP_PATH."tmp_mysql/articles.csv' FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n'
				FROM dims_mod_cata_article a
				INNER JOIN dims_mod_cata_article_lang al ON al.id_article_1 = a.id_article AND al.lang='french'
				INNER JOIN dims_mod_cata_article_famille af ON af.id_article = a.id_article
				INNER JOIN dims_mod_cata_famille_lang fl ON fl.id_famille_1 = af.id_famille AND fl.lang='french'";

		global $db;
		$db->query($sql);
		dims_downloadfile(DIMS_APP_PATH."tmp_mysql/articles.csv", 'articles.csv', true);
		dims_redirect($dims->getScriptEnv());
		die();
		break;
	case 'commandes':
		if(file_exists(DIMS_APP_PATH."tmp_mysql/commandes.csv")) unlink(DIMS_APP_PATH."tmp_mysql/commandes.csv");
		$sql = "SELECT
					'ID Commande',
					'Code client',
					'Nom client',
					'Date de creation',
					'Date de validation',
					'Etat',
					'Commentaire',
					'Hors catalogue',
					'Adresse 1',
					'Adresse 2',
					'Adresse 3',
					'Code postal',
					'Ville',
					'Email',
					'Nom du client pour livraison',
					'Adresse de livraison 1',
					'Adresse de livraison 2',
					'Adresse de livraison 3',
					'CP - Ville de livraison',
					'Frais de port',
					'Taux TVA Ports',
					'Total HT',
					'Total TVA',
					'Total TTC',
					'Mode paiement',
					'Date de generation',
					'Validateur',
					'Refuse par',
					'Motif de refus',


					'Ref. article',
					'Designation',
					'Qte.',
					'PU Brut',
					'Remise',
					'PU HT',
					'Taux TVA',
					'PU TTC'

			UNION

				SELECT

					c.id_cde,
					IFNULL(c.code_client, ''),
					IFNULL(c.cli_nom, ''),
					IFNULL(c.date_cree, ''),
					IFNULL(c.date_validation, ''),
					IFNULL(c.etat, ''),
					IFNULL(c.commentaire, ''),
					IFNULL(c.hors_cata, ''),
					IFNULL(c.cli_adr1, ''),
					IFNULL(c.cli_adr2, ''),
					IFNULL(c.cli_adr3, ''),
					IFNULL(c.cli_cp, ''),
					IFNULL(c.cli_ville, ''),
					IFNULL(c.cli_email, ''),
					IFNULL(c.cli_liv_nom, ''),
					IFNULL(c.cli_liv_adr1, ''),
					IFNULL(c.cli_liv_adr2, ''),
					IFNULL(c.cli_liv_adr3, ''),
					IFNULL(c.cli_liv_cp_ville, ''),
					IFNULL(c.port, ''),
					IFNULL(c.port_tx_tva, ''),
					IFNULL(c.total_ht, ''),
					IFNULL(c.total_tva, ''),
					IFNULL(c.total_ttc, ''),
					IFNULL(c.mode_paiement, ''),
					IFNULL(c.date_gen, ''),
					IFNULL(c.validation_user_name, ''),
					IFNULL(c.refus_user_name, ''),
					IFNULL(c.refus_motif, ''),

					IFNULL(l.reference, ''),
					IFNULL(l.designation, ''),
					IFNULL(l.qte, ''),
					0,
					0,
					IFNULL(l.pu, ''),
					0,
					0

				FROM dims_mod_cata_cde c
				INNER JOIN dims_mod_cata_cde_lignes_hc l ON l.id_cde = c.id_cde
				WHERE c.hors_cata = 1

			UNION

				SELECT

					c.id_cde,
					IFNULL(c.code_client, ''),
					IFNULL(c.cli_nom, ''),
					IFNULL(c.date_cree, ''),
					IFNULL(c.date_validation, ''),
					IFNULL(c.etat, ''),
					IFNULL(c.commentaire, ''),
					IFNULL(c.hors_cata, ''),
					IFNULL(c.cli_adr1, ''),
					IFNULL(c.cli_adr2, ''),
					IFNULL(c.cli_adr3, ''),
					IFNULL(c.cli_cp, ''),
					IFNULL(c.cli_ville, ''),
					IFNULL(c.cli_email, ''),
					IFNULL(c.cli_liv_nom, ''),
					IFNULL(c.cli_liv_adr1, ''),
					IFNULL(c.cli_liv_adr2, ''),
					IFNULL(c.cli_liv_adr3, ''),
					IFNULL(c.cli_liv_cp_ville, ''),
					IFNULL(c.port, ''),
					IFNULL(c.port_tx_tva, ''),
					IFNULL(c.total_ht, ''),
					IFNULL(c.total_tva, ''),
					IFNULL(c.total_ttc, ''),
					IFNULL(c.mode_paiement, ''),
					IFNULL(c.date_gen, ''),
					IFNULL(c.validation_user_name, ''),
					IFNULL(c.refus_user_name, ''),
					IFNULL(c.refus_motif, ''),

					IFNULL(l.ref, ''),
					IFNULL(al.label, ''),
					IFNULL(l.qte, ''),
					IFNULL(l.pu_brut, ''),
					IFNULL(l.remise, ''),
					IFNULL(l.pu_ht, ''),
					IFNULL(l.tx_tva, ''),
					IFNULL(l.pu_ttc, '')


				INTO OUTFILE '".DIMS_APP_PATH."tmp_mysql/commandes.csv' FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n'
				FROM dims_mod_cata_cde c
				INNER JOIN dims_mod_cata_cde_lignes l ON l.id_cde = c.id_cde
				INNER JOIN dims_mod_cata_article_lang al ON al.id_article_1 = l.id_article
				WHERE c.hors_cata = 0";

		global $db;
		$db->query($sql);
		dims_downloadfile(DIMS_APP_PATH."tmp_mysql/commandes.csv", 'commandes.csv', true);
		dims_redirect($dims->getScriptEnv());
		break;
}
?>
<div class="export_container">
	<table class="export_table" width="100%">
		<tr bgcolor="<?php echo $skin->values['colsec']; ?>">
			<th style="text-align: left;">Type d'export</th>
			<!-- <th width="20px">CSV</th> -->
		</tr>
		<tr bgcolor="<?php echo $skin->values['bgline1']; ?>">
			<td>Liste des clients</td>
<!-- 			<td>
				<a href="<?php echo $dims->getScriptEnv();?>?part=exports&exp_action=clients" title="Télécharger la liste des clients">
					<img src="./common/modules/catalogue/img/download_csv.png" alt="Télécharger la liste des clients"/>
				</a>
			</td>
 -->		</tr>
		<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
			<td>Liste des familles d'articles</td>
<!-- 			<td>
				<a href="<?php echo $dims->getScriptEnv();?>?part=exports&exp_action=familles" title="Télécharger la liste des familles d'articles">
					<img src="./common/modules/catalogue/img/download_csv.png" alt="Télécharger la liste des familles d'articles"/>
				</a>
			</td>
 -->		</tr>
		<tr bgcolor="<?php echo $skin->values['bgline1']; ?>">
			<td>Liste des articles</td>
<!-- 			<td>
				<a href="<?php echo $dims->getScriptEnv();?>?part=exports&exp_action=articles" title="Télécharger la liste des articles">
					<img src="./common/modules/catalogue/img/download_csv.png" alt="Télécharger la liste des articles"/>
				</a>
			</td>
 -->		</tr>
		<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
			<td>Liste des commandes</td>
<!-- 			<td>
				<a href="<?php echo $dims->getScriptEnv();?>?part=exports&exp_action=commandes" title="Télécharger la liste des commandes">
					<img src="./common/modules/catalogue/img/download_csv.png" alt="Télécharger la liste des commandes"/>
				</a>
			</td>
 -->		</tr>
	</table>
</div>
<?php
echo $skin->close_simplebloc();
?>
