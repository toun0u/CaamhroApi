<?php
class UserController extends APIController{

	public function authUser() {
		//$pwd = hashgen('toto', 'efc4c939cdf18de5ba2660c40dc82244');
		//die($pwd);
		$post = json_decode(file_get_contents("php://input"), true);
		$login = $post['Login'];
		$password = $post['Password'];
		
		//$login = $this->app->request->post("Login");
		//$password = $this->app->request->post("Password");
		$ok = false;
		if(!empty($login) && !empty($password)){
			$db = dims::getInstance()->getDb();
			/*$sel = 'SELECT          rc.*, u.salt as usalt, u.password as upassword
			FROM            '.reservation_compte::TABLE_NAME.' rc
			INNER JOIN      '.user::TABLE_NAME.' u
			ON                      u.id = rc.id_user_link
			WHERE           u.login = :login
			AND                     (u.status = 1 OR u.status IS NULL)
			AND             (u.deleted = 0 OR u.deleted IS NULL)
			AND             rc.state = '.reservation_compte::COMPTE_ACTIF.'
			AND             (rc.deleted = 0 OR rc.deleted IS NULL)
			LIMIT           0,1';

			SELECT          u.salt as usalt, u.password as upassword*/
			$sel = 'SELECT      *, u.salt as usalt, u.password as upassword
				FROM            '.user::TABLE_NAME.' u
				WHERE           u.login = :login
				LIMIT           0,1';
			$params = array(
				':login' => $login,
			);
			$res = $db->query($sel,$params);
			if($r = $db->fetchrow($res)){
				$upassword = rtrim($r['upassword']);
				//if($upassword === hashgen($password,$r['usalt'])){
				if($upassword === md5($login)){
					$ok = true;
					unset($r['upassword']);
					unset($r['usalt']);
					$compte = new user();
					$compte->openFromResultSet($r);
					if($compte->get('token') == ''){
						$compte->set('token',uniqid('',true));
						$compte->set('token_create',date('Y-m-d H:i:s'));
					}elseif(strtotime($compte->get('token_create')) >= strtotime("- "._TOKEN_LIFE_MINUTE."minutes")){
					   $compte->set('token_create',date('Y-m-d H:i:s')); // on refresh le timer
					}else{
						$compte->set('token',uniqid('',true));
						$compte->set('token_create',date('Y-m-d H:i:s'));
					}
					$compte->save();
					echo json_encode(
						array(
							'status'=> array(
							'statusCode' => 200,
							'statusMessage' => 'Successful request',
						),
						'id' => $compte->get('id'),
						'token' => $compte->get('token'),
						)
					);
				}
			}
		}
		if(!$ok){
			message403($this->app,40302);

		}
	}

	public function unauthUser(){
		$token = $this->app->request->get('Token');

		$ok = false;
		if(!empty($token)){
			$compte = user::find_by(array('token'=>$token),null,1);
			if(!empty($compte)){
				echo 'ya un user';
				$ok = true;
				$compte->set('token',null);
				$compte->set('token_create','0000-00-00 00:00:00');
				$compte->save();
				echo json_encode(
					array(
						'status'=> array(
							'statusCode' => 200,
							'statusMessage' => 'Successful request',
						),
					)
				);
			}
		}

		if(!$ok){
			message403($this->app,40303);
		}
	}
}
?>