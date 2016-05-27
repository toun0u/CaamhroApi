/****************************************************
*****************************************************
*** @author 	Arnaud KNOBLOCH [NETLOR CONCEPT]  ***
*** @version  	1.0                               ***
*** @package  	projects                          ***
*** @access  	public                            ***
*** @licence	GPL                               ***
*****************************************************
*****************************************************/


/********* GESTION DU DRAG AND DROP *********/

/* Déclaration des variables utilisées pour le drag n drop */
var isDragging = false;
var objectToDrag;
var obj;
var ecartX;
var ecartY;
var curX;
var curY;
var minX;
var maxX;

/* Permet de possitionner une div en javascript
  Déterminine aussi la zone minimale et maximale du drag pour le visualisateur */

function positionne(p_id, p_pos_X, p_pos_Y, limit){
	document.getElementById(p_id).style.left = p_pos_X+"px";
	document.getElementById(p_id).style.top = p_pos_Y+"px";
    minX = parseInt(p_pos_X)-3;
    maxX = minX+limit;
}

/* Permet de récupéer la position du curseur */

function getPositionCurseur(e){

	//ie
	if(document.all){
		curX = event.clientX;
		curY = event.clientY;
	}

	//netscape 4
	if(document.layers){
		curX = e.pageX;
		curY = e.pageY;
	}

	//mozilla
	if(document.getElementById){
		curX = e.clientX;
		curY = e.clientY;
	}
}

/* Fonction appellée au début du drag */

function beginDrag(p_obj,e){

	isDragging = true;
	objectToDrag = p_obj;
	getPositionCurseur(e);
	ecartX = curX - parseInt(objectToDrag.style.left);
	ecartY = curY - parseInt(objectToDrag.style.top);

	var cadre = document.getElementById("cadre");
	cadre.style.border = "1px dashed";

	cadre.style.left = objectToDrag.style.left;
	cadre.style.top = objectToDrag.style.top;

	cadre.style.width = objectToDrag.offsetWidth+'px';
	cadre.style.height = objectToDrag.offsetHeight+'px';
	objectToDrag.style.opacity = '0.7';
}

/* Fonction appellée au début d'un drag exclusivement horizontale */

function beginDragX(p_obj,e){

	isDragging = true;
	objectToDrag = p_obj;
	getPositionCurseur(e);
	ecartX = curX - parseInt(objectToDrag.style.left);

	var cadre = document.getElementById("cadre");
	cadre.style.border = "1px dashed";

	cadre.style.left = objectToDrag.style.left;
	cadre.style.top = objectToDrag.style.top;

	cadre.style.width = objectToDrag.offsetWidth+'px';
	cadre.style.height = objectToDrag.offsetHeight+'px';
	objectToDrag.style.opacity = '0.7';
}

/* Fonction appellée pendant le drag */

function drag(e){

	var newPosX;
	var newPosY;

	if(isDragging == true){

		getPositionCurseur(e);

		newPosX = curX - ecartX;
		newPosY = curY - ecartY;

		objectToDrag.style.left = newPosX + 'px';
		objectToDrag.style.top = newPosY + 'px';
	}
}

/* Fonction appellée pendant un drag horizontale */

function dragX(e){

	var newPosX;

	if(isDragging == true){

		getPositionCurseur(e);

		newPosX = curX - ecartX;

               /* On vérifie que l'on ne sort pas de la zone définie */
                if((newPosX>=minX)&&(newPosX<=maxX)) {
                     objectToDrag.style.left = newPosX + 'px';
               }

	}
}

/* Fonction appellée à la fin du drag */

function endDrag(){

	if(isDragging == true){

		var X = Math.round(parseInt(objectToDrag.style.left)/25)*25;
		var Y = Math.round(parseInt(objectToDrag.style.top)/25)*25;

		objectToDrag.style.left = X + 'px';
		objectToDrag.style.top = Y + 'px';
	}

	var cadre = document.getElementById("cadre");
	cadre.style.border = "";

	objectToDrag.style.opacity = '0.5';
	isDragging = false;
}

/* Fonction appellée à la fin d'un drag horizontale */

function endDragX(){

	if(isDragging == true){

		var X = Math.round(parseInt(objectToDrag.style.left)/25)*25;

               if((X>=minX)&&(X<=maxX)) {
                     objectToDrag.style.left = X + 'px';
               }
	}

	var cadre = document.getElementById("cadre");
	cadre.style.border = "";

	objectToDrag.style.opacity = '0.5';
	isDragging = false;
}

/********* GESTION DES DATES *********/

/* Fonction qui prend une date en format fr et return l'objet javascript correspondant */

function getDate(strDate) {

   day = strDate.substring(0,2);
   month = strDate.substring(3,5);
   year = strDate.substring(6,10);
   d = new Date(0);
   d.setDate(day);
   d.setMonth(month-1);
   d.setFullYear(year);

   return d;
}


/* Fonction qui compare 2 dates et retourne :
   0 si date_1=date_2
   1 si date_1>date_2
   -1 si date_1<date_2 */

function compare(date_1, date_2){

      diff = date_1.getTime()-date_2.getTime();
      return (diff==0?diff:diff/Math.abs(diff));
}

/********* GESTION D'UN POPUP DANS UN POPUP *********/

/* Fonction identique à dims_showpopup_delayed.
  Utile pour un second dims_popup dans un dims_popup */

function dims_showpopup2_delayed(w) {

	if (timer_started) {

		w = parseInt(w);
		var dims_popup = dims_getelem('dims_popup2');

		with (dims_popup.style) {

			display = 'none';
			//dims_popup.innerHTML = msg+' '+posx+','+posy;
			dims_popup.innerHTML = msg;

			tmpleft = parseInt(posx) + 20;
			tmptop = parseInt(posy);

			if (w > 0) width = w+'px';
			else w = parseInt(dims_popup.offsetWidth);

			if (20 + w + parseInt(tmpleft) > parseInt(document.body.offsetWidth)) {
				tmpleft = parseInt(tmpleft) - w - 40;
			}

			left = tmpleft+'px';
			top = tmptop+'px';
			display = 'block';
			visibility = 'visible';
		}
		popup_displayed = true;
	}
}

/* Fonction identique à dims_showpopup
  Sert à afficherà dims_showpopup dans un dims_show_popup *
  Note : Il serait préférable de modifier la fonction dims_showpopup en ajoutant en paramètre l'id du div */

function dims_showpopup2(message, w, e, origine) {

	msg = message;
	if (w == '') w = 200;

	if (!origine) var origine = '';

	if (!e) var e = window.event;

	if (e.pageX || e.pageY)	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}

	if (origine == 'click')
	{
		timer_started = true;
		dims_showpopup2_delayed(w);
	}
	else
	{
		if (!timer_started)
		{
			timer_started = true;
			setTimeout("dims_showpopup2_delayed("+w+")", 1000*timerdelay);
		}

		if (popup_displayed) dims_showpopup2_delayed(w);
	}

}

/* Fonction identique à dims_hidepopup.
  Utile pour les même raison que décrite ci-dessus */

function dims_hidepopup2() {

	timer_started = false;
	popup_displayed = false;

	var dims_popup = dims_getelem('dims_popup2');
        dims_popup.innerHTML = '';

	with (dims_popup.style)	{
		display = 'none';
		visibility = 'hidden';
	}
}

/********* GESTION D'UN PROJET *********/

/* Permet la création d'un projet (formulaire) (en AJAX) */

function project_new(event) {

	dims_showpopup2('',600,event,'click');
        dims_xmlhttprequest_todiv('admin.php','op=project_add','','dims_popup2');

        return true;

}

/* Vérifie la cohérence des informations du formulaire */

function project_error(event, form) {

   /* les dates du projets */
   var date_start = document.form_project.elements['project_date_start'].value;
   var date_end = document.form_project.elements['project_date_end'].value;

   /* la comparaison des dates */
   var cmp = compare(getDate(date_start),getDate(date_end));

   /* la zone d'affichage des erreurs */
   var element = window.document.getElementById('project_error');

   /* le message d'erreur */
   var msg = '';

   if (cmp=='1') {
       msg = msg+"Erreur au niveau de la cohérence entre les dates, peut-être que la date de début est postérieur à la date de fin ?";

      if (element) {
         element.value=msg;
      }

      return false;
   }

   return true;
}

/********* GESTION D'UNE TACHE *********/

/* Permet la création d'une tâche (formulaire) depuis le menu (en AJAX) */

function task_new(event, idproject) {

	dims_showpopup2('',600,event,'click');
        dims_xmlhttprequest_todiv('admin.php','op=project_task_add&idproject='+idproject+'','','dims_popup2');
}

/* Permet la création d'une tâche (formulaire) depuis une autre tâche (en AJAX) */

function task_near_new(event, idproject, zoom, date, position) {

      dims_showpopup2('',600,event,'click');
      dims_xmlhttprequest_todiv('admin.php','op=project_task_add&idproject='+idproject+'&zoom='+zoom+'&date='+date+'&position='+position+'','','dims_popup2');
}

/* Vérifie la cohérence des informations du formulaire */

function task_error() {

   /* les dates du projets */
   var date_start = document.form_task.elements['task_date_start'].value;
   var date_end = document.form_task.elements['task_date_end'].value;

   /* la comparaison des dates */
   var cmp = compare(getDate(date_start),getDate(date_end));

   /* la zone d'affichage des erreurs */
   var element = window.document.getElementById('task_error');

   /* le message d'erreur */
   var msg = '';

   if (cmp=='1') {
      msg = msg+"Erreur au niveau de la cohérence entre les dates, peut-être que la date de début est postérieur à la date de fin ?";

      if (element) {
         element.value=msg;
      }
      /* Il y à une erreur */
      return true;
   }
   /* Il n'y a pas d'erreur */
   return false;

}

/* Permet la sauvegarde d'une tâche dans la base de données  (en AJAX)

  Note : Si utilisation en dims_xmlhttprequest_tofunction :
  Erreur : [Exception... "Component returned failure code: 0x80040111 (NS_ERROR_NOT_AVAILABLE) [nsIXMLHttpRequest.status]"  nsresult: "0x80040111 (NS_ERROR_NOT_AVAILABLE)"  location: "JS frame :: http://localhost/dims_v3_beta_2/include/functions.js :: anonymous :: line 868"  data: no]
  Fichier source : http://localhost/dims_v3_beta_2/include/functions.js
  Ligne : 868
  --> Corrigé */

function task_save(idproject, idtask, zoom) {

      /* Si il n'y a pas d'erreur */
      if (!task_error()) {

        var label = document.form_task.elements['task_label'].value;
        var priority = document.form_task.elements['task_priority'].value;
        var description = document.form_task.elements['task_description'].value;
        var date_start = document.form_task.elements['task_date_start'].value;
        var date_end = document.form_task.elements['task_date_end'].value;

        if (idtask=="") {
            /* Dans le cas d'une création de tâche */
            param = 'op=project_task_save&idproject='+idproject+'&zoom='+zoom+'&label='+label+'&priority='+priority+'&description='+description+'&datestart='+date_start+'&dateend='+date_end+'';
        } else {
            /* Dans le cas d'une modification d'une tâche existante */
            param = 'op=project_task_save&idproject='+idproject+'&idtask='+idtask+'&zoom='+zoom+'&label='+label+'&priority='+priority+'&description='+description+'&datestart='+date_start+'&dateend='+date_end+'';

        }

        /* On sauvegarde en AJAX */

        document.getElementById('div_loading').style.visibility="visible";
        document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Enregistrement de la tâche...</p>";

        dims_xmlhttprequest('admin.php',param);

        dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

      }
}

/* Permet de changer l'état d'une tâche (en AJAX) */

function task_change_state(id, idproject, zoom) {

      document.getElementById('div_loading').style.visibility="visible";
      document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Changement de l'état...</p>";

      dims_xmlhttprequest('admin.php','op=project_task_change_state&idproject='+idproject+'&zoom='+zoom+'&idtask='+id+'');
      dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

}

/* Permet de modifier les données d'une tâche (en AJAX) */

function task_modify(event, id, idproject, zoom) {

   dims_showpopup2('',600,event,'click');
   dims_xmlhttprequest_todiv('admin.php','op=project_task_modify&idtask='+id+'&idproject='+idproject+'&zoom='+zoom+'','','dims_popup2');

}

/* Permet de supprimer une tâche (en AJAX) et de rafraichir les divs */

function task_delete(id, idproject, zoom) {

      if (confirm("Voulez vous vraiment supprimer cette tâche ?")) {

         document.getElementById('div_loading').style.visibility="visible";
         document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Suppression de la tâche...</p>";

         dims_xmlhttprequest('admin.php','op=project_task_delete&idproject='+idproject+'&zoom='+zoom+'&idtask='+id+'');
         dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

         document.getElementById('div_loading').style.visibility="visible";
         document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Suppression de la tâche...</p>";

         dims_xmlhttprequest_tofunction('admin.php','op=project_diag_small_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'visu_div');
      }
}

/* Permet de rendre visible/invisible la barre d'outils d'une tâche données */

function displayBarMenu(me, infos, length){

   /* Mozilla et autres */
   if (document.getElementById)    {

      /*  On récupére l'élément */
      elem = document.getElementById(me);

      /*  Si l'élément est visible */
      if (elem.style.display=="block") {

	 elem.style.display="none";

      /*  Si l'élément n'est pas visible */
      } else {

         elem.style.display="block";
      }
   }
   /*  Ie et autres */
   else if (document.all)   {

      /*  On récupére l'élément */
      elem = document.all["a"];

      /*  Si l'élément est visible */
      if (elem.style.display=="block") {

         elem.style.display="none";

      /*  Si l'élément n'est pas visible */
      } else {

         elem.style.display="block";

      }
   }
}

/* Permet d'afficher les informations de la dernière tâche selectionnée (en AJAX) */

function task_infos(idtask, idproject, zoom) {

        var res = dims_xmlhttprequest('admin.php','op=project_task_infos&idtask='+idtask+'');

        document.getElementById('visualisation_infos').innerHTML=res;

        /* Première affichage du div d'information */
        if (document.getElementById('visualisation_infos').style.visibility=="hidden") {
                //document.getElementById('visualisation_infos').style.visibility="visible";
        }


}

/* Variable indispensable pour la création des dépendances */
var num_task = 0;
var id_task = 0;
var date_e_task;
var date_s_task;

/* Permet de lier 2 tâches entre elles (en AJAX) */

function task_link(event, id, datee, dates, idproject, zoom) {

   num_task++;

   var element = (document.getElementById) ? document.getElementById("task_"+id) : eval("document.all[task_"+id+"]");

   element.style.border = "2px solid #ff0000;";
   element.style.background = "#ffe9b2;";

   if (num_task==1) {
      /* On sauvegarde les dates et l'id de la première tâche */
      id_task = id;
      date_e_task = datee;
      date_s_task = dates;
   } else if (num_task==2) {

      document.getElementById('div_loading').style.visibility="visible";
      document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Enregistrement de la dépendance...</p>";

      var res = dims_xmlhttprequest('admin.php','op=project_task_task_add&idproject="'+idproject+'"&zoom="'+zoom+'"&idtask='+id_task+'&idtaskneeded='+id+'&datee1='+date_e_task+'&datee2='+datee+'');

      dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

      /* Affichage de l'erreur */
      if (res.length>2) { /* 2 car \n dans tous les cas */
         show_message(event, '400', res, '1');
      }
      num_task=0;
   }
}

/* Permet de supprimer une dépendance entre 2 tâches (en AJAX) */

function task_link_delete(id, idproject, zoom) {

   if (confirm("Voulez vous vraiment supprimer ce lien ?")) {

      document.getElementById('div_loading').style.visibility="visible";
      document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Suppression de la dépendance...</p>";

      dims_xmlhttprequest('admin.php','op=project_task_task_delete&idproject='+idproject+'&zoom='+zoom+'&idtasktask='+id+'');
      dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

   }
}

/********* GESTION D'UN OBJECTIF *********/

/* Permet la création d'un objectif (formulaire) depuis le menu (en AJAX) */

function objective_new(event, idproject) {

	dims_showpopup2('',400,event,'click');
        dims_xmlhttprequest_todiv('admin.php','op=project_objective_add&idproject='+idproject+'','','dims_popup2');
}

/* Permet de changer l'état d'un objectif (en AJAX) */

function objective_change_state(id, idproject, zoom) {

      document.getElementById('div_loading').style.visibility="visible";
      document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Changement de l'état...</p>";

      dims_xmlhttprequest('admin.php','op=project_objective_change_state&idproject='+idproject+'&zoom='+zoom+'&idobjective='+id+'');
      dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

}

/* Permet de supprimer un objectif (en AJAX) */

function objective_delete(id, idproject, zoom) {

   if (confirm("Voulez vous vraiment supprimer cet objectif ?")) {

      document.getElementById('div_loading').style.visibility="visible";
      document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Suppression de l'objectif...</p>";

      dims_xmlhttprequest('admin.php','op=project_objective_delete&idproject='+idproject+'&zoom='+zoom+'&idobjective='+id+'');
      dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

   }
}
/********* GESTION DES UTILISATEURS *********/

/* Permet d'attacher un utilisateur à une tâche (affichage) */

function attach_user(event, id, idproject, zoom) {

	dims_showpopup2('',500,event,'click');
        dims_xmlhttprequest_todiv('admin.php','op=project_task_attach_user&idtask='+id+'&idproject='+idproject+'&zoom='+zoom+'','','dims_popup2');
}

/* Permet d'attacher un utilisateur à une tâche (sauvegarde) */

function attach_user_save(id, idproject, zoom) {

	document.getElementById('div_loading').style.visibility="visible";
	document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Enregistrement des utilisateurs...</p>";

        var res = dims_xmlhttprequest('admin.php','op=project_task_attach_user_save&idtask='+id+'');

        dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

}

/********* GESTION DE LA COMMUNICATION AVEC L'UTILISATEUR *********/

/* Permet d'afficher des messages de différents types à l'utilisateur */

function show_message(event, width, text, code, idtasktask, idproject, zoom) {

   var content ='<br><br>';

   switch (code) {
      case '1':
         var ok = '<input type="button" class="button" value="Ok" onclick="dims_getelem(\'dims_popup2\').style.visibility=\'hidden\';">';
         content += ok;
         break;
      case '2':
         var cancel = '<input type="button" class="button" value="Annuler" onclick="dims_getelem(\'dims_popup2\').style.visibility=\'hidden\';">';
         var correct = '<input type="button" class="button" value="Corriger le problème" onclick="task_aaa(\''+idtasktask+'\',\''+idproject+'\',\''+zoom+'\';dims_getelem(\'dims_popup2\').style.visibility=\'hidden\';"">';
         var del = '<input type="button" class="button" value="Supprimer"  onclick="task_link_delete(\''+idtasktask+'\',\''+idproject+'\',\''+zoom+'\');dims_getelem(\'dims_popup2\').style.visibility=\'hidden\';"">';
         content += cancel+correct+del;
         break;
      default: break;
   }

   dims_showpopup2(text+content,width,event,'click');
}

/********* GESTION DE LA VISUALISATION *********/

/* Permet de mettre le visualisateur en miniture pour visualiser le projet en plein écran (Dynamique)*/

function change_display(idproject, zoom, hide) {
 /*
   document.getElementById('div_loading').style.visibility="visible";
   document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Chargement de la vue...</p>";

    if(window.innerWidth) {
        x = window.innerWidth;
    }
    else {
        x=document.body.offsetWidth;
    }
   //if( window.innerWidth) {
    var top = document.getElementById('main_div_project').offsetTop;
    var left = document.getElementById('main_div_project').offsetLeft;
    */
    // Rafraichissement du diagramme de gantt
  // dims_xmlhttprequest_tofunction('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

   //document.getElementById('div_loading').style.visibility="visible";
   //document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/img/loading.gif'><br>Chargement de la vue...</p>";

   // Si on doit afficher le visualisateur

   if (hide=='n') {

      document.getElementById('visualisation_header').style.width='23.28%';

      inner_html = '<div style="float:left;width:90%;height:16px;"><b>Visualisateur</b></div>';
      inner_html += '<div style="float:left;" >';
      inner_html += '<img src="./common/modules/projects/img/v_close_16.png" onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="change_display(1,\'m\',\'o\');">';
      inner_html += '</div>';

      document.getElementById('visualisation_header').innerHTML=inner_html;
      document.getElementById('visualisation_header').style.visibility="visible";

      document.getElementById('visualisation').style.width='23.28%';
      document.getElementById('visualisation').style.background='#f0f0f0'; // Bug assez byzard contourné : le css dims est prioritaire quand on modifie en ajax la div

      inner_html =  '<div id="visu_div" style="float:left;overflow:none;height:100%;width:80%"></div>';
      inner_html += '<div id="visualisation_zoom" class=="visualisation_zoom" style="float:right;width:15.43%;height:100%;">';
      inner_html +=  '<img id="zoom_img" style="border:none;" src="./common/modules/projects/img/visualisation_mois.png"></div>';


      document.getElementById('visualisation').innerHTML=inner_html;

      document.getElementById('visualisation_area').style.visibility="visible";
      document.getElementById('visualisation_cadre_j').style.visibility="visible";
      document.getElementById('visualisation_cadre_s').style.visibility="visible";
      document.getElementById('visualisation_cadre_m').style.visibility="visible";
      document.getElementById('visualisation_cadre_a').style.visibility="visible";
      document.getElementById('visualisation_infos').style.visibility="visible";

      change_view(idproject, zoom);
   }
   // Si on doit réduire le visualisateur
   else {
      document.getElementById('visualisation_header').style.visibility="hidden";

      document.getElementById('visualisation').style.width='1.955%';
      document.getElementById('visualisation').style.background='#ffffff';
      document.getElementById('visualisation').style.border='none';

      inner_html =  '<img id="zoom_img" style="border:none;padding:none;width:25px;height:75px;" src="./common/modules/projects/img/visualisateur_top.png">';
      inner_html += '<img style="background:#abccdc;border:none;padding:none;width:22px;height:22px;" src="./common/modules/projects/img/v_open_22.png"  onmouseover="javascript:this.style.cursor=\'pointer\';" onclick="change_display(1,\'m \',\'n\');">';
      inner_html += '<img id="zoom_img" style="border:none;padding:none;width:25px;height:75px;" src="./common/modules/projects/img/visualisateur_bottom.png">';

      document.getElementById('visualisation').innerHTML=inner_html;

      document.getElementById('visualisation_area').style.visibility="hidden";
      document.getElementById('visualisation_cadre_j').style.visibility="hidden";
      document.getElementById('visualisation_cadre_s').style.visibility="hidden";
      document.getElementById('visualisation_cadre_m').style.visibility="hidden";
      document.getElementById('visualisation_cadre_a').style.visibility="hidden";
      document.getElementById('visualisation_infos').style.visibility="hidden";

   }

   // On actualise toutes les positions
   //position_all(hide);
}

/* Permet de changer le zoom sur la visualisation d'un projet (en AJAX) */

function change_view(idproject, zoom) {
   document.getElementById('div_loading').style.visibility="visible";
   document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/img/loading.gif'><br>Chargement de la vue...</p>";
   /*
   switch(zoom) {
      case "j": document.getElementById('zoom_img').src = "./common/modules/projects/img/visualisation_jour.png";break;
      case "s": document.getElementById('zoom_img').src = "./common/modules/projects/img/visualisation_semaine.png";break
      case "m": document.getElementById('zoom_img').src = "./common/modules/projects/img/visualisation_mois.png";break
      case "a": document.getElementById('zoom_img').src = "./common/modules/projects/img/visualisation_annee.png";break
      default : document.getElementById('zoom_img').src = "./common/modules/projects/img/visualisation_mois.png";break

   }*/

   /* Rafraichissement du diagramme de gantt */
   if(window.innerWidth)
		dims_xmlhttprequest_todiv('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'','','diag_div');
   else
		dims_xmlhttprequest_todiv('admin.php','op=project_diag_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+document.body.offsetWidth+'&height='+document.body.offsetHeight+'','','diag_div');

   //refresh_now('','diag_div');
   //document.getElementById('div_loading').style.visibility="visible";
   //document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Chargement de la nouvelle vue...</p>";

   /* Rafraichissement du visualisateur */
   dims_xmlhttprequest_tofunction('admin.php','op=project_diag_small_gantt&idproject='+idproject+'&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'visu_div');
}

/* Permet de changer le zoom sur la visualisation multi-projet (en AJAX) */

function change_view_multi_project(zoom) {

   document.getElementById('div_loading').style.visibility="visible";
   document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Chargement de la vue...</p>";

   /* Rafraichissement */
   dims_xmlhttprequest_tofunction('admin.php','op=project_multi_project&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

}

/* Permet de changer le zoom sur la visualisation multi-tâche (en AJAX) */

function change_view_multi_task(zoom) {

   document.getElementById('div_loading').style.visibility="visible";
   document.getElementById('div_loading').innerHTML="<p align='center'><br><br><img src='./common/modules/projects/img/loading.gif'><br>Chargement de la vue...</p>";

   /* Rafraichissement */
   dims_xmlhttprequest_tofunction('admin.php','op=project_multi_task&zoom='+zoom+'&width='+window.innerWidth+'&height='+window.innerHeight+'',refresh_now,'diag_div');

}

/* Rafraichie la div passé en paramètre (en modifiant l'innerHtml */

function refresh_now(res,elemdiv) {
        document.getElementById('div_loading').style.visibility="hidden";
        document.getElementById('div_loading').innerHTML="";
        document.getElementById(elemdiv).innerHTML=res;

}

/* Permet de changer le contenu du popup Dims */

function dims_modify_popup(contents) {

	var dims_popup = (document.getElementById) ? document.getElementById('dims_popup') : eval("document.all['dims_popup']");
	dims_popup.innerHTML = contents;
}


/* Fonction permettant de positionner toutes les divs dynamiquement (suivant la résolution de l'écran (taille du navigateur) et les tailles des div)

   A FAIRE :
  /* Mozilla : window.innerHeight et window.innerWidth espace fenetre dispo.
  Internet Explorer : document.body.clientHeight et document.body.clientWidth */

function position_all (hide) {

   visu_header_w = "23.28";
   visu_header_h = "15"; // (on fixe la valeur)

   visu_w = "23.43";
   visu_h = "25.58";

   visu_zoom_w = "15.43";
   visu_zoom_h = "100";

   visu_cadre_w = "3.000";
   visu_cadre_h = "3.800";

   visu_area_w = "7.815";
   visu_area_h = "25.20";

   visu_header_hide_w = "1.955";
   visu_header_hide_h = "25";

   visu_hide_w = "1.715";
   visu_hide_h = "25.58";

   /* Si le visualisateur est visible */
   if (hide=="n" || true) {

      document.getElementById('main_div_project').style.width="75%";
		if(window.innerWidth) {
			width = window.innerWidth;
			height = window.innerHeight;
		}
		else {
			width=document.body.offsetWidth;
			height = document.body.offsetHeight;
		}
      area_limit = (0.82*((width*visu_w)/100)-(width*visu_area_w)/100);
      x_cadre = (width-(0.150*((width*visu_w)/100))-12);
      y_cadre_j = (100+(0.220*((height*visu_w)/100)));
      y_cadre_s = (100+(0.391*((height*visu_w)/100)));
      y_cadre_m = (100+(0.560*((height*visu_w)/100)));
      y_cadre_a = (100+(0.730*((height*visu_w)/100)));

      positionne('visualisation_header', (width-((width*visu_header_w)/100)-15), 80, 0);
      positionne('visualisation', (width-((width*visu_w)/100)-15)+10, 100, 0);
      positionne('visualisation_cadre_j', x_cadre,y_cadre_j,0);
      positionne('visualisation_cadre_s', x_cadre,y_cadre_s,0);
      positionne('visualisation_cadre_m', x_cadre,y_cadre_m,0);
      positionne('visualisation_cadre_a', x_cadre,y_cadre_a,0);
      positionne('visualisation_area',(width-((width*visu_w)/100)-13), 100, area_limit);
      positionne('visualisation_infos', (width-((width*visu_w)/100)-15)+10, 300, 0);
      isDragging = false;

  }
  /* Si le visualisateur est en mode miniature */
  else {

      document.getElementById('main_div').style.width="96.6%";

      positionne('visualisation', (window.innerWidth-((window.innerWidth*visu_hide_w)/100)-5), 110, 0);

      isDragging = false;
  }
}

var decalgantt=8;

function addElementGantt(parentId, elementTag, elementId, html,xdeb,ydeb,xfin,yfin) {
    // Adds an element to the document
    var p = document.getElementById(parentId);

    var newElement = document.createElement(elementTag);
    newElement.setAttribute('id', elementId);

    var width=Math.abs(xfin-xdeb);
    var height=Math.abs(yfin-ydeb);

    if (width==0) width=1;
    if ( height==0) height=1;

    // definition de la taille du canvas
	if (xfin<(xdeb+(decalgantt*2))) width+=(decalgantt*2);

	if (width!=null) newElement.setAttribute('width', width);
    if (height!=null) newElement.setAttribute('height',height+2);

    //Calcul du plus petit top et left, pour largeur et hauteur : valeur absolue
	//  [## T1 ##]-.
    //    _________|
    //    |
    //    |-->[## T2 ##]
    if (xfin<xdeb) {
    	minx=xdeb-width+decalgantt;
		miny=ydeb;
    	xdeb=width-decalgantt;
		xfin=decalgantt;
		newElement.style.left=minx+'px';
		newElement.style.top=miny+'px';
		newElement.style.position='absolute';
		p.appendChild(newElement);

		if(!window.innerWidth) {
			newElement=G_vmlCanvasManager.initElement(newElement);
		}
		canvas = document.getElementById(elementId);
		if (canvas.getContext){
			var ctx = canvas.getContext('2d');
			ctx.save();
			ctx.lineWidth = 1;
			ctx.strokeStyle = '#000000';
			ctx.beginPath();
			ctx.moveTo(xdeb,0);
			ctx.lineTo(width,0);
			ctx.lineTo(width,height/2);
			ctx.lineTo(0,height/2);
			ctx.lineTo(0,height);
			ctx.lineTo(decalgantt-4,height);
			ctx.stroke();
			ctx.beginPath();
			ctx.rect(decalgantt-4,height-2,4,4);
			ctx.fill();
			ctx.stroke();
			ctx.restore();
		}
    }
    else {
		//[## T1 ##]
        //         |__- >[## T2 ##]
    	minx=xdeb;
		miny=ydeb;
		newElement.style.left=minx+'px';
		newElement.style.top=miny+'px';
		newElement.style.position='absolute';
		p.appendChild(newElement);

		if(!window.innerWidth) {
			newElement=G_vmlCanvasManager.initElement(newElement);
		}
		canvas = document.getElementById(elementId);
		if (canvas.getContext){
			var ctx = canvas.getContext('2d');
			ctx.save();
			ctx.lineWidth = 1;
			ctx.strokeStyle = '#000000';
			ctx.moveTo(0,0);
			ctx.lineTo(decalgantt,0);
			ctx.lineTo(decalgantt,height);
			ctx.lineTo(width-4,height);
			ctx.stroke();
			ctx.beginPath();
			ctx.rect(width-4,height-2,4,4);
			ctx.fill();
			ctx.stroke();
			ctx.restore();
		}
    }

	p.appendChild(newElement);
	newElement.innerHTML = html;


}

function removeElementGantt(elementId) {
// Removes an element from the document
var element = document.getElementById(elementId);
element.parentNode.removeChild(element);
}

function drawGantt(elem,xdep,ydep,xfin,yfin){
	var canvas = document.getElementById(elem);
	if (canvas.getContext){

	  	var ctx = canvas.getContext('2d');
	  	ctx.save();
	  	ctx.lineWidth = 1;
		ctx.strokeStyle = '#BCBCBC';
	  	ctx.beginPath();
	  	ctx.moveTo(xdep,ydep);

		dist=(yfin-ydep)*1;
		ctx.bezierCurveTo(xdep,dist,xfin,yfin-(dist/2),xfin,yfin);

		ctx.stroke();
		ctx.restore();
	}
}