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
			$error = false;
			switch($t){
				case "indexsite":
					$is = new INDEXSITE($n, $a, $u);
					$is->saveSite();
				break;
				default:
					$error = true;
				break;
			}
			/*if(is_file("../conf/indexsites.db")){
				//check if added indexsite already exists
				$indexers = true; //check for indexers was good
				$inxs = file_get_contents("../conf/indexsites.db");
				$inxs = explode("\r\n",$inx);
				$indexsites=array();
				$indexersprop = true; //check for indexsites class was good
				foreach( $inxs as $inx):
					$indexsite = unserialize($inx);
					if(! $indexsite instanceof INDEXSITE){
						$indexersprop = false; //cancel that, found an improperly set indexsite
						$indexsite = NULL;
						break;
					}
					else{
						array_push($indexsites,$indexsite);
					}
					$indexsite = NULL;
				endforeach;
			}*/
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