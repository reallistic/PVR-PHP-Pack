<?php
session_start();
ob_start();
$config = false;
$indexers = false;
require_once("../bootstrap.php");
$fnm = explode("/",__FILE__);
$fnm = $fnm[-1];
if(class_exists(CONFIG)){
	$config = true;
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		if(!$at->checkToken() || !isset($_POST['t'])){
			LOG::error(__FILE__." Line[".__LINE__."]"." AUTH|SCRIPT attempt to access a script without permission");
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
						LOG::info(__FILE__." Line[".__LINE__."]"." Added index site $n");
					}
					elseif($mthd == "delete"){
						$is = INDEXSITE::withID($ind);
						if($is instanceof INDEXSITE){
							$_SESSION['response']=$is->delSite();							
							LOG::info(__FILE__." Line[".__LINE__."]"." deleted index site with id $ind");
						}
						else{
							LOG::error(__FILE__." Line[".__LINE__."]"." indexsite not properly stored");
						}
					}
					elseif($mthd == "edit"){
						$is = INDEXSITE::withID($ind);
						if($is instanceof INDEXSITE){
							$_SESSION['response']=$is->delSite();
							$is = new INDEXSITE($n, $a, $u, $ind, $enabled);
							$is->saveSite();
							LOG::info(__FILE__." Line[".__LINE__."]"." Changed index site $n with id $ind");
						}
						else{
							LOG::error(__FILE__." Line[".__LINE__."]"." indexsite not properly stored");
						}
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						header("location: logout.php");
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
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed sabnzbd config");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						header("location: logout.php");
					}
				break;
				case "credentials":
					if($mthd == "edit"){
						$_SESSION['response'] = $at->changeAuth($nu, $np);
						$_SESSION['authtoken'] = serialize($at);
						LOG::info(__FILE__." Line[".__LINE__."]"." Changed login credentials");
					}
					else{
						LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
						header("location: logout.php");
					}
				break;
				default:
					LOG::error(__FILE__." Line[".__LINE__."]"." SCRIPT attempt to access a script without proper post");
					header("location: logout.php");
				break;
			}
		}
	}
	else{
		LOG::error(__FILE__." Line[".__LINE__."]"." AUTH|SCRIPT attempt to access a script without permission");
		header("location: logout.php");
	}
}
$url = $root.CONFIG::$MGMT;
header("location: $url");
ob_end_clean();
exit;
?>