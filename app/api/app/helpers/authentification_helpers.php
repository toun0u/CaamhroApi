<?php
function auth_client(&$app){
	$method = $app->request->getMethod();
	$ip = $app->request->getIp();
	$user = $app->request->headers('PHP_AUTH_USER');
	$headers = $app->request->headers;
	$pwd = base64_decode($app->request->headers('PHP_AUTH_PW'));
	$db = dims::getInstance()->getDb();
	//die();
	$sel = "SELECT 	*
			FROM 	ws_clients
			WHERE 	label = :label
			AND 	(ip = :ip OR ip = '*')";
	$params = array(
		':label' => $user,
		':ip' => $ip,
	);
	$ok = false;
	$res = $db->query($sel,$params);
	if($db->numrows($res)>0){
		$params = '';
		switch ($method) {
			case 'POST':
				$post = json_decode(file_get_contents("php://input"));
				//$post = $app->request->post();
				foreach($post as $k => $v){
					if (!is_array($v)) {
						$params .= $k.$v;
					}
					else $params .=$k."Array";
				}
				break;
			case 'PUT':
				$put = $app->request->put();
				foreach($put as $k => $v){
					$params .= $k.$v;
				}
				break;
			case 'DELETE':
				$delete = $app->request->get();
				//$delete = $app->request->delete();
				//$delete = $app->request->getBody();
				//$delete = $headers;
				//var_dump($delete);
				foreach($delete as $k => $v){
					if($k == 'Token' || $k ==  'Login' || $k == 'Password'){
						$params .= $k.$v;
						//var_dump($k.$v);
					}
				}
				break;
			default:
			case 'GET':
				$get = $app->request->get();
				//$get = $headers;
				foreach($get as $k => $v){
					//if($k == 'Token' || $k ==  'Login' || $k == 'Password'){
						$params .= $k.$v;
					//}
				}
				break;
		}
		while($r = $db->fetchrow($res)){
			//has_ip_fixe : bypass du contrôle sur l'IP pour OW qui n'est pas capable de connaître son IP au moment de la requête
			$params = $method . ($r['has_ip_fixe'] ? $ip : '') . $params;
			//var_dump($pwd);
			//var_dump(hash_hmac('sha256', utf8_encode($params), $r['uid']));
			//die();
			if(hash_hmac("sha256", utf8_encode($params), $r['uid']) === $pwd){
				$ok = true;
				$_SESSION['dims']['userid'] = $r['id_user'];//c'est pas super joli mais pas le choix avec Dims
				$_SESSION['dims']['moduleid'] = $r['id_module'];//c'est pas super joli mais pas le choix avec Dims
				$_SESSION['dims']['workspaceid'] = $r['id_workspace'];//c'est pas super joli mais pas le choix avec Dims
				dims::getInstance()->init_metabase();
				break;
			}
		}
	}
	if(!$ok){
		$app->halt(401,json_encode(array('status' => array('statusCode' => 401, 'statusMessage' => 'Protected Area', 'usr'=>$user, 'res'=>$db->numrows($res), 'params'=>$params, 'method' => $method))));
	}
}
function validCustomerAuth(&$app, $id = false, $token, $emptyId = true, $sendHalt = true){
    $user = null;
    if($app->request->getPath() !== "/user"){
	    if(!empty($token) && (!empty($id) || $emptyId)){
	    	require_once DIMS_APP_PATH . 'modules/system/class_user.php';
	        if(empty($id) && $emptyId){
	            $user = user::find_by(array('token'=>$token),null,1);
	        }else{
	            $user = user::find_by(array('id'=>$id,'token'=>$token),null,1);
	        }
	        if(!empty($user)){
	            if(strtotime($user->get('token_create')) >= strtotime("- "._TOKEN_LIFE_MINUTE."minutes") /*&& $user->get('state') && (is_null($user->fields['deleted']) || ! $user->get('deleted'))*/ ){
	                $user->set('token_create',date('Y-m-d H:i:s')); // on refresh le timer
	                // update de mise a jour du compte sans faire de traitement additionnel
	                $user->save(true);
	            }else{
	                // on vide le token & le timer
	                $user->set('token',null);
	                $user->set('token_create','0000-00-00 00:00:00');
	                $user->save();
	                $user = null;
	            }
	        }
	    }

	    if(empty($user) && $sendHalt){
	        message403($app,40301);
	    }
	    return $user;
	}
}
//
