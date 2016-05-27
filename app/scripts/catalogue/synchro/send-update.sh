#!/bin/bash
#
# send-update.sh - Envoi des données sur le serveur web via sftp
# 28 Feb 2014 : version 1.0
# 19 Mar 2014 : version 1.1 - Add upload lockfile
# 31 Jul 2014 : version 1.2 - Add partial export
# 01 Aug 2014 : version 1.3 - Add tariff export
# 07 Aug 2014 : version 1.4 - Add client export
# 03 Feb 2015 : version 1.5 - Add set_param method and set param SYCNHRO_FULL = 1 in full export
# 19 May 2015 : version 1.6 - Update partial export for daily updates
# 19 May 2015 : version 1.7 - Add lock file while export to prevent multiple instances in a while
# 19 May 2015 : version 1.8 - FIX Add IND_DIMS_DICO_CDT_PAIE necessary for documents update
# Author: Netlor SAS, Benjamin Ganivet <benjamin@netlor.fr>


# INIT
VERSION=1.8

EXPECTED_UID=1000

DUMP=/usr/bin/mysqldump
BZIP2=/bin/bzip2
MD5=/usr/bin/md5sum
SFTP=/usr/bin/sftp
MYSQL=/usr/bin/mysql

MYSQL_USER=caahmro
MYSQL_PASSWD=tot9OoPo
DATABASE=synchro

INPUT_DIR=/home/admindims/synchro
BATCH_FILE=batchfile
EXPORT_LOCK_FILE=$INPUT_DIR/lock_export
UPLOAD_LOCK_FILE=lock_upload
UPLOAD_DIR=upload
CHECKSUMS_FILE=md5checksums.txt

E_BAD_USER=67
E_BAD_ARGUMENTS=68


usage() {
	cat <<EOF
Usage: $0 [OPTIONS]
OPTIONS:
-h | --help : Show this help screen
-f | --full : Export all database
-p | --partial : Export minimal tables for partial synchro
-s | --stock : Export only stocks
-t | --tarif : Export only tariff
-c | --clients : Export only clients and contacts
EOF
}

export_all() {
	# Export de la base de données
	$DUMP -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE | $BZIP2 > $DATABASE.sql.bz2
	make_checksum
	upload
	set_param SYNCHRO_FULL 0
}

export_partial() {
	# Export de la base de données (tout sauf les clients, produits et documents)
	$DUMP -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE \
		IND_DIMS_ADD_FAC \
		IND_DIMS_ADD_LIV \
		IND_DIMS_CATEGORIES \
		IND_DIMS_CLIENT \
		IND_DIMS_CONTACT \
		IND_DIMS_CRE_ADD_LIV \
		IND_DIMS_DESCRIPTIONS_PRODUITS \
		IND_DIMS_DICO_CDT_PAIE \
		IND_DIMS_DICO_MODE_EXP \
		IND_DIMS_DICO_UNIT \
		IND_DIMS_DOC_CREA_ENTETE \
		IND_DIMS_DOC_CREA_PIED \
		IND_DIMS_DOC_CREA_POSITION \
		IND_DIMS_DOC_ENTETE \
		IND_DIMS_DOC_MAJ_ENTETE \
		IND_DIMS_DOC_MAJ_PIED \
		IND_DIMS_DOC_MAJ_POSITION \
		IND_DIMS_DOC_PIED \
		IND_DIMS_DOC_PIED_TAXE \
		IND_DIMS_DOC_POSITION \
		IND_DIMS_GABARIT \
		IND_DIMS_PHYTO \
		IND_DIMS_PRODUIT \
		IND_DIMS_PRODUITS_TARIFS \
		IND_DIMS_RECAP_TVA_DOC \
		IND_DIMS_STATUS \
		IND_DIMS_STOCK \
		IND_DIMS_UTILISATEURS \
		PARAM \
		| $BZIP2 > $DATABASE.sql.bz2
	make_checksum
	upload
}

export_stock() {
	# Export de la base de données
	$DUMP -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE IND_DIMS_STOCK | $BZIP2 > $DATABASE.sql.bz2
	make_checksum
	upload
}

export_tarif() {
	# Export de la base de données
	$DUMP -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE IND_DIMS_PRODUITS_TARIFS | $BZIP2 > $DATABASE.sql.bz2
	make_checksum
	upload
}

export_clients() {
	# Export de la base de données
	$DUMP -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE IND_DIMS_CLIENT IND_DIMS_CONTACT IND_DIMS_ADD_FAC IND_DIMS_ADD_LIV | $BZIP2 > $DATABASE.sql.bz2
	make_checksum
	upload
}

make_checksum() {
	# Création du fichier de contrôle
	$MD5 $DATABASE.sql.bz2 > $CHECKSUMS_FILE
}

upload() {
	touch $UPLOAD_LOCK_FILE

	# Envoi sur le serveur
	echo "cd $UPLOAD_DIR
	mput $UPLOAD_LOCK_FILE
	mput $DATABASE.sql.bz2
	mput $CHECKSUMS_FILE
	rm $UPLOAD_LOCK_FILE
	quit" > $BATCH_FILE

	$SFTP -b $BATCH_FILE gaia

	# Suppression des fichiers
	rm -f $DATABASE.sql.bz2 $CHECKSUMS_FILE $BATCH_FILE $UPLOAD_LOCK_FILE
}

set_param() {
	PARAM_NAME=$1
	PARAM_VALUE=$2
	$MYSQL -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE -e "UPDATE \`PARAM\` SET \`$PARAM_NAME\` = '$PARAM_VALUE' ;"
}


if [ "$UID" -ne "$EXPECTED_UID" ]; then
	echo "Vous n'êtes pas sur le bon utilisateur pour exécuter ce script."
	exit $E_BAD_USER
fi

if [ ! -f $EXPORT_LOCK_FILE ]; then
	touch $EXPORT_LOCK_FILE

	while [ $1 ]; do
		case $1 in
			'-h' | '--help')
				usage
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
			'-f' | '--full')
				cd $INPUT_DIR
				export_all
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
			'-p' | '--partial')
				cd $INPUT_DIR
				export_partial
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
			'-s' | '--stock')
				cd $INPUT_DIR
				export_stock
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
			'-t' | '--tarif')
				cd $INPUT_DIR
				export_tarif
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
			'-c' | '--clients')
				cd $INPUT_DIR
				export_clients
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
			*)
				usage
				rm -f $EXPORT_LOCK_FILE
				exit 0
				;;
		esac
		shift
	done
fi


if [ $# -ne 1 ]; then
	usage
	exit $E_BAD_ARGUMENTS
fi

# rm -f $EXPORT_LOCK_FILE

exit 0
