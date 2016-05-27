(function($){
	$.fn.voip_call = function(account,url){  
        if(account!=null ){ 
            var contact = $(this).attr('data-phone').toString().trim();
            var ligne = account.toString().trim();
            var name =  $(this).attr('data-callname').toString();
            var res = "<img style='cursor: pointer' class='appel_contact' src="+url+" alt='téléphone' title='Appeler "+name+" !' >";
            if(contact != 'Numéro')
                $(res).appendTo(this);
            
            $(this).find("img").click(function(){
                 $.post( "/api_keyyo.php/appels",{ account : international_meta(ligne), callee : international_meta(contact)});
            });
        }
    };
 
})(jQuery)
 
function international_meta(phoneNumber) {
        var phoneUtil = i18n.phonenumbers.PhoneNumberUtil.getInstance();
        var test = phoneUtil.parse(phoneNumber, "FR");
    	var codepays = test.values_["1"].toString();
    	var num = test.values_["2"].toString();
    	return codepays+num;
}

