function doc_gallery_validate(form)
{
	ok = true;

	if (!dims_validatefield('Nom de la gallerie', form.docgallery_name, 'string'))
		ok = false;
	if (!dims_validatefield('Largeur des miniatures', form.docgallery_small_width, 'int'))
		ok = false;
	if (!dims_validatefield('Hauteur des miniatures', form.docgallery_small_height, 'int'))
		ok = false;
	if (!dims_validatefield('Largeur des images', form.docgallery_big_width, 'int'))
		ok = false;
	if (!dims_validatefield('Hauteur des images', form.docgallery_big_height, 'int'))
		ok = false;
	if (!dims_validatefield('Nombre de ligne', form.docgallery_nb_row, 'int'))
		ok = false;
	if (!dims_validatefield('Nombre de colonne', form.docgallery_nb_column, 'int'))
		ok = false;

	if ((form.docgallery_show_picture.value=="no") && (form.docgallery_show_textfile.value=="no") && (form.docgallery_show_compressfile.value=="no")) {
		alert('Selectionner au moins une cat�gorie � afficher.')
		ok = false;
	}

	if (((form.docgallery_small_width.value<=0) || (form.docgallery_small_width.value>100)) && (form.docgallery_s_w_format.value=="%"))  {
		alert('La valeur du pourcentage du champ \'Largeur des miniatures\' n\'est pas correct.')
		ok = false;
	}

	if (((form.docgallery_small_height.value<=0) || (form.docgallery_small_height.value>100)) && (form.docgallery_s_h_format.value=="%"))  {
		alert('La valeur du pourcentage du champ \'Hauteur des miniatures\' n\'est pas correct.')
		ok = false;
	}

	if (((form.docgallery_big_width.value<=0) || (form.docgallery_big_width.value>100)) && (form.docgallery_b_w_format.value=="%"))  {
		alert('La valeur du pourcentage du champ \'Largeur des images\' n\'est pas correct.')
		ok = false;
	}

	if (((form.docgallery_big_height.value<=0) || (form.docgallery_big_height.value>100)) && (form.docgallery_b_h_format.value=="%"))  {
		alert('La valeur du pourcentage du champ \'Hauteur des images\' n\'est pas correct.')
		ok = false;
	}

	return ok;
}

function doc_folder_validate(form, tovalidate)
{
	next = false;

	if (dims_validatefield('Nom du Dossier', form.docfolder_name, 'string'))
	if (tovalidate)
	{
		next = confirm('Cette action va envoyer\nune demande de publication\naux validateurs de ce dossier\n\n�tes-vous certain de vouloir continuer ?');
	}
	else next = true;

	if (next) return true;

	return false;
}

function doc_file_validate(form, newfile, tovalidate)
{
	next = false;

	if (!newfile || (newfile && dims_validatefield('Fichier', form.docfile_file, 'string')))
	if (tovalidate)
	{
		next = confirm('Cette action va envoyer\nune demande de publication\naux validateurs de ce dossier\n\n�tes-vous certain de vouloir continuer ?');
	}
	else next = true;

	if (next) bUploaded.start('fileprogress');

	return false;
}


function checkAllFiles(nbfiles) {
	for (i = 0; i < nbfiles; i++)
		document.getElementById("seldoc"+i).checked = true;
}

function uncheckAllFiles(nbfiles) {
	for (i = 0; i < nbfiles; i++)
		document.getElementById("seldoc"+i).checked = false;
}

function checkAllFolders(nbfolders) {
	for (i = 0; i < nbfolders; i++)
		document.getElementById("selfolder"+i).checked = true;
}

function uncheckAllFolders(nbfolders) {
	for (i = 0; i < nbfolders; i++)
		document.getElementById("selfolder"+i).checked = false;
}

function validCommand(event,currentfolder,nbfiles,nbfolders) {
	var cpte=0;
	var selectfolders="";

	for (i = 0; i < nbfiles; i++) {
		if (document.getElementById("seldoc"+i).checked) cpte++;
	}
	for (i = 0; i < nbfolders; i++) {
		if (document.getElementById("selfolder"+i).checked) {
			cpte++;
			if (selectfolders=="") selectfolders=document.getElementById("selfolder"+i).value;
			else selectfolders+=","+document.getElementById("selfolder"+i).value;
		}
	}

	if (cpte==0) {
		document.getElementById('op').selectedIndex=0;
	}
	else {
		var elem=document.getElementById('op').selectedIndex;
		if (elem>0) {
			switch(elem) {
				case 1:
					displayDocChoice(event,currentfolder,selectfolders);
				break;

				case 2:
					if (confirm('OK ?')) {
						document.listdoc.submit();
					}
				break;
			}
		}
	}
}

function displayDocChoice(event,currentfolder,selectfolders) {
	//dims_showpopup('',400,event,'click');
	dims_showcenteredpopup("",650,400,'dims_popup');
	//dims_showpopup('',400,event,'click','dims_popup',0,0);
	dims_xmlhttprequest_todiv('admin.php','dims_op=choice_docfolder&currentfolder='+currentfolder+"&selectfolders="+selectfolders,'','dims_popup');
}


function doc_showfolder(hid,str) {
		elt = document.getElementById(hid+'_plus');
		if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
		else  if (elt.innerHTML.indexOf('minusbottom')  != -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
		else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
		else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');


		if (elt = document.getElementById(hid)) {
			if (elt.style.display == 'none')
			{
				if (elt.innerHTML.length < 20) dims_xmlhttprequest_todiv('/admin.php','op=xml_detail_folder&hid='+hid+'&str='+str,'',hid);
				document.getElementById(hid).style.display='block';
			}
			else
			{
				document.getElementById(hid).style.display='none';
			}
		}
}
