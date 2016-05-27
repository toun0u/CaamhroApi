#!/bin/bash
#
# update.sh - Récupération du fichier SQL de synchro pour mise à jour des données
# 17 Mar 2014 : version 1.0
# 16 Jun 2015 : version 1.1 - Suppression du fichier de lock si le process ne tourne plus
# Author: Netlor SAS, Benjamin GANIVET <benjamin@netlor.fr>


# INIT
VERSION=1.1

# UID attendu pour lancer le script (correspond au user caahmro)
EXPECTED_UID=1002

# Racine du site
ROOT_DIR=/var/www/dims/caahmro

# Dossier de dépôt des données de l'ERP
INPUT_DIR=$ROOT_DIR/app/data/synchro/input

# Fichier de verrou upload ERP
UPLOAD_LOCK_FILE=$INPUT_DIR/lock_upload

# Fichier de verrou import des données
UPDATE_LOCK_FILE=$INPUT_DIR/lock_update

# Fichier de somme de controle MD5
MD5CHECKSUMS_FILE=$INPUT_DIR/md5checksums.txt

# Fichier SQL contenant le jeu de données
SQL_FILE=$INPUT_DIR/synchro.sql


# Connexion MySQL
DB_SERVER=localhost
DB_LOGIN=caahmro
DB_PASSWORD=KdXmuRmEH24LBB9X
DB_DATABASE=dims_caahmro


# Chemins des exécutables
MYSQL=/usr/bin/mysql
BUNZIP2=/bin/bunzip2
MD5=/usr/bin/md5sum


# Codes de retour
E_OK=0
E_BAD_USER=67
E_UPDATE_IN_PROGRESS=70
E_UPLOAD_IN_PROGRESS=71
E_X_MKDIR=80


# On vérifie qu'on est sur le bon utilisateur
if [ "$UID" -ne "$EXPECTED_UID" ]; then
	# echo "Vous n'êtes pas sur le bon utilisateur pour exécuter ce script."
	exit $E_BAD_USER
fi

# On vérifie qu'il n'y a pas déjà une synchro en cours d'import
if [ -f $UPDATE_LOCK_FILE ]; then
	# On regarde si le process tourne encore
	# Dans le cas contraire, on supprime le verrou
	still_running=false
	for pid in $(ps -e | awk '{print $1}'); do
		[ "$pid" = $(cat $UPDATE_LOCK_FILE) ] && {
			# echo "Le process $pid tourne toujours"
			still_running=true
		}
	done

	[ "$still_running" = true ] && {
		# echo "Mise à jour en cours..."
		exit $E_UPDATE_IN_PROGRESS
	}

	# Suppression du verrou si le process ne tourne plus
	# echo "Process mal terminé"
	rm -f $UPDATE_LOCK_FILE
fi

# On vérifie qu'il n'y a pas déjà une synchro en cours d'upload
if [ -f $UPLOAD_LOCK_FILE ]; then
	# echo "Upload en cours..."
	exit $E_UPLOAD_IN_PROGRESS
fi

# Création du verrou de synchro qui contient le pid du process courant
echo $$ > $UPDATE_LOCK_FILE
if [ $? -gt 0 ]; then
	# echo 'ERREUR lors de la création du verrou. Vérifiez les permissions.'
	exit $E_X_MKDIR
fi

if [ -f $MD5CHECKSUMS_FILE ]; then
	cd $INPUT_DIR
	$MD5 --quiet -c $MD5CHECKSUMS_FILE
	if [ $? -eq 0 ]; then
		$BUNZIP2 $SQL_FILE.bz2
		$MYSQL -h$DB_SERVER -u$DB_LOGIN -p$DB_PASSWORD $DB_DATABASE < $SQL_FILE
		mv -f $SQL_FILE backup/$( basename $SQL_FILE | sed "s/\.sql/_`date +%Y%m%d%H%I%S`.sql/")
		rm -f $MD5CHECKSUMS_FILE

		# Mise à jour des données
		php $ROOT_DIR/app/scripts/catalogue/import/update.php

		# Indexation des données
		php $ROOT_DIR/app/cron/cronindex.php
	fi
fi

# Suppression du verrou
rm -f $UPDATE_LOCK_FILE


exit $E_OK
