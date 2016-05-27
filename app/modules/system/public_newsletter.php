<?php
switch($dims_op) {
    case 'newsletters_xsd':
        header("Content-type: text/xml");
        echo '<?xml version="1.0"?>
                <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">

                <xs:element name="event">
                  <xs:complexType>
                    <xs:sequence>
                      <xs:element name="id" type="xs:numeric"/>
                      <xs:element name="title" type="xs:string"/>
                      <xs:element name="descriptif" type="xs:descriptif"/>
                      <xs:element name="lastname" type="xs:string"/>
                      <xs:element name="firstname" type="xs:string"/>
                      <xs:element name="url" type="xs:string"/>
                    </xs:sequence>
                  </xs:complexType>
                </xs:element>

                </xs:schema>';
                die();
    break;

	case 'newsletter':
    default:
        $sql = 'SELECT
                    n.id AS id_nl,
                    n.label,
                    n.descriptif,
                    u.id AS id_user,
                    u.lastname,
                    u.firstname,
                    u.id_contact
                FROM
                    dims_mod_newsletter n
                INNER JOIN
                    dims_user u
                    ON
                        u.id = n.id_user_create
                WHERE
                    n.etat = 1
                ';

        $ressource = $db->query($sql);

        if($db->numrows($ressource))
        {
            $tab_evt = array();

            while($result = $db->fetchrow($ressource)) {
                $tab_info = array();

                $tab_info['id']         = $result['id_nl'];
                $tab_info['title']      = $result['label'];
                $tab_info['descriptif'] = strip_tags($result['descriptif']);
                $tab_info['lastname']   = $result['lastname'];
                $tab_info['firstname']  = $result['firstname'];
				$link=array();
				$link['name']=$result['label'];
				$link['link']=dims_urlencode('http://'.$http_host.'/index.php?id_nl='.$result['id_nl'],true);

                $tab_info['urls'][] = array("url"=> $link);

                $tab_news[] = $tab_info;
            }

            header("Content-type: text/xml");
            echo '<?xml version="1.0" encoding="UTF-8"?>';

            if (sizeof($tab_news)>0) {
                echo '<newsletters>';
                // on construit la balise de tableaux
                foreach($tab_news as $k => $news) {
                    echo '<newsletter>';

                    foreach($news as $k => $elem) {
						echo '<'.$k.'>';

						if (is_array($elem)) {
							foreach($elem as $ki =>$elemi) {
								if (is_array($elemi)) {

									foreach($elemi as $kj =>$elemj) {
										echo '<'.$kj.'>';
										foreach($elemj as $kk => $elemk) {
											echo '<'.$kk.'>';
											echo utf8_encode($elemk);
											echo '</'.$kk.'>';
										}
										echo '</'.$kj.'>';
									}
								}
							}
						}
						else {
							echo utf8_encode($elem);
						}
						echo '</'.$k.'>';
                    }
                    echo '</newsletter>';
                }
                echo '</newsletters>';
            }
        }
        die();
        break;
}
?>
