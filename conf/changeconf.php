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
			$error = false;
			switch($t){
				case "indexsite":
					if($mthd == "add"){
						//echo "apikey is ".$a;
						$is = new INDEXSITE($n, $a, $u, $ind);
						$is->saveSite();
					}
					elseif($mthd == "delete"){
						$is = INDEXSITE::withID($ind);
						if($is instanceof INDEXSITE){
							$_SESSION['response']=$is->delSite();
							//echo "in delete<br>";
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
							$is = new INDEXSITE($n, $a, $u, $ind);
							$is->saveSite();
						}
					}
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