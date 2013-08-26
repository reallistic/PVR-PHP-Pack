<?php
session_start();
$config = false;
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
			//logged in successfully
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
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet"></link>
<script>
  $(function() {
    $( "#dialog" ).dialog();
  });
</script>
<style type="text/css">

</style>
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
else{ print_r($at->info);?>
	<h3> Logged in</h3><?php
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
echo "<br>post: ";
print_r($_POST);
echo "<br>Token check: ".$at->checkToken();
?>
</div>
</body>
</html>