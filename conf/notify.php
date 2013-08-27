<?php
if(is_file("../conf/config.php")){
	require("../conf/config.php");
	if(isset($_REQUEST['link']) && isset($_REQUEST['name']) && isset($_REQUEST['method'])){
		$l = escape_query($_REQUEST['link']);
		$n = escape_query($_REQUEST['name']);
		$md = escape_query($_REQUEST['method']);
		$conf = new CONFIG;
		if($md == "sab"){			
			echo $conf->sendToSab($l, $n);
		}
		elseif($md == "email"){
			echo $conf->sendToMail($l, $n);
		}
	}
	else{
		echo "Error";
	}
}
exit;
?>