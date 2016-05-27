<?php
// Lejal Simon
// Model pour telephony

//On notifie le serveur a changer si l'on veut utiliser un autre opérateur de keyyo
define("_TELEPHONY_API", "http://telephony:1337/");
//serveur node.js relatif à l'opérateur keyyo
//local pour le dev

class TelephonyModel {
	
    private $db;
	
    public function __construct() {
		$this->db = dims::getInstance()->getDb();
	}

   // Renvoie toutes les lignes référencés à l'user connecté via son login
    public function getSipAccounts2() {
        $loguser=$_SESSION["dims"]["login"];
        $sql = "SELECT phone, login FROM dims_user WHERE login='$loguser'";
        $res = $this->db->query($sql, array());
        $accounts = array();
        while($account = $this->db->fetchrow($res)) {
            $accounts[] = $account;
        }
        $accountsbis= array();
        for($i=0;$i<count($accounts);$i++){
           $accountsbis[$i]['sipaccount']=$accounts[$i]['phone'];
           $accountsbis[$i]['name']=$accounts[$i]['login'];
        }
        return $accountsbis;
    }

	//Renvoie le token nécessaire à la téléphonie ou le génère 
    public function retrieveOrGenerateToken($account) {
        $sql = 'SELECT token FROM dims_mod_telephony_tokens WHERE account=:account AND expires>'.time();
		$res = $this->db->query($sql, array(
            ':account' => $account
        ));
        if ($this->db->numrows() == 0) {
        	// Pas de token, on supprime les éventuels tokens expirés
        	$sql = 'DELETE FROM dims_mod_telephony_tokens WHERE expires<=NOW()';
        	$this->db->query($sql);

        	// On en génère un nouveau, valide 24h
        	$token = sha1(uniqid() . time() . microtime(true));
        	$sql = 'INSERT INTO dims_mod_telephony_tokens(token, account, expires) VALUES(:token, :account, :expires);';
        	$this->db->query($sql, array(
        		':token' => $token,
        		':account' => $account,
        		':expires' => time()+3600*24
        	));

        	// On notifie le serveur a changer si l'on veut utiliser un autre opérateur de keyyo
        	// @file_get_contents(_TELEPHONY_API."/reloadtokens"); entraine un lag violent

        	return $token;
        } else {
        	// On a un token, on le renvoie
        	$row = $this->db->fetchrow($res);
        	return $row['token'];
        }
	}
}

?>