<?php
session_start();
ob_start();
require_once("../bootstrap.php");
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
			$_SESSION['response']="No Method";
		}
	}
	else{
		$_SESSION['response']="Error";
	}
}
$url = $root.CONFIG::$REQ;
header("location: $url");
ob_end_clean();
exit;
?>