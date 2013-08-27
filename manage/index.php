<?php
session_start();
$config = false;
$indexers = false;
$indexersprop = false;
if(is_file("../conf/config.php")){
	$config = true;
	require("../conf/config.php");
	if(isset($_POST['usr']) && isset($_POST['pwd'])){
		$u = escape_query($_POST['usr']);
		$p = escape_query($_POST['pwd']);
		$at = new AUTH($u,$p);
		if($at->info[0]){
			$_SESSION['authtoken'] = serialize($at);
		}
	}
	elseif(isset($_POST['cpwd']) && isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		$p = escape_query($_POST['cpwd']);
		$at->confirm($p);
	}
	elseif(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		if(!$at->checkToken() && $at->info[1] !="confirm"){
			header("location: logout.php");
		}
		elseif($at->checkToken()){
			if(is_file("../conf/indexsites.db")){
				$indexers = true; //check for indexers was good
				$inxs = file_get_contents("../conf/indexsites.db");
				$inxs = explode("\r\n",$inxs);
				$indexsites=array();
				$indexersprop = true; //check for indexsites class was good
				for( $i=0; $i<count($inxs)-1; $i++){
					$inx = $inxs[$i];
					$indexsite = unserialize($inx);
					if(! $indexsite instanceof INDEXSITE){
						$indexersprop = false; //cancel that, found an improperly set indexsite
						$indexsite = NULL;
						$indexsites=array();
						break;
					}
					else{
						array_push($indexsites,$indexsite);
					}
					$indexsite = NULL;
				}
			}
			$error = false;
		}
	}
	else{
		$error = true;
	}
	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Plexcloud - Music | Manage</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet" type="text/css"></link>
<link href="../conf/style.css" rel="stylesheet" type="text/css"></link>
<script>
  $(function() {
    $( "#dialog" ).dialog();
  });
  $(function() {
		$("input[type=submit], button, a.button" )
		  .button();
	 });
</script>
</head>

<body>
<?php 
if($at ==NULL || (!$at->checkToken() && $at->info[1]!="confirm")){ ?>
	<div id="dialog" title="Login">
	  <form method="post" enctype="multipart/form-data">
      		<label>Username:<br />
                <input type="text" name="usr" value="plexcloud" />
            </label>
            <br />
            <label>Password:<br />
                <input type="password" name="pwd" value="administrator" />
            </label>
                <br />
                <input type="submit" value="Login" />
      </form>
	</div> <?php
}
elseif(isset($at) && $at->info[1]=="confirm"){ ?>
	<div id="dialog" title="Confirm new credentials">
	  <form method="post" enctype="multipart/form-data">
      		<label>Username:<br />
                <input disabled type="text" name="usr" value="<?php echo $at->getUsername(); ?>" />
            </label>
            <br />
            <label>Confirm Password:<br />
                <input type="password" name="cpwd" value="" />
            </label>
                <br />
                <input type="submit" value="Login" />
      </form>
	</div> <?php
} 
else{
	include("settings.php");
} ?>
<div id="info">
<?php
	if($config === false){
		echo "<h3>Improper installation. Missing config.php</h3>";
	}
echo "Info: ";
print_r($at->info);
echo "<br>error: ";
print_r($error);
echo "<br>indexers: ";
print_r($indexers);
echo "<br>indexersprop: ";
print_r($indexersprop);
echo "<br>Indexers count: ".count($indexsites);
echo "<br>Token check: ".$at->checkToken();
echo "<br>inxs: ";
print_r($inxs);
?>
</div>
</body>
</html>