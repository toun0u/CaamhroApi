#/bin/bash

# $1 : path du répertoire contenant le pdf
# $2 : id du pdf
# $3 : version du pdf
# format du pdf : $2_$3.pdf
# format de l'index général : $2_$3_index.txt
# répertoire des previews : $1/preview_$2_$3/
# format previews images : $2_$3_p<page>.png
# format des index par page : $2_$3_index_<page>.txt

# ajouter des tests pour vérifier que tous les paramètres sont bien passés ?

# on tente un pdf to texte pour extraire le texte si c'est un PDF texte
cd $1

pdftotext -nopgbrk ./$2_$3.pdf ./$2_$3_index.txt

# On recupere le nombre de lignes du fichier resultat
taille_result=$(wc -l "./$2_$3_index.txt" | cut -f1 -d " ")

# Si ce nombre de ligne est inferieur à zero on tente une extraction des images avec OCR
if [ $taille_result -le 0 ]; then
	# Phase 1 extraction des images
	#    Cela va nous créer autant de fichiers images-XXX.ppm qu'ils trouve d'images (de pages) dans le pdf
	pdfimages ./$2_$3.pdf images
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
	mv $1/resultat.txtcat $1/$2_$3_index.txt
fi
#On affiche le resultat en nettoyant tous les caractères genants (on les remplace par un espace
cat $1/$2_$3_index.txt | sed -e "s/[\`,»,\',\",§,\<,\>]/\ /g"
#rm $1/resultat_extraction_pdf.txt

# création des preview
if [ -d $1/preview_$2_$3 ];then
else
	mkdir $1/preview_$2_$3/
fi
cd $1/preview_$2_$3/

nb_pages=$(pdfinfo "$1/$2_$3.pdf" | grep "Pages:" | sed "s/ //g" | cut -d: -f2)
for page in `seq 1 $nb_pages` ;
do
	# On tente un pdf to texte pour extraire le texte si c'esdt un PDF texte
	pdftotext -nopgbrk -f $page -l $page $1/$2_$3.pdf $1/preview_$2_$3/$2_$3_index_p$page.txt

	# on récupére le nombre de ligne du fichier extrait
	taille_page=$(wc -l "$1/preview_$2_$3/$2_$3_index_p$page.txt" | cut -f1 -d " ")

	# si le nombre de ligne est inférieur à 0 on tente une extraction des images avec OCR
	if [ $taille_page -le 0 ]; then
		# Phase 1 : extraction des images
		pdfimages -f $page -l $page $1/$2_$3.pdf images
		# Phase 2 : on supprime le fond pour chacune des images et on la renomme en .pbm
		for fichier in ./*.ppm; do unpaper --overwrite -q $fichier ${fichier/.ppm/.pbm}; done
		# Phase 3 : on convertit chaque image en tif 8 bit niveau de gris
		for fichier in ./*.pbm; do convert $fichier -density 300 -depth 8 -colorspace Gray ${fichier/.pbm/.tif}; done
		# Phase 4 : on tente une OCR
		for fichier in ./*.tif; do tesseract $fichier ${fichier/.tif/.txt} 2>> ./trace.log; done
		# On remet ensemble l'OCR de chaque image
		cat ./images-*.txt > ./tmp.txtcat
		# On efface les fichiers intermédiaire
		rm -rf ./*.ppm
		rm -rf ./*.pbm
		rm -rf ./*.tif
		rm -rf ./*.log
		rm -rf ./$2_$3_index_p$page.txt
		rm -rf ./images-*.txt
		# on renomme le résultat final
		mv ./tmp.txtcat ./$2_$3_index_p$page.txt
	fi
# On nettoye le résultat en enlevant tous les caractères génants
cat $1/preview_$2_$3/$2_$3_index_p$page.txt | sed -e "s/[\`,»,\',\",§,\<,\>]/\ /g"

# on génére les images permettant la preview
convert $1/$2_$3.pdf[$((page - 1))] -colorspace RGB ./$2_$3_p$page.png

done

