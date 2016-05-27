<?php
function hashgen($pwd, $salt){
	return crypt(hash_hmac('sha512',$pwd,CRYPTO_SALT), '$2a$'.CRYPTO_COST.'$'.$salt.'$');
}

function validEmail($email){
	return (preg_match('/^[\w-]+(\.[\w-]+)*(\+[\w-]+)?@([\w-]+\.)+[a-zA-Z]{2,7}$/', $email) !== 1);
}
