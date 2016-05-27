#/bin/bash
# on tente un pdf to texte pour extraire le texte si c'est un PDF texte
cd $1

> resultat_extraction_pdf.txt
pdftotext -nopgbrk $2 resultat_extraction_pdf.txt

# On recupere le nombre de lignes du fichier resultat
taille_result=$(wc -l "resultat_extraction_pdf.txt" | cut -f1 -d " ")

# Si ce nombre de ligne est uninferieur à zero on tente une extraction des images avec OCR
if [ $taille_result -le 0 ]; then
	# Phase 1 extraction des images
	#    Cela va nous créer autant de fichiers images-XXX.ppm qu'ils trouve d'images (de pages) dans le pdf
	pdfimages $2 images
	# Phase 2 : on supprime le fond pour chacune des images et on la renomme en .pbm
	for fichier in ./*.ppm; do unpaper --overwrite -q $fichier ${fichier/.ppm/.pbm};	done
	# Phase 3 on convertit chaque image en tif 8 bit niveau de gris
	for fichier in ./*.pbm; do convert $fichier -density 300 -depth 8 -colorspace Gray ${fichier/.pbm/.tif}; done
	# Phase 4 on tente une OCR (enfin !!!!)
	for fichier in ./*.tif; do tesseract $fichier ${fichier/.tif/.txt} 2>> ./trace.log; done
	# On remet ensemble l'OCR de chaque image
	cat $1/*.txt > ./resultat.txtcat
	# On efface les fichiers intermediaires
	rm -rf $1/*.ppm
	rm -rf $1/*.pbm
	rm -rf $1/*.tif
	rm -rf $1/*.txt
	rm -rf $1/*.log
	#On renomme le resultat final
	mv $1/resultat.txtcat $1/resultat_extraction_pdf.txt
fi
#On affiche le resultat en nettoyant tous les caractères genants (on les remplace par un espace
cat $1/resultat_extraction_pdf.txt | sed -e s/[\`,»,\',\",§,\<,\>]/\ /g
#rm $1/resultat_extraction_pdf.txt

