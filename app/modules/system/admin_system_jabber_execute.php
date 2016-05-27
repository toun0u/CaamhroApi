<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . "/include/jabber/paramsJabber.php");

$message='
		<initDims>
		<from>{NetlorHome}</from>
		<to>{RelaisNetlor}</to>
		<infosDims>
			<nom>'.$_SESSION['ejabber']['dims_name'].'</nom>
			<ip>'.$_SESSION['ejabber']['host_ip'].'</ip>
						<host>'.$_SESSION['ejabber']['host_name'].'</host>
						<clefSecuriteHost>'.$_SESSION['ejabber']['host_securitykey'].'</clefSecuriteHost>
		</infosDims>
	</initDims>';


// envoi au relai

$socket = fsockopen(params::host, params::port, $errno, $errstr, 100);

if ($socket) {
	//echo $message;
	echo "\n\n=========================\n\nEnvoie du message : \n".$message."\n\n=========================\n\n";
	fwrite($socket, $message."\0");
	$message='';

	while(true) {
			$c = fread($socket, 1);
			$message .= $c;
			if ($c == "\0")
			{
					echo "\n\tMessage reçu : \n".$message;
					$_SESSION['ejabber']['message']=$message;
					fclose($socket);

					dims_redirect('/admin.php?op=result_request');
//$this->process($message);
					echo "\nEn attente d'un message...\n\n";
					$message = "";

					//exit();
			}
	}
}
?>
