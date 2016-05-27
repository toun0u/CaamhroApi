<?php
final class dims_debug {

	private static $memory	= array();

	public function __construct () {
		if($_GET['session'] == 'delete'){
			if (isset($_COOKIE[session_name()])) {
				setcookie(session_name(), '', time()-42000, '/');
			}

			//Supprime les données de session du serveur.
			session_destroy();
			unset($_SESSION);
		}
	}

	public static function css () {
		if (self::getDebug()) {
		?>
        <style type="text/css">
			#debug {
				background-color:#f3f3f3;
				border-top:1px solid #dcdcdc;
				color:#000;
				line-height:25px;
				width:100%;
				position:absolute;
				bottom:0px;
				max-height:20%;
				overflow:auto;
			}
			#debug select, #debug input {
				font-size:10px;
			}
			#debug pre {
				line-height:12px;
			}
			#debug .printr {
				border:1px dashed #dcdcdc;
				padding:0 10px;
			}
		</style>
        <?php
		}
	}

	public static function display () {
		if (self::getDebug()) {
		?>
		<div id="debug" style="z-index:800;">
		<div style="float:right; margin-right:5px;">Mon IP : <?=_DIMS_IP_CLIENT; ?></div>
            &nbsp;&nbsp; <strong>Debug Tools Bar</strong> |
            <select id="values" onchange="javascript: $('#'+$('#values option:selected').attr('value')).css('display', 'block');">
                <option value="null" selected="selected">print_r()</option>
                <option value="print_get">$_GET</option>
                <option value="print_post">$_POST</option>
                <option value="print_server">$_SERVER</option>
                <option value="print_session">$_SESSION</option>
                <option value="print_custom">My Elements</option>
                <option value="print_sql">Requet MySQL</option>
            </select> |
            <input type="button" value="Vider les sessions" onclick="javascript:document.location.href='<?=$_SERVER['PHP_SELF']; ?>?session=delete';" />
            <div class="printr" id="print_get" style="display:none;">
                <b onclick="javascript:$('#print_get').css('display', 'none');" style="float:right;cursor:pointer;">[close]</b><b>print_r($_GET);</b>
				<?php dims_print_r($_GET); ?>
            </div>
            <div class="printr" id="print_post" style="display:none;">
                <b onclick="javascript:$('#print_post').css('display', 'none');" style="float:right;cursor:pointer;">[close]</b><b>print_r($_POST);</b>
				<?php dims_print_r($_POST); ?>
            </div>
            <div class="printr" id="print_server" style="display:none;">
                <b onclick="javascript:$('#print_server').css('display', 'none');" style="float:right;cursor:pointer;">[close]</b><b>print_r($_SERVER);</b>
				<?php
                $_SERVER['SERVER_SIGNATURE']	= htmlspecialchars($_SERVER['SERVER_SIGNATURE']);
				dims_print_r($_SERVER);
				?>
            </div>
            <div class="printr" id="print_session" style="display:none;">
                <b onclick="javascript:$('#print_session').css('display', 'none');" style="float:right;cursor:pointer;">[close]</b><b>print_r($_SESSION);</b>
				<?php dims_print_r($_SESSION); ?>
            </div>
            <div class="printr" id="print_custom" style="display:none;">
                <b onclick="javascript:$('#print_custom').css('display', 'none');" style="float:right;cursor:pointer;">[close]</b>
                <?php
				if (isset($memory["element"])) {
					foreach(self::$memory["element"] as $key => $value){
						echo '<b>print_r('.$key.');</b>';
						dims_print_r($value);
					}
				}
				?>
            </div>
            <div class="printr" id="print_sql" style="display:block;">
                <b onclick="javascript:$('#print_sql').css('display', 'none');" style="float:right;cursor:pointer;">[close]</b>
                <?php
                $time	= 0;
				foreach(self::$memory["sql"] as $value){
					echo "<b>Requete (".$value["time"]."ms) : </b>".$value["requet"]."<br />";
					$time	+= $value["time"];

				}

				print "Total : ".count(self::$memory["sql"])." en ".$time."ms";
				?>
            </div>
       	</div>
        <?php
		}
	}

	public static function setElement($nom, $array) {
		self::$memory["element"][$nom]	= $array;
	}

	public static function setRequet($array) {
		self::$memory["sql"][]	= $array;
	}

	private static function getDebug(){
		if (_DIMS_DEBUGMODE || (_DIMS_DEBUGMODE_PROD && in_array(_DIMS_IP_CLIENT, $_SESSION["dims_debug"]["ip"]))) {
			return true;
		}

		return false;
	}
}
?>
