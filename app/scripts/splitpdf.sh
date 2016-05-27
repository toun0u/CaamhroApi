# Version du 08/06/2012
# On se depace dans la repertoire passe en 2eme parametre


if [ $# -lt 2 ] ; then
      echo need 2 args  
      exit 0
fi


if [ ! -d "$2" ]; then
	echo "Dossier non existant"
	exit 0
fi

cd $2

if [ ! -f "$1" ]; then
	echo "Fichier non existant"
	exit 0
fi


# On convertit le fichier passe en premier parmetre en PDF
gs -dBATCH -dNOPAUSE -sDEVICE=pdfwrite  -sOutputFile=convert_result.pdf $1
#gs -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dAutoRotatePages=/None -c "<</Orientation 0>> setpagedevice" -sOutputFile=convert_result.pdf $1
# Phase 1 extraction des images  
#    Cela va nous creer autant de fichiers images-XXX.ppm qu'ils trouve d'images (de pages) dans le pdf
pdfimages convert_result.pdf images
# ON supprime les pages blancjes
i=1
nbfichier=0
j=0
# on convertit en PDF chaque page
pdftk convert_result.pdf burst output images-%03d.pdf

for fichier in *.pbm
do
	 #convert -fuzz 10% -trim +repage $fichier trim.ppm
        convert -fuzz 10% -trim +repage $fichier res$i.png
	nbfichier=$(($nbfichier+1))
	taille_trim=$(du res$i.png | cut -f1)
	j=$(($i ))
	
	echo "Taille de $fichier est de $taille_trim"	
	if [ $taille_trim -lt 20 ]; then
		echo "Je supprime le fichier $fichier"	
		rm -rf $fichier

		if [ $j -le 9 ]; then
			rm -rf images-00$j.pdf
		else

			if [ $j -le 99 ]; then
                		rm -rf images-0$j.pdf
		        else
       			        rm -rf images-$j.pdf
			fi
		fi

		nbfichier=$(($nbfichier-1))

	fi

	rm -rf res$i.png 
         i=$((i+1))

done

if [ $nbfichier -ge 1 ] ; then
	echo "Je poursuis la conversion"

	# On passe les images en 300 DPI
	

	# On reconstruit le PDF snas les pages blanches
	gs -sDEVICE=pdfwrite  -dBATCH -dNOPAUSE -sOutputFile=trim_result.pdf images*.pdf
	rm -rf images*.pdf
	rm *.pbm
	rm *.ppm

	
	#pdfimages trim_result.pdf images
	pdftk trim_result.pdf burst output images-%03d.pdf

	# Phase 2 : on supprime le fond pour chacune des images et on la renomme en .pbm
	#for fichier in *.ppm; do unpaper --no-noisefilter --no-blurfilter --no-blackfilter --no-mask-scan --overwrite $fichier ${fichier/.ppm/.pbm}; done
	# Phase 3 on convertit chaque image en tif 8 bit niveau de gris
	for fichier in images*.pdf; do convert -density 300 $fichier -depth 8 -colorspace Gray -contrast -contrast -density 300 -scale 2000x3000 ${fichier/.pdf/.tif}; done
	page_courante=1
	debut=1
	fich=1

	for fichier in *.tif
	do 
		zbarimg -q $fichier > ${fichier/.tif/.txt}
		taille_result=$(wc -l "${fichier/.tif/.txt}" | cut -f1 -d " ")
		if [ $taille_result -gt 0 ]; then
			echo "un resultat page $page_courante"
		
			fin=$(($page_courante-1))
			if [ $page_courante -eq 1 ] ; then
				cat "${fichier/.tif/.txt}" > "result$fich.txt"
				tesseract $fichier ocr$fich
			else 
				if [ $debut -le $fin ] ; then
					echo "Debut : $debut Fin : $fin"
					gs -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dFirstPage=$debut -dLastPage=$fin -sOutputFile=result$fich.pdf trim_result.pdf
					fich=$(($fich+1))
					cat "${fichier/.tif/.txt}" > "result$fich.txt"
					tesseract $fichier ocr$fich
					debut=$(($page_courante))
				fi
			fi
		fi
		page_courante=$(($page_courante+1))
	done
	fin=$(($page_courante))
	if [ $debut -le $fin ] ; then
				gs -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -dFirstPage=$debut -dLastPage=$fin -sOutputFile=result$fich.pdf trim_result.pdf
	fi
else
	echo "Pas de fichier a traiter"
fi

# Phase 4 : on supprime le fond pour chacune des images et on la renomme en .pbm
rm *.ppm
rm *.pbm
rm *.tif
rm images*.pdf
rm doc_data.txt
rm images*.txt
rm trim_result.pdf
rm convert_result.pdf
	


