<?php
session_start();
ob_start();
require_once("../bootstrap.php");
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
if(class_exists(CONFIG)){
	if(isset($_REQUEST['link']) && isset($_REQUEST['name']) && isset($_REQUEST['method'])){
		$l = urlencode($_REQUEST['link']);
		$n = urlencode($_REQUEST['name']);
		$md = CONFIG::escape_query($_REQUEST['method']);
		$conf = new CONFIG;
		if($md == "sabnzbd"){			
			$resp = $conf->sendToSab($l, $n);
			$_SESSION['response'] = $resp;
		}
		elseif($md == "email"){
			echo $conf->sendToMail($l, $n);
		}
		else{
			LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			header("location: $url");

		}
	}
	else{
		LOG::error(__FILE__." Line[".__LINE__."]"." AUTH|SCRIPT attempt to access a script without permission");
		$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
		header("location: $url");

	}
}
$url = $root.CONFIG::$REQ;
header("location: $url");
ob_end_clean();
exit;
?>