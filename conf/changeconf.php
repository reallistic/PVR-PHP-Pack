<?php
session_start();
ob_start();
$config = false;
$indexers = false;
if(is_file("../conf/config.php")){
	$config = true;
	require("../conf/config.php");
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		if(!$at->checkToken() || !isset($_POST['t'])){
			header("location: logout.php");
		}
		else{
			$t = escape_query($_POST['t']);
			$n = escape_query($_POST['name']);
			$u = escape_query($_POST['url']);
			$a = escape_query($_POST['apikey']);
			$ind = intval(escape_query($_POST['index']));
			$mthd = escape_query($_POST['method']);
			$port = escape_query($_POST['port']);
			$https = (escape_query($_POST['https']) == "true");
			$enabled = (escape_query($_POST['enabled']) == "true");
			$category = escape_query($_POST['cat']);
			$error = false;
			switch($t){
				case "indexsite":
					if($mthd == "add"){
						//check if added indexsite already exists
						$is = new INDEXSITE($n, $a, $u, $ind, $enabled);
						$is->saveSite();
					}
					elseif($mthd == "delete"){
						$is = INDEXSITE::withID($ind);
						if($is instanceof INDEXSITE){
							$_SESSION['response']=$is->delSite();
							//print_r($_SESSION['response']);
						}
						else{
							//print_r($is);
						}
					}
					elseif($mthd == "edit"){
						$is = INDEXSITE::withID($ind);
						if($is instanceof INDEXSITE){
							$_SESSION['response']=$is->delSite();
							$is = new INDEXSITE($n, $a, $u, $ind, $enabled);
							$is->saveSite();
						}
					}
				break;
				case "sabnzbd":
					if($mthd == "edit"){
						
						$s = array(
							"server"=>$u,
							"apikey"=>$a,
							"port"=>$port,
							"category"=>$category,
							"enabled"=>$enabled,
							"https"=>$https
						);
						$conf = new CONFIG;
						$conf->saveSabConfig($s);
					}
				break;
				default:
					$error = true;
				break;
			}
		}
	}
	else{
		$error = true;
	}
}
header("location: ../manage/");
ob_end_clean();
exit;
?>