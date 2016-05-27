<script type="text/javascript">
    //Enlever les notifications après x secondes
    function remv_notif(id,time){
        setTimeout(function(){
            document.getElementById(id).innerHTML = "&nbsp;";
        },time);
    }

    //AJAX: Recherche d'un contact
    function search_contact_list() {
        if(search_contact_list.arguments[0]!=0){
            var q = dims_getelem('q');
            q = q.value;
            q = "&q="+q;
        }else{
            q ='';
        }
        dims_xmlhttprequest_todiv('admin.php','action=search&action_sup=adv_search'+q,'','list_contact');
        if(search_contact_list.arguments[0]!=0){
            return false;
        }
    }

    function search_ent_list() {
       if(search_ent_list.arguments[0]!=0){
            var q = dims_getelem('q');
            q = q.value;
            q = "&q="+q;
        }else{
            q ='';
        }
        dims_xmlhttprequest_todiv('admin.php','action=search_ent&action_sup=adv_search&q='+q,'','list_contact');
        if(search_contact_list.arguments[0]!=0){
            return false;
        }
    }

    //AJAX : afficher le profil du contact
    function show_profil(id_user,filter){
        if(filter != ""){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=profil&action_sup=show&id='+id_user+filter_url,'','profil_contact');
    }

    //AJAX : recherche rapide d'entreprise
    function speed_search_ent(){
        var q_search = dims_getelem('q_search');
        q_search = q_search.value;
        dims_xmlhttprequest_todiv('admin.php','action=search&action_sup=speed_search&q='+q_search,'','speed_search');
    }

    function show_ent(id_ent,filter){
        if(filter != ""){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=entreprise&action_sup=show&id='+id_ent+filter_url,'','profil_contact');
    }

    function show_entview(id_ent,filter){
        if(filter != ""){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=view_services&action_sup=show&id='+id_ent+filter_url,'','profil_contact');
    }


     //AJAX : afficher le profil du contact
    function show_profil_plus(id_user,filter){
        if(filter != ""){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=profil&action_sup=showplus&id='+id_user+filter_url,'','profil_contact');
    }

    //AJAX : modifier les liens avec le contact
    function update_profil(id_user){
        //On recupere l'etat des checkbox
        var ami = document.getElementById('contact_ami').checked;
        var loisirs = document.getElementById('contact_loisirs').checked;
        var pro = document.getElementById('contact_pro').checked;

        //On retransforme en booleen numerique
        if(ami == true) ami=1; else ami=0;
        if(loisirs == true) loisirs=1;else loisirs=0;
        if(pro == true) pro=1; else pro=0;

        //On envois pas AJAX et on affiche le résultat
        var check = dims_xmlhttprequest('admin.php','action=profil&action_sup=update&update='+id_user+'&data='+ami+'-'+loisirs+'-'+pro);
        if(check == '1'){document.getElementById('infos_contact').innerHTML = "<span class='infos_grant'>Modification &eacute;ffectu&eacute;e avec succ&egrave;s</span>";}
        if(check == '0'){document.getElementById('infos_contact').innerHTML = "<span class='infos_error'>Modification &eacute;chou&eacute;e</span>";}

        //On rafraichis la liste des contacts
        refresh_list_contact(false);

        //On lance la fonction qui fera disparaitre la notification après plusieurs secondes
        remv_notif('infos_contact',2000);

    }

    //AJAX : Modifier note sur profil contact
    function update_note(id_user,filter){
        //On recupere le champs note
        var note = document.getElementById('note_perso').value;

        //on envoie par AjAX
        var check = dims_xmlhttprequest('admin.php','action=profil&action_sup=update&update='+id_user+'&data_note='+note);
        if(check == '1'){document.getElementById('infos_contact').innerHTML = "<span class='infos_grant'>Modification &eacute;ffectu&eacute;e avec succ&egrave;s</span>";}
        if(check == '0'){document.getElementById('infos_contact').innerHTML = "<span class='infos_error'>Modification &eacute;chou&eacute;e</span>";}

        //On rafraichis la liste des contacts
        if(filter){var filter_url = "&filter="+filter;}else{filter_url = "";}
        refresh_list_contact(filter_url);

        //On lance la fonction qui fera disparaitre la notification après plusieurs secondes
        remv_notif('infos_contact',2000);
    }

    //AJAX : supprimer le contact de la liste des adresses
    function del_profil(heading,id_user){
        if(confirm('Etes-vous sur de vouloir supprimer ce contact ?'))
        {
        var check = dims_xmlhttprequest('admin.php','action=profil&action_sup=del&id='+id_user);

        if(check == '1'){document.getElementById('profil_contact').innerHTML = "<div id='infos_contact'><span class='infos_grant'>Suppression &eacute;ffectu&eacute;e avec succ&egrave;s</span></div>";}
        if(check == '0'){document.getElementById('infos_contact').innerHTML = "<span class='infos_error'>Suppression &eacute;chou&eacute;e</span>";}

        //On rafraichis la liste des contacts
        refresh_list_contact('');

        //On lance la fonction qui fera disparaitre la notification après plusieurs secondes
        remv_notif('infos_contact',2000);
        }
    }

    //AJAX : rafraichir la liste des adresses apres modification ou suppression
    function refresh_list_contact(filter){
        if(filter){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=refresh'+filter_url,'','dims_contact');
    }

    function refresh_list_ent(filter){
        if(filter){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=refresh'+filter_url,'','dims_contact');
    }

    //AJAX : afficher formulaire de recherche d'adresse
    function form_search_contact() {
        dims_xmlhttprequest_todiv('admin.php','action=add','','profil_contact');
    }

    //AJAX : afficher formulaire de recherche d'adresse avancee
    function form2_search_contact() {
        dims_xmlhttprequest_todiv('admin.php','action=add&action_sup=adv_form_search','','profil_contact');
    }

    function form_create_contact() {
        dims_xmlhttprequest_todiv('admin.php','action=profil&action_sup=show&create=1','','profil_contact');
    }

    function form_create_ent () {
        dims_xmlhttprequest_todiv('admin.php','action=entreprise&action_sup=show&create=1','','profil_contact');
    }

    //AJAX : Ajouter une addresse
    function add_contact(id){

        //On envois pas AJAX et on affiche le résultat
        var check = dims_xmlhttprequest('admin.php','action=profil&action_sup=add&id='+id);
        if(check == '1'){document.getElementById('infos_contact').innerHTML = "<span class='infos_grant'>Ajout &eacute;ffectu&eacute; avec succ&egrave;s</span>";}
        if(check == '0'){document.getElementById('infos_contact').innerHTML = "<span class='infos_error'>Ajout &eacute;chou&eacute;</span>";}

        //On rafraichis la liste des contacts
        refresh_list_contact('');

        //On lance la fonction qui fera disparaitre la notification après plusieurs secondes
        remv_notif('infos_contact',2000);
    }

     //AJAX : afficher resultat(s) de recherche d'adresse
    function search_contact(filter){
        //On recupere les valeurs de champs de recherche
        var lastname = document.getElementById('contact_search_lastname').value;
        var firstname = document.getElementById('contact_search_firstname').value;
        var q = lastname+'%sep%'+firstname;
        //On apelle l'AJAX
        if(filter){var filter_url = "&filter="+filter;}else{filter_url = "";}
        dims_xmlhttprequest_todiv('admin.php','action=add&action_sup=search'+filter_url+'&q='+q,'','res_search');
    }

    //AJAX : afficher resultat(s) de la recherche avancee pour ajout
    function search_contact2(){
        var lastname = document.getElementById('contact_search_lastname').value;
        var firstname = document.getElementById('contact_search_firstname').value;
        var arrond = document.getElementById('arrond_map').value;
        var inter = document.getElementById('interest').value;
        var car = document.getElementById('car').value;
        var q = lastname + "%sep" + firstname + "%sep" + arrond + "%sep" + inter + "%sep" + car;
        dims_xmlhttprequest_todiv('admin.php','action=add&action_sup=adv_search&q='+q,'','profil_contact');
    }

    //AJAX : Afficher formulaire recherche avancee
    function form_adv_search_contact(heading){
        dims_xmlhttprequest_todiv('admin.php','action=search&action_sup=search','','profil_contact');
    }

    //AJAX : afficher resultat(s) de la recherche avancee
    function adv_search_contact(){
        var lastname = document.getElementById('contact_search_lastname').value;
        var firstname = document.getElementById('contact_search_firstname').value;
        var arrond = document.getElementById('arrond_map').value;
        var inter = document.getElementById('interest').value;
        var car = document.getElementById('car').value;
        var q = lastname + "%sep" + firstname + "%sep" + arrond + "%sep" + inter + "%sep" + car;
        dims_xmlhttprequest_todiv('admin.php','action=search&action_sup=adv_search&q='+q,'','list_contact');
    }

    //AJAX : switch permettant de passer de la recherche rapide a la recherche avancee et inversement
    function switch_add(onglet){
        if(onglet == 1){
            form_search_contact();
        }
        if(onglet == 2){
            form2_search_contact();
        }
    }
</script>