<?php

/**
 * Cette classe permet d'encrypter un fichier, ou texte
 * à l'aide d'une clé publique PGP.
 *
 * @author Guillaume Lesniak <xplodwild@cyanogenmod.org>
 * @created July 1st 2013
 */
class PGP {
	// ============
	// Fields
	// ============
	private $Handler;
	private $GPGDir;


	// ============
	// Methods
	// ============

	/**
	 * Constructeur
	 */
	public function __construct($www) {
		// Configuration
		putenv("GNUPGHOME=$www/gpg");
		$this->GPGDir = $www;
		$this->Handler = new gnupg();
		$this->Handler->seterrormode(gnupg::ERROR_EXCEPTION);
	}

	/**
	 * Importe une clé depuis le nom de fichier spécifié.
	 * Le fichier .gpg et .asc seront chargés automatiquement si
	 * $decryptToo est à true, sinon seule la clé GPG pour l'encryption
	 * sera chargée, mais pas le .asc pour la décryption.
	 */
	public function importKey($name, $decryptToo) {
		$this->Handler->import(file_get_contents($this->GPGDir."/gpg/".$name.".gpg"));
		if ($decryptToo) {
			$this->Handler->import(file_get_contents($this->GPGDir."/gpg/".$name.".asc"));
		}
	}

	/**
	 * Supprime toutes les clés du cache local. Un nettoyage est nécessaire
	 * puisque chaque clé importée par importKey reste dans le cache
	 * pubring.gpg de GnuPG, et sera automatiquement réutilisée le cas
	 * échéant.
	 */
	public function clearKeys() {
		$this->Handler->cleardecryptkeys();
		$this->Handler->clearencryptkeys();
		$this->Handler->clearsignkeys();

		// HACK
		echo shell_exec("rm " . $this->GPGDir."/gpg/pubring.*");
		echo shell_exec("rm " . $this->GPGDir."/gpg/secring.gpg");
		echo shell_exec("rm " . $this->GPGDir."/gpg/trustdb.gpg");
	}

	/**
	 * Supprime une clé avec le fingerprint spécifié
	 */
	public function removeKey($fingerprint) {
		$this->Handler->deletekey($fingerprint);
	}

	/**
	 * Donne les infos d'une clé PGP, en se basant sur un morceau
	 * de son nom.
	 */
	public function dumpKeyInfo($key) {
		$records = $this->Handler->keyinfo($key);
		foreach($records as $record) {
			echo htmlspecialchars($record['uids'][0]['uid']) . '<br/>';
			foreach ($record['subkeys'] as $key) {
				if ($key['can_sign'] == 1) {
					echo 'Signing key created on: ' . date('d M Y', $key['timestamp']) . '<br/>';
					echo 'Signing key fingerprint: ' . $key['fingerprint'] . '<br/>';
				}
				if ($key['can_encrypt'] == 1) {
					echo 'Encryption key created on: ' . date('d M Y', $key['timestamp']) . '<br/>';
					echo 'Encryption key fingerprint: ' . $key['fingerprint'] . '<br/>';
				}
				echo '<br/>';
			}
		}
	}

	/**
	 * Retourne si oui ou non une clé existe pour l'adresse
	 * e-mail indiquée
	 */
	public function keyExists($email) {
		$keys = $this->Handler->keyinfo($email);
		if (count($keys) == 1) {
			return true;
		} else if (count($keys) > 1) {
			echo "WARNING: More than one key for $email!";
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Génère une clé pour l'utilisateur et l'adresse mail
	 * indiquée.
	 */
	public function generateKey($username, $email) {
		// Génération du script d'infos
		$file = uniqid();
		$script = "%echo Generating a default key
Key-Type: DSA
Key-Length: 1024
Subkey-Type: ELG-E
Subkey-Length: 2048
Name-Real: $username
Name-Comment: DIMS PGP KEY [$username]
Name-Email: $email
Expire-Date: 0
%pubring $file.gpg
%secring $file.asc
%commit
		";

		$tmpscript = tempnam("/tmp/", "PGP_");
		file_put_contents($tmpscript, $script);
		echo nl2br(shell_exec("cd \$GNUPGHOME; pwd; gpg --home=\$GNUPGHOME --gen-key --batch $tmpscript 2>&1"));
	}

	/**
	 * Enregistre une clé d'encryptage a partir de la chaine de
	 * caractères fournie.
	 */
	public function addEncryptKey($key) {
		$this->Handler->addencryptkey($key);
	}

	/**
	 * Enregistre une clé de décryptage a partir de la chaine de
	 * caractères et la passphrase fournie.
	 */
	public function addDecryptKey($key, $password) {
		$this->Handler->adddecryptkey($key, $password);
	}

	/**
	 * Charge une clé publique à partir du fichier
	 * passé en paramètres
	 */
	public function setPublicKeyFromFile($file) {
		$this->setPublicKey(file_get_contents($file));
	}

	/**
	 * Decrypte une chaine passée en paramètre et retourne
	 * la chaine décryptée.
	 * @note Une clé publique doit être enregistrée.
	 */
	public function decryptString($data) {
		return $this->Handler->decrypt($data);
	}

	/**
	 * Decrypte une chaine passée en paramètre et retourne
	 * la chaine décryptée.
	 * @note Une clé publique doit être enregistrée.
	 */
	public function decryptFile($fileName) {
		return $this->decryptString(file_get_contents($fileName));
	}

	/**
	 * Encrypte une chaine passée en paramètre et retourne
	 * la chaine encryptée.
	 * @note Une clé publique doit être enregistrée.
	 */
	public function encryptString($str) {
		return $this->Handler->encrypt($str);
	}

	/**
	 * Encrypte un fichier passé en paramètres et retourne
	 * les données encryptées.
	 * @note Une clé publique doit être enregistrée.
	 */
	public function encryptFile($fileName) {
		return $this->encryptString(file_get_contents($fileName));
	}

	/**
	 * Encrypte un fichier dont le chemin est passé en paramètre
	 * ($input) et écrit le fichier encrypté au chemin spécifié
	 * dans $output.
	 * @note Une clé publique doit être enregistrée
	 */
	public function encryptAndSaveFile($input, $output) {
		file_put_contents($output, $this->encryptFile($input));
	}
}
