#!/bin/bash
#
# Script de redimentionnement des photos

E_XCD=86			# Can't change directory
E_XCRD=87			# Can't create directory

SITEPATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../../../.." && pwd )"
SRCDIR=$SITEPATH/photos
BACKUPDIR=$SITEPATH/photos/backup

CONVERT=`which convert`
TS_NOW=`date +%s`


rename_photos() {
	subfolder=$1

	# Renommage des fichiers mal formés
	for photo in `find  $SRCDIR/$subfolder/ -name "*.JPG"`; do
		mv $SRCDIR/$subfolder/$(basename $photo) `echo  $SRCDIR/$subfolder/$(basename $photo) | sed s/JPG/jpg/` ;
	done
	for photo in `find  $SRCDIR/$subfolder/ -name "*.jpg.jpg"`; do
		mv $SRCDIR/$subfolder/$(basename $photo) `echo  $SRCDIR/$subfolder/$(basename $photo) | sed 's/\.jpg\.jpg/\.jpg/'` ;
	done
}

redim_photos() {
	subfolder=$1
	dimensions=$2

	for photo in $(ls -1 $SRCDIR/$subfolder/) ; do

		# On ne traite que les photos qui ont plus de 5 minutes (300s)
		# pour être sûr que son transfert est terminé
		TS_FILE=`date +%s -r $SRCDIR/$subfolder/$(basename $photo)`
		if [ $(($TS_NOW-$TS_FILE)) -gt 0 ]; then
			# echo "Traitement de `basename $photo`";
			$CONVERT "$SRCDIR/$subfolder/$(basename $photo)" -thumbnail "$dimensions" -density 72 -colorspace RGB -background "#FFFFFF" -gravity center -extent "$dimensions" "$SITEPATH/photos/$dimensions/$(basename $photo)"
		fi
	done
}

backup_photos() {
	subfolder=$1

	[ -d $BACKUPDIR/$subfolder ] || mkdir $BACKUPDIR/$subfolder
	[ -d $BACKUPDIR/$subfolder ] || exit E_XCRD

	for photo in $(ls -1 $SRCDIR/$subfolder/) ; do
		mv -f "$SRCDIR/$subfolder/$(basename $photo)" "$BACKUPDIR/$subfolder/$(basename $photo)"
	done
}


if [ -d $SITEPATH ] && [ -d $SRCDIR ]; then

	rename_photos originals
	rename_photos watermarked

	redim_photos originals 50x50
	redim_photos originals 100x100
	redim_photos watermarked 300x300

	backup_photos originals
	backup_photos watermarked

	echo "Traitement termine." && echo
else
	echo "Le dossier n'existe pas. Verifiez les parametres du script !" && echo
	exit 1
fi

exit 0
