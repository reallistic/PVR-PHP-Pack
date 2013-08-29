<?php
session_start();
$config = false;
$indexers = false;
$indexersprop = false;
require_once("../bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
	}
	
	if(isset($at)){
		if($at->checkToken()){
			LOG::getLogs();
		}
		else{
			LOG::error(__FILE__." Line[".__LINE__."]"." AUTH unauthorized attempt to view logs");
			$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
			header("location: $url");
		}
	}
	else{
		LOG::error(__FILE__." Line[".__LINE__."]"." AUTH unauthorized attempt to view logs. No auth token");
		$url = $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT;
		header("location: $url");
	}
}
else{
	$error = true;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Plexcloud - Music | Logs</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet" type="text/css"></link>
<link href="<?php echo $root.CONFIG::$STYLE; ?>" rel="stylesheet" type="text/css"></link>
<script type="text/javascript">
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
<div class="outerHead">
	<div class="head">
    	PlexCloud - Music made easy
    </div>
</div>
<div class="mainCont">
	
<div class="innerCont">
	<div class="subhead">
    	<h3>Logs</h3>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$MGMT; ?>">Manage</a>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$REQ; ?>">Request</a>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT; ?>">Logout</a>
    </div>
    <div style="clear:both"></div>
    <hr />
    <div>
    <?php
		$i=0;
		foreach(LOG::getLogs() as $log):
			$i++;
			if($i % 2 == 1){ ?>
				<div class="log-alternate">
                <?php echo $log; ?>
                </div>
	  <?php }
			else{ ?>
				<div class="log">
                <?php echo $log; ?>
                </div>
	  <?php }	  		
		endforeach;
		if($i===0){
			echo "<div class=\"log\">No Logs</div>";
		}
	?>
    </div>
</div>
</div>
<div id="info" title="Notifications">
<?php
	$notify=false;
	if(isset($_SESSION['response'])){
		echo "<p>".$_SESSION['response']."</p>";
		unset($_SESSION['response']);
		$notify=true;
	}
	if($config === false){
		$notify=true;
		echo "<p>Improper installation. Missing config.php</p>";
	}
	if(isset($at) && $indexersprop === false){
		$notify = true;
		echo "<p>Please add an index site</p>";
	}
if($notify){ ?>
   	<script type="text/javascript">
		$(function() {
			$("#info").show();
			$( "#info" ).dialog();
		});
  </script>
<?php } ?>
</div>
</body>
</html>