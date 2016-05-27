# Petit shell qui decoupe un rectangle  d'une imzge et fait un ocr sur le resultat
#
# Parametres
# $1 nom du fichier
# $2 abscisse origine (x) de la decoupe
# $3 ordonee origine (y) de la decoupe
# $4 largeur de la decoupe
# $5 hauteur de la decoupe
rm image.tif
rm texte.txt
convert $1  -unsharp 0x0.5 -crop $4x$5+$2+$3 +repage -background white -flatten +matte -density 300 -depth 4 -colorspace Gray -sigmoidal-contrast 5,0% image.tif
#convert $1   -crop $4x$5+$2+$3 -background white image.tif
# on tente une OCR (enfin !!!!)
tesseract -l fra image.tif texte


