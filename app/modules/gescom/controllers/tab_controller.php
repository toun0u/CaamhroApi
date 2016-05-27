<?php
$a = dims_load_securvalue('a', dims_const::_DIMS_CHAR_INPUT, true, true);

ob_clean(); // On supprime le code html qui a été généré avant

if(!isset($_SESSION['dims_tabs']))
	$_SESSION['dims_tabs'] = new TabManager;

$manager = $_SESSION['dims_tabs'];

switch($a) {
	default :
		return false;
		break;
	case 'create' :
		$link = dims_load_securvalue('link', dims_const::_DIMS_CHAR_INPUT, true, true);

		$tab = new Tab($link, '');
		// On ne connait pas l'état de l'onglet donc on le recherche par le lien :
		$exist =  $manager->findOneByLink($link);
		// Si il n'existe pas on peut le créer :
		if($exist === false) {

			if(count($manager->get('tabs')) > 0) {
				$currentlyActive = $manager->findOneByState(1);
				$currentlyActive->set('state', 0);
			}

			$tab->set('state', 1);
			$manager->addTab($tab);

		}
		// Si il existe, on met le statut actif sur celui qui existe
		else {
			$tab = $manager->alreadyExist($tab); // On récupere l'onglet existant

			// On remet l'onglet actif en inactif
			if(count($manager->get('tabs')) > 0) {
				foreach ($manager->get('tabs') as $key => $value) {
					$value->set('state', 0);
				}
			}

			$tab->set('state', 1);
		}

		$data = array();
		$data['redirect'] = $link;

		$json = json_encode($data);

		echo $json;
		die; // Et on s'assure qu'il y en a pas après
		break;
	case 'change' :
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);

		$tab = $manager->findOneById($id);

		if(!empty($tab)) {
			// On remet l'onglet actif en inactif
			if(count($manager->get('tabs')) > 0) {
				$currentlyActive = $manager->findOneByState(1);
				$currentlyActive->set('state', 0);
			}
			$tab->set('state', 1);
		}

		$data = array();
		$data['redirect'] = $tab->link;
		$json = json_encode($data);

		echo $json;
		die;
		break;
	case 'update' :
		$link = dims_load_securvalue('link', dims_const::_DIMS_CHAR_INPUT, true, true);

		$current = $manager->findOneByState(1);

		if(!empty($current)) {
			$current->update($link, '', 1);
		}

		$data = array();
		$data['redirect'] = $link;
		$json = json_encode($data);

		echo $json;
		die;
		break;
	case 'remove' :

		$link = dims_load_securvalue('link', dims_const::_DIMS_CHAR_INPUT, true, true);
		$tab = $manager->findOneByLink($link);

		if($tab !== false) {

			$data = array();
			$nbTabs = count($manager->get('tabs'));

			if($nbTabs > 1){
				$manager->removeTab($tab);
				$tabs = $manager->get('tabs');

				// On cherche un onglet actif pour savoir comment rediriger
				$actif = $manager->findOneByState(1);

				if($actif === false) {
					// On prépare le lien vers le dernier onglet ouvert:
					$i = $nbTabs - 2; // -2 : -1 à cause de l'indice qui commence à 0 et encore -1 car on vient de supprimer un onglet
					$link = $tabs[$i]->link;
					$tabs[$i]->set('state', 1);
				}
				else {
					$link = $actif->link;
				}

			}
			else {
				unset($_SESSION['dims_tabs']);
				$link = Gescom\get_path();
			}

			$data = array();
			$data['redirect'] = $link;
			$json = json_encode($data);

			echo $json;
			die;
		}
		else {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		break;
	case 'destroy' :
			unset($_SESSION['dims_tabs']);
			$link = Gescom\get_path();

			$data = array();
			$data['redirect'] = $link;
			$json = json_encode($data);

			echo $json;
			die;
		break;
}