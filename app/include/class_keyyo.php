<?php
//API RELATIF à l'opérateur téléphonique Keyyo
define("_KEYYO_API", "https://ssl.keyyo.com/");
//Serveur Node.js relatif à l'opérateur téléphonique Keyyo
//define("_TELEPHONY_API", "http://telephony:1337/");

/**
 * Classe d'interaction avec l'API VoIP Keyyo et le proxy NodeJS Dims Telephony
 * @author Guillaume Lesniak <xplodwild@cyanogenmod.org>
 */

//Interface Telephony générique à tous les opérateurs
class Keyyo implements Telephony{

	//attributs
	private $SIPAccount;

	/**
	 * Constructeur
	 * @param SIPAccount Le numéro de compte Keyyo
	 */
	public function __construct($SIPAccount) {
		$this->SIPAccount = $SIPAccount;
	}

	/**
	 * Envoie un SMS contenant le texte $text au destinataire
	 * $callee
	 * @param callee Le numéro du destinataire, au format "33612345678"
	 * @param text Le texte du SMS
	 */
	//il faut souscrire au forfait spécifique pour utiliser le service d'envoie de sms
	public function sendSMS($callee, $text) {
		//$text=urlencode($text);
		$url=_KEYYO_API."sendsms.html?ACCOUNT=".$this->SIPAccount."&CALLEE=".$callee."&MSG=".$text;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		echo $data;
	}

	/**
	 * Etablit un appel au numéro $callee, et enregistre le nom de l'appelé
	 * désigné par $calleeName
	 * @param callee Le numéro du destinataire au format international (0033123456789)
	 * @param calleeName Le nom du destinataire
	 */
	public function makeCall($callee, $calleeName) {
		/*version avec verification de l'ip source
		if($fp=fopen(_KEYYO_API."makecall.html?ACCOUNT=$this->SIPAccount&CALLEE=$callee", "r")){
			echo "OK";
		}else{
			echo "Error";
		} 
		*/

		$postFields=array(
			"callee"=>"$callee",
			"account"=>"$this->SIPAccount"
		);
 
	// Tableau contenant les options de téléchargement
	$options=array(
	      CURLOPT_URL            => "http://dimsportal/api_keyyo.php/appels",       // Url cible (l'url de la page que vous voulez télécharger)
	      CURLOPT_RETURNTRANSFER => true,       // Retourner le contenu téléchargé dans une chaine (au lieu de l'afficher directement)
	      CURLOPT_HEADER         => true,      // Ne pas inclure l'entête de réponse du serveur dans la chaine retournée
	      CURLOPT_FAILONERROR    => true,       // Gestion des codes d'erreur HTTP supérieurs ou égaux à 400
	      
	      //Pour lES POSTs
	      CURLOPT_POST           => true,       // Effectuer une requête de type POST
	      CURLOPT_POSTFIELDS     => $postFields // Le tableau associatif contenant les variables envoyées par POST au serveur
	);
 
	////////// MAIN
	// Création d'un nouvelle ressource cURL
	$CURL=curl_init();
	// Erreur suffisante pour justifier un die()
	if(empty($CURL)){die("ERREUR curl_init : Il semble que cURL ne soit pas disponible.");}
      // Configuration des options de téléchargement
      curl_setopt_array($CURL,$options);
      // Exécution de la requête
      $content=curl_exec($CURL);            // Le contenu téléchargé est enregistré dans la variable $content. Libre à vous de l'afficher.
      // Si il s'est produit une erreur lors du téléchargement
      if(curl_errno($CURL)){
            //Le message d'erreur correspondant est affiché
            echo "ERREUR curl_exec : ".curl_error($CURL);
      }
		// Fermeture de la session cURL
		curl_close($CURL);
		echo $content;


	}
	
	//fonction de notifications de prise de notes d'un appel
	// cf voir admin-voip.php
	public function takeCall($sip,$ref,$number,$direction,$sessionid){
		$url=_TELEPHONY_API."takeCall?sip=$sip&ref=$ref&number=$number&direction=$direction&sessionid=$sessionid";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
	}


}