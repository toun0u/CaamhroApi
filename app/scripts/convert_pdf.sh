#/bin/bash
# installer wkhtmltopdf https://github.com/wkhtmltopdf/wkhtmltopdf
>>>>>>> dev/master
# on tente un pdf to texte pour extraire le texte si c'est un PDF texte
export PATH="$PATH:/usr/local/bin:/bin/:/opt/local/bin/:/usr/bin/"

#cd $1
#$4/wkhtmltopdf  --disable-pdf-compression --disable-smart-shrinking $2 $3
$4/wkhtmltopdf --disable-smart-shrinking $2 $3
