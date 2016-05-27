#!/bin/bash
#
# get-documents.sh - Récupération des commandes sur le serveur web via sftp
# 14 Nov 2014 : version 1.0
# 03 Feb 2015 : version 1.1 - Fix unable to find backup folder when launched from other directory
# 15 Jun 2015 : version 1.2 - Handle error while loading the SQL file in Mysql
# Author: Netlor SAS, Benjamin Ganivet <benjamin@netlor.fr>


# INIT
VERSION=1.2

EXPECTED_UID=1000

MD5=/usr/bin/md5sum
SFTP=/usr/bin/sftp
MYSQL=/usr/bin/mysql

MYSQL_USER=caahmro
MYSQL_PASSWD=tot9OoPo
DATABASE=synchro

INPUT_DIR=/home/admindims/synchro
DOWNLOAD_DIR=download
BATCH_FILE_GET_DOCS=batchfile_get_documents
BATCH_FILE_BACKUP_DOCS=batchfile_backup_documents

# Liste des fichiers à archiver sur le serveur web
FILES_TO_BACKUP=()

# Messages d'erreur à renvoyer par mail
ERROR_MESSAGES=""

# Adresses email sur lesquels envoyer les erreurs
ERROR_MAILS=benjamin@netlor.fr

E_BAD_USER=67
E_BAD_ARGUMENTS=68


CURRENT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $CURRENT_PATH


# Récupération des fichiers sur le serveur SFTP
get_documents() {
	echo "cd $DOWNLOAD_DIR
	mget *.sql
	mget *.sql.md5
	quit" > $BATCH_FILE_GET_DOCS

	$SFTP -b $BATCH_FILE_GET_DOCS gaia

	rm -f $BATCH_FILE_GET_DOCS
}

# Vérifie l'intégrité du fichier en fonction de son checksum md5
check_integrity() {
	document=$1

	[ -f ${document}.md5 ] || ERROR_MESSAGES+=$(date +%d/%m/%Y-%R:%S)" : $document - pas de checksum\n"

	if [ -f ${document}.md5 ]; then
		if [ "$($MD5 $document | awk '{print $1}')" = "$(cat ${document}.md5)" ]; then
			insert_document $document
		else
			ERROR_MESSAGES+=$(date +%d/%m/%Y-%R:%S)" : $document - checksum invalide\n"
		fi
	fi
}

insert_document() {
	file=$1

	# Insertion dans la base
	$MYSQL -u$MYSQL_USER -p$MYSQL_PASSWD $DATABASE < $file

	response=$?
	if [ $response -eq 0 ]; then
		# Archivage du fichier en local et sur le serveur web
		mv -f $file backup/
		rm -f ${file}.md5
		FILES_TO_BACKUP[${#FILES_TO_BACKUP[@]}]=$file
	else
		ERROR_MESSAGES+=$(date +%d/%m/%Y-%R:%S)" : $document - Erreur à l'insertion dans la base MySQL (${response})\n"
	fi
}

# Backup des documents sur le serveur web
backup_documents() {
	echo "cd $DOWNLOAD_DIR" > $BATCH_FILE_BACKUP_DOCS
	for file in "${FILES_TO_BACKUP[@]}"; do
		echo "rename $file backup/$file
			rm ${file}.md5" >> $BATCH_FILE_BACKUP_DOCS
	done
	echo "quit" >> $BATCH_FILE_BACKUP_DOCS

	$SFTP -b $BATCH_FILE_BACKUP_DOCS gaia

	rm -f $BATCH_FILE_BACKUP_DOCS
}

send_errors() {
	echo -e $ERROR_MESSAGES | mail -s "CAAHMRO / Erreur de synchro" $ERROR_MAILS
}

main() {
	# Récupération des fichiers
	get_documents

	# Vérification de l'intégrité des fichiers
	if [ `ls *.sql | wc -l` -gt 0 ]; then
		for document in *.sql; do
			check_integrity $document
		done
	fi

	# Pour archivage les fichiers sur le serveur web
	[ ${#FILES_TO_BACKUP[@]} -gt 0 ] && backup_documents

	# Envoi des erreurs par mail
	[ -n "$ERROR_MESSAGES" ] && send_errors
}

if [ "$UID" -ne "$EXPECTED_UID" ]; then
	echo "Vous n'êtes pas sur le bon utilisateur pour exécuter ce script."
	exit $E_BAD_USER
fi

main

exit 0
