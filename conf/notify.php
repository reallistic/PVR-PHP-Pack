<?php
session_start();
ob_start();
if(is_file("../conf/config.php")){
	require("../conf/config.php");
	if(isset($_REQUEST['link']) && isset($_REQUEST['name']) && isset($_REQUEST['method'])){
		$l = urlencode($_REQUEST['link']);
		$n = urlencode($_REQUEST['name']);
		$md = escape_query($_REQUEST['method']);
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
header("location: ../request/");
ob_end_clean();
exit;
?>