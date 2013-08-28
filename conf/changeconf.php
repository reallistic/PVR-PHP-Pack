<?php
session_start();
ob_start();
$config = false;
$indexers = false;
require_once("../bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		if(!$at->checkToken() || !isset($_POST['t'])){
			header("location: logout.php");
		}
		else{
			$t = CONFIG::escape_query($_POST['t']);
			$n = CONFIG::escape_query($_POST['name']);
			$u = CONFIG::escape_query($_POST['url']);
			$a = CONFIG::escape_query($_POST['apikey']);
			$nu = CONFIG::escape_query($_POST['usr']);
			$np = CONFIG::escape_query($_POST['pwd']);
			$ind = intval(CONFIG::escape_query($_POST['index']));
			$mthd = CONFIG::escape_query($_POST['method']);
			$port = CONFIG::escape_query($_POST['port']);
			$https = (CONFIG::escape_query($_POST['https']) == "true");
			$enabled = (CONFIG::escape_query($_POST['enabled']) == "true");
			$category = CONFIG::escape_query($_POST['cat']);
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
				case "credentials":
					if($mthd == "edit"){
						$_SESSION['response'] = $at->changeAuth($nu, $np);
						$_SESSION['authtoken'] = serialize($at);
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
$url = $root.CONFIG::$MGMT;
header("location: $url");
ob_end_clean();
exit;
?>