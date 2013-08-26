<?php
session_start();
$config = false;
if(is_file("../conf/config.php")){
	$config = true;
	require("../conf/config.php");
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
</head>

<body>
<div id="dialog" title="Basic dialog">
  <p>This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.</p>
</div>
<div id="info">
<?php
	if($config === false){
		echo "<h3>Improper installation. Missing config.php</h3>";
	}
?>
</div>
</body>
</html>