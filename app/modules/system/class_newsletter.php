<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class newsletter extends dims_data_object {
	const TABLE_NAME = 'dims_mod_newsletter';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	function save() {
		if($this->new) {
			$this->fields['id_user_create'] = $_SESSION['dims']['userid'];
			$this->fields['timestp_create'] = date("YmdHis");
		}
		$this->fields['id_user_modif'] = $_SESSION['dims']['userid'];
		$this->fields['timestp_modif'] = date("YmdHis");
		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		parent::delete();
	}

	function getContent($id_envoi) {
		$db = dims::getInstance()->getDb();

		$sql_sch = 'SELECT * FROM dims_mod_newsletter_content WHERE id_newsletter= :idnewsletter AND id = :idenvoie';
		$res_sch = $db->query($sql_sch, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':idenvoie' => array('type' => PDO::PARAM_INT, 'value' => $id_envoi),
		));

		$tab_content = $db->fetchrow($res_sch);

		return $tab_content;
	}

	function getNbSending() {

		$nbsend=0;
		//recuperation des donnees
		$sql = 'SELECT	distinct n.*,
				count(distinct c.id) as nb_article
			FROM	dims_mod_newsletter n
			LEFT JOIN	dims_mod_newsletter_content c
			ON		c.id_newsletter = n.id
			WHERE	n.id= :idnewsletter
			GROUP by	n.id';

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0) {
			   $nbsend=$f['nb_article'];
			}
		}
		return $nbsend;
	}

	function getNbTag() {

		$nbsend=0;
		//recuperation des donnees
		$sql = 'SELECT
				count(distinct t.id_tag) as nb_tag
			FROM	dims_mod_newsletter_tag as t
			WHERE	t.id_newsletter= :idnewsletter
			';

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		if($this->db->numrows($res_ml)) {
			$f = $this->db->fetchrow($res_ml);
			$nbsend=$f['nb_tag'];
		}

		return $nbsend;
	}

	/*
	 * Récupération de la liste des tags
	 */
	function getSelectedTags() {

		$list=array();

		//recuperation des donnees
		$sql = 'SELECT	nt.*,t.tag
			FROM	dims_mod_newsletter_tag  as nt
			inner join	dims_tag as t
			on		t.id=nt.id_tag
			WHERE	nt.id_newsletter= :idnewsletter
			';

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
		   $list[$f['id_tag']]=$f;
		}

		return $list;
	}


	function getAllTags() {

		$list=array();

		//recuperation des donnees
		$sql = 'SELECT      *
			FROM        dims_tag
			WHERE       type < 3
						AND         id_workspace=:idworkspace
			order by    tag';

				$res_ml = $this->db->query($sql, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_workspace']),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
		   $list[$f['id']]=$f;
		}

		return $list;
	}

	/*
	 *
	 */
	function attachContactsByTag($id_tag = '', $filtername = '') {
		$list=array();

		//recuperation des donnees
		if ($id_tag>0) {
		$sql = "SELECT		distinct c.id,c.email,c.email2,c.firstname,c.lastname,tg.timestp_modify
			FROM		dims_mod_business_contact as c

			INNER	JOIN	dims_tag_globalobject as tg
			ON			c.id_globalobject=tg.id_globalobject
			WHERE		tg.id_tag=:idtag and c.email <>''";
			$params[':idtag'] = array('type' => PDO::PARAM_INT, 'value' => $id_tag);
		} else {
			// on prend toutes les personnes distinctes des tags
			$sql = 'SELECT		distinct c.id,c.email,c.email2,c.firstname,c.lastname,tg.timestp_modify
				FROM		dims_mod_business_contact as c

				INNER	JOIN	dims_tag_globalobject as tg
				ON		c.id_globalobject=tg.id_globalobject
				INNER JOIN	dims_mod_newsletter_tag as nt
				ON		nt.id_tag=tg.id_tag
				AND		nt.id_newsletter=:idnewsletter
				';
			if ($filtername!='') {
				$sql.= " and (c.lastname like :filtername or c.firstname like :filtername or c.email like :filtername)";
				$params[':filtername'] = array('type' => PDO::PARAM_INT, 'value' => '%'.$filtername.'%');
			}
			$params[':idnewsletter'] = array('type' => PDO::PARAM_INT, 'value' => $this->getId());
		}

		// liste
		$res_ml = $this->db->query($sql, $params);

		while ($f=$this->db->fetchrow($res_ml)) {
		   $list[$f['id']]=$f;

		//dims_mod_newsletter_subscribed
		}

		return $list;
	}

	/*
	 * Fonction permettant la récupération de l'ensemble des personnes concernees par la newsletter
	 */
	function getNbRegistration() {
		$arraynb=array();
		$sql = 'SELECT	distinct n.*,
				count(distinct s.id_contact) as nb_sub,
				count(distinct i.id) as nb_inscr
			FROM	dims_mod_newsletter n
			LEFT JOIN	dims_mod_newsletter_subscribed s
			ON		s.id_newsletter = n.id
			AND		s.etat = 1
			LEFT JOIN	dims_mod_newsletter_inscription i
			ON		i.id_newsletter = n.id
			WHERE	n.id= :idnewsletter';

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0) {
				$arraynb['nbsub']=$f['nb_sub'];
				$arraynb['nbinscr']=$f['nb_inscr'];
			}
		}

		// on regarde dans les mailinglist attaches
		$sqlct = '	SELECT		count(ct.id) as nbct
			FROM	dims_mod_newsletter_mailing_ct ct
			INNER JOIN	dims_mod_newsletter_mailing_news mn
			ON		mn.id_mailing = ct.id_mailing
			AND		mn.id_newsletter = :idnewsletter
			WHERE	ct.actif =1';

		$resct = $this->db->query($sqlct, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if($this->db->numrows($resct) > 0) {
			while($f = $this->db->fetchrow($resct)) {
				$arraynb['nbinml']=$f['nbct'];
			}
		}
		return $arraynb;
	}

	/*
	 * fonction permettant la collecte des newsletters
	 */
	function getNewsletters() {
		$registration=array();
		$registration['all']=array();
		$registration['notsent']=array();
		$registration['sent']=array();

		$sql = 'SELECT	distinct c.*
			FROM	dims_mod_newsletter_content as c
			WHERE	id_newsletter= :idnewsletter
			ORDER BY	date_create desc';

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
		if ($f['id']>0) {
			$registration['all'][]=$f;
			if ($f['date_envoi']=='') {
				$registration['notsent'][]=$f;
			}else {
				$registration['sent'][]=$f;
			}
		}
		}
		return $registration;
	}
	/*
	 * fonction permettant la collecte des nouvelles demandes
	 */
	function getNewRegistration() {
		$registration=array();

		$sql = 'SELECT	distinct i.*
			FROM	dims_mod_newsletter_inscription i
			WHERE	id_newsletter= :idnewsletter';

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0) {
				$registration[]=$f;
			}
		}
		return $registration;
	}

	/**
	 *
	 */
	public function getAllEmailRegistration($onlyactives = false) {
		$tabemail=array();
		$doublons=array();
		$list_ct='';
		$sql_sub = 'SELECT		DISTINCT cl.email, cl.id, u.login, subscribedoptions.nomail
					FROM		dims_mod_business_contact ct
					INNER JOIN	dims_mod_newsletter_subscribed sub
					ON			sub.id_contact = ct.id
					AND			sub.id_newsletter = :idnewsletter
					INNER JOIN	dims_mod_business_contact_layer cl
					ON			cl.id = ct.id
					AND			cl.type_layer = 1
					LEFT JOIN	dims_user u
					ON			u.id_contact = ct.id
					LEFT JOIN   '.newsletter_subscribed_options::TABLE_NAME.' subscribedoptions
					ON          subscribedoptions.id_mailinglist = :idnewsletter
					AND         subscribedoptions.id_subscribeduser = u.id
					WHERE       (sub.date_desinscription = ""
					OR          sub.date_desinscription IS NULL)';

		$res_sub = $this->db->query($sql_sub, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
		));

		if($this->db->numrows($res_sub)) {
			while($tab_sub = $this->db->fetchrow($res_sub)) {
				if($tab_sub['email'] != '' && (!$onlyactives || !$tab_sub['nomail'])) {
					$tab_sub['email'] = strtolower($tab_sub['email']);
					$nom_compare=strtolower($tab_sub['email']);
					if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
						if($tab_sub['login'] == '') {
							//si le contact n'est pas un user il doit pouvoir se désinscrire via l'email
							$list_email['contact'][$tab_sub['id']] = $tab_sub['email'];
						}
						else {
							$list_email[$tab_sub['id']] = $tab_sub['email'];
						}
						$list_ct .= ' ,'.$tab_sub['id'];

						$tabemail[$nom_compare]=$nom_compare;
					}
					else {
						if (!isset($doublons[$tab_sub['email']])) $doublons[$tab_sub['email']]=1;
						$doublons[$tab_sub['email']]++;
					}
				}
			}
		}

		//2
		$sql_mail = 'SELECT		ct.email, ct.id
					FROM		dims_mod_newsletter_mailing_ct ct
					INNER JOIN	dims_mod_newsletter_mailing_news mn
					ON			mn.id_mailing = ct.id_mailing
					AND			mn.id_newsletter = :idnewsletter
					WHERE		ct.actif = 1';
		$res_mail = $this->db->query($sql_mail, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']),
		));

		if($this->db->numrows($res_mail)) {
			while($tab_mail = $this->db->fetchrow($res_mail)) {
				$tab_mail['email'] = strtolower($tab_mail['email']);
				$nom_compare=strtolower($tab_mail['email']);
				if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
					$list_email['mailing'][$tab_mail['id']] = $tab_mail['email'];
					$tabemail[$nom_compare]=$nom_compare;
				}
				else {
					if (!isset($doublons[$tab_mail['email']])) $doublons[$tab_mail['email']]=1;
					$doublons[$tab_mail['email']]++;
				}
			}
		}

		//3
		$params = array();
		$sql_sub = 'SELECT		DISTINCT ct.email, ct.id, subscribedoptions.nomail
					FROM		dims_mod_business_contact ct
					INNER JOIN	dims_mod_newsletter_subscribed sub
					ON			sub.id_contact = ct.id
					LEFT JOIN	dims_user u
					ON			u.id_contact = ct.id
					LEFT JOIN   '.newsletter_subscribed_options::TABLE_NAME.' subscribedoptions
					ON          subscribedoptions.id_mailinglist = :idnewsletter
					WHERE       (sub.date_desinscription = ""
					OR          sub.date_desinscription IS NULL)
					AND			sub.id_newsletter = :idnewsletter
					AND			ct.id NOT IN ('.$this->db->getParamsFromArray(explode(',', $list_ct), 'listct', $params).')';
		$params[':idnewsletter'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields['id']);

		$res_sub = $this->db->query($sql_sub, $params);

		if($this->db->numrows($res_sub)) {
			while($tab_sub = $this->db->fetchrow($res_sub)) {
				if(!$onlyactives || !$tab_sub['nomail']) {
					$tab_sub['email'] = strtolower($tab_sub['email']);
					$nom_compare=strtolower($tab_sub['email']);
					if (!isset($tabemail[$nom_compare])) { // controle de l'existence de l'email
						$list_email[$tab_sub['id']] = $tab_sub['email'];
						$tabemail[$nom_compare]=$nom_compare;
					}
					else {
						if (!isset($doublons[$tab_sub['email']])) $doublons[$tab_sub['email']]=1;
						$doublons[$tab_sub['email']]++;
					}
				}
			}
		}

		return $tabemail;
	}

	/*
	 * fonction permettant la collecte de l'ensemble des informations
	 */
	function getAllregistration($sqlfilter='',$sql2='',$sqlfiltername='',$sqlfiltername2='') {
		$registration=array();

		$sql = 'SELECT	distinct ct.id,ct.firstname,ct.lastname,ct.email,s.*
			FROM	dims_mod_newsletter_subscribed s
			INNER JOIN	dims_mod_business_contact as ct
			ON		ct.id = s.id_contact
			WHERE	s.id_newsletter= :idnewsletter'.$sqlfiltername.'
			'.$sqlfilter;

		$res_ml = $this->db->query($sql, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0) {
				$registration['ct'][]=$f;
			}
		}

		// on regarde dans les mailinglist attaches
		$sqlct = '	SELECT		ct.*
			FROM	dims_mod_newsletter_mailing_ct ct
			INNER JOIN	dims_mod_newsletter_mailing_news mn
			ON		mn.id_mailing = ct.id_mailing
			AND		mn.id_newsletter = :idnewsletter'.$sqlfiltername2.'
			WHERE	ct.actif =1 '.$sql2;

		$resct = $this->db->query($sqlct, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));
		if($this->db->numrows($resct) > 0) {
			while($f = $this->db->fetchrow($resct)) {
				$registration['ml'][]=$f;
			}
		}
		return $registration;
	}

	/*
	 * Fonction permettant la récupération de l'ensemble des mailing lists concernees par la newsletter
	 */
	function getMailinglist() {
		$arraymailing=array();

		$sql_ml = ' SELECT		distinct ml.*,
			count(distinct ct.id) as id_nb_ct,
			mn.id as id_link
			FROM		dims_mod_newsletter_mailing_list ml
			INNER JOIN	dims_mod_newsletter_mailing_news mn
			ON		mn.id_mailing = ml.id
			AND		mn.id_newsletter = :idnewsletter
			LEFT JOIN  dims_mod_newsletter_mailing_ct ct
			ON		ct.id_mailing = ml.id
			AND		ct.actif = 1';

		$res_ml = $this->db->query($sql_ml, array(
			':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
		));

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0)
				$arraymailing[$f['id']]=$f;
		}

		return $arraymailing;
	}

	/*
	 * Fonction permettant la récupération de l'ensemble des mailing lists concernees par la newsletter
	 */
	function getAllMailinglist($listworkspace_nl) {
		$arraymailing=array();

		$sql_ml = ' SELECT		distinct ml.*,
			count(distinct ct.id) as id_nb_ct,
			mn.id as id_link
			FROM		dims_mod_newsletter_mailing_list ml
			LEFT JOIN	dims_mod_newsletter_mailing_news mn
			ON		mn.id_mailing = ml.id
			LEFT JOIN  dims_mod_newsletter_mailing_ct ct
			ON		ct.id_mailing = ml.id
			AND		ct.actif = 1
			WHERE		ml.id_workspace in ('.$listworkspace_nl.')
			GROUP BY	ml.id';

		$res_ml = $this->db->query($sql_ml);

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0)
				$arraymailing[$f['id']]=$f;
		}

		return $arraymailing;
	}

	/*
	 * Fonction permettant la récupération de l'ensemble des groupes mailing lists concernees par la newsletter
	 */
	function getAllGroupMailinglist($listworkspace_nl) {
		$arraymailing=array();

		$sql_ml = ' SELECT		distinct ml.id,
					mn.id_newsletter,
					m.label
			FROM		dims_mod_newsletter_mailing_list ml
			INNER JOIN	dims_mod_newsletter_mailing_news mn
			ON		mn.id_mailing = ml.id
			INNER JOIN	dims_mod_newsletter as m
			ON		m.id = mn.id_newsletter
			WHERE		ml.id_workspace in ('.$listworkspace_nl.')
			';


		$res_ml = $this->db->query($sql_ml);

		while ($f=$this->db->fetchrow($res_ml)) {
			if ($f['id']>0)
				$arraymailing[$f['id']][$f['id_newsletter']]=$f['label'];
		}

		return $arraymailing;
	}

	function getRequests() {

	}
}
