function deleteAssoc(article, activite)
{
	if(confirm("Etes-vous sûr de vouloir supprimer ce lien ?"))
	{
		dims_xmlhttprequest_todivpostargs('admin.php',
								  'action=deleteAssoc'
                                  +'&article='+article
                                  +'&activite='+activite,
								  '',
								  'div_assoc_'+article);
	}
}


function addAssoc(article)
{
	var select = document.getElementById('addact_'+article).value;
	if(select != 0)
	{
		dims_xmlhttprequest_todivpostargs('admin.php',
									  'action=addAssoc'
									  +'&article='+article
									  +'&activite='+select,
									  '',
									  'div_assoc_'+article);
	}
	else alert("Veuillez sélectionner une activité");
}


/*pour le planning*/
function changeViewMode(view){
  document.location.href = '?viewmode='+view;
}

function popupPrintableCreneau(id_creneau)
{
	dims_openwin('/admin.php?mainmenu=1&op=print_creneau&id='+id_creneau, 700, 600, 'Impression');
}

function reserveByCard(id) {
		dims_xmlhttprequest_todiv('admin.php', 'op=card_exist&session_id='+id+'&card_id='+$('#resa_carte').val(),'','message-resa');
}

function refreshResaList(session_id) {
		dims_xmlhttprequest_todiv('admin.php', 'op=refresh_resa_list&creneau_id='+session_id,'','table_reservation_enrg');
}

function DetachResa(resa_ref, resa_source_id, creneau_id) {
		var resultat = dims_xmlhttprequest('admin.php', 'op=detach_resa&resa_ref='+resa_ref+'&resa_source_id='+resa_source_id);
		if($('#message-resa').text() != ''){
			$('#message-resa').text('');
			$('#resa_carte').val('');
		}

		//gestion de la valeur de retour de l'annulation
			/*
			 * define('_DIMS_ELISATH_CANCEL_OK', 0); //l'annulation s'est bien déroulée
			   define('_DIMS_ELISATH_CANCEL_OUT_OF_TIME', 2);
			 */
		if(resultat==0)//OK
		{
			var message = '<span class="cancel_good">La réservation a bien été annulée</span>';

			message += dims_xmlhttprequest('admin.php', 'op=refreshCreneauElems&creneau='+creneau_id);
			$('#message-resa').html(message);
		}

		else if(resultat==2)//OUT_OF_TIME
		{
			$('#message-resa').html('<span class="cancel_not_good">Annulation impossible : délai dépassé</span>');
		}

		refreshResaList(creneau_id);


}

function addComment(){
	var elem =  '<h3>Commentaire complémentaire</h3>'+
				'<textarea name="activity_comment" id="creneau_comment"></textarea>';
	$('div.bloc_comment').html(elem);
}


function checkTime(idDate, idHeureDeb, idHeureFin, past) {
	// Si la session est passé on ne controle pas les dates
	if(past)
		return true;
	else {
		var error = false;
		// Objet du timestamp courant
		var currDate 	= new Date();

		// Objet des timestamp du creneau
		var dateDebCreneau = new Date();
		var dateEndCreneau = new Date();

		// Récupération des inputs en tableau
		var aDateCreneau = $("#"+idDate).val().split('/');
		var aTimeDebCreneau = $("#"+idHeureDeb).val().split(':');
		var aTimeEndCreneau = $("#"+idHeureFin).val().split(':');

		// Construction de l'objet de timestamp de début
		dateDebCreneau.setDate(aDateCreneau[0]);
		dateDebCreneau.setMonth((aDateCreneau[1]-1)); // les mois sont décalé : janvier = 0, frévrier = 1
		dateDebCreneau.setYear(aDateCreneau[2]);
		dateDebCreneau.setHours(aTimeDebCreneau[0]);
		dateDebCreneau.setMinutes(aTimeDebCreneau[1]);

		// Construction de l'objet de timestamp de fin
		dateEndCreneau.setDate(aDateCreneau[0]);
		dateEndCreneau.setMonth((aDateCreneau[1]-1)); // les mois sont décalé : janvier = 0, frévrier = 1
		dateEndCreneau.setYear(aDateCreneau[2]);
		dateEndCreneau.setHours(aTimeEndCreneau[0]);
		dateEndCreneau.setMinutes(aTimeEndCreneau[1]);

		// Comparaison date de début et date courante
		if(dateDebCreneau.getTime() < currDate.getTime()) {
			error = true;
			$('#def_'+idHeureDeb).text('L\'heure de début doit être supérieur à l\'heure courante.');
			$('#def_'+idHeureDeb).fadeIn();
		}

		// Comparaison date de fin et date de début
		if(dateEndCreneau.getTime() <= dateDebCreneau.getTime()) {
			error = true;
			$('#def_'+idHeureFin).text('L\'heure de fin doit être supérieur à l\'heure début.');
			$('#def_'+idHeureFin).fadeIn();
		}
		return !error;
	}
}

function openDetailCompte(id_compte) {
	id_popup = dims_openOverlayedPopup(600,500);
	dims_xmlhttprequest_todiv('admin.php', 'dims_op=infos_compte&id_compte='+id_compte+'&id_popup='+id_popup, '','p'+id_popup);
}

function openUrlInPopup(url) {
	id_popup = dims_openOverlayedPopup(600,500);
	dims_xmlhttprequest_todiv('admin.php', url+'&id_popup='+id_popup, '','p'+id_popup);
}

// javascript
// autocompletion
var value_suggested = '';
function suggest(element){
    //Détection du navigateur
    var is_ie = ((navigator.userAgent.toLowerCase().indexOf("msie") != -1) && (navigator.userAgent.toLowerCase().indexOf("opera") == -1));

	var hasInnerText = (document.getElementsByTagName("body")[0].innerText != undefined) ? true : false;

    /*Fonction utile : détermine la position absolue exacte d'un objet sur la page*/
    findPos = function(obj){
        var curleft = curtop = 0;
        if (obj.offsetParent) {
            curleft = obj.offsetLeft;
            curtop = obj.offsetTop;
            while (obj = obj.offsetParent) {
                curleft += obj.offsetLeft;
                curtop += obj.offsetTop;
            }
        }
        return [curleft,curtop];
    }

    //Création de la liste des propositions si elle n'existe pas encore
    if(!document.getElementById('suggestsList')){
        var suggestsList = document.createElement('ul');
        suggestsList.id = 'suggestsList';

        /*On donne à la liste la même largeur que le champ de texte => on doit récupérer sa largeur et son padding*/
        var style = (!is_ie ? window.getComputedStyle(element, null) : element.currentStyle); //Récupération du style
        if(style.width){
            var width = parseInt(style.width.replace(/px/, '')); //On transforme la largeur dans le style en int

            //On récupère le padding éventuel du champ pour le rajouter à la largeur à attribuer à la liste
            var paddingRight = (style.paddingRight ? style.paddingRight : false);
            if(paddingRight){
                paddingRight = parseInt(paddingRight.replace(/px/, ''));
                width += paddingRight;
            }

            var paddingLeft = (style.paddingLeft ? style.paddingLeft : false);
            if(paddingLeft){
                paddingLeft = parseInt(paddingLeft.replace(/px/, ''));
                width += paddingLeft;
            }

            width = (isNaN(width) ? 150 : width);
            suggestsList.style.width = width+'px'; //On donne à la liste la même largeur que celle du champ de texte
        }

        //On positionne la liste sous le champ
        suggestsList.style.position = 'absolute';
        var coord = findPos(element); //Récupération des coordonnées du champ
        suggestsList.style.left = coord[0]+'px';
        suggestsList.style.top = coord[1]+(19)+'px'; //On ajoute 19px de haut pour que la liste soit sur le champ et non par-dessus
        document.body.appendChild(suggestsList); //On insère la liste dans le document
    }
    else{
        //Si la liste existe déjà, on se contente de la repérer par rapport à son id
        suggestsList = document.getElementById('suggestsList');
    }

    //Si la valeur a changée, on masque la liste, le temps d'actualiser son contenu
    if(element.value != value_suggested){
        suggestsList.style.display = 'none';
    }

    //Fonction servant à cacher les suggestions
    closeSuggest = function(nofocus){
        var todelete = document.createElement('div');
        todelete.appendChild(suggestsList);
		value_suggested = '';
        if(!nofocus){element.focus();}
    };

    //Fonction gérant le parcour des éléments à l'aide des touches directionnelles
    selectSuggest = function(direction){
        //On regarde si un élément est selectionné
        var selected = -1;
        var lis = suggestsList.getElementsByTagName('li');
        for(i=0; i<lis.length; i++){
            if(lis[i].id == 'selectedSuggest'){
                selected = i;
            }
            lis[i].id = '';
        }

        selected += direction;
        selected = (selected < -1 ? (lis.length-1) : selected);
        if(selected >= 0 && selected < lis.length){
            lis[selected].id = 'selectedSuggest';
        }
    };

    //Remplit le champ avec la suggestion sélectionnée
    useSelected = function(){
        //On regarde si un élément est selectionné
        var lis = suggestsList.getElementsByTagName('li');
        for(i=0; i<lis.length; i++){
            if(lis[i].id == 'selectedSuggest'){
				if(!hasInnerText)
					element.value = lis[i].firstChild.textContent;
				else
					element.value = lis[i].firstChild.innerText;
            }
        }
        closeSuggest();
    };

    document.body.onkeyup = function(e){
        var key = (!is_ie ? e.keyCode : window.event.keyCode);
        switch(key){
            case 27: //Esc
                closeSuggest();
                break;
            case 9: //Tab
                closeSuggest(true); //On referme la liste sans redonner le focus au champ
                break;
            case 38: //Up
                selectSuggest(-1);
                break
            case 40: //Down
                selectSuggest(1);
                break;
            case 13: //Enter
                useSelected();
                break;
        }
    };
    document.body.onclick = function(){closeSuggest(true);};

    if(element.value != '' && element.value != value_suggested){
        dims_xmlhttprequest_todiv('admin.php','dims_op=search_abonne&keyword='+element.value,'','suggestsList');
    }
    else if(element.value == ''){
        //Si le champ est vide, on cache la liste
        closeSuggest();
    }
	suggestsList.style.display = 'block';
    value_suggested = element.value;
}
