<?php
session_start();
require("conf/config.php");
function escape_query($str) {
	$str=htmlentities($str, ENT_QUOTES);
    return strtr($str, array(
        "\0" => "",
        "\"" => "&#34;",
        "\\" => "&#92;"
    ));
}

if(isset($_REQUEST['q']) && $_REQUEST['q'] != ""){
	$_SESSION['q']=$_REQUEST['q'];
	header("location: /request/");
}
elseif(isset($_SESSION['q'])){
	$q = escape_query($_SESSION['q']);
	
	unset($_SESSION['q']);
	session_destroy();
	if($q!=""){
		$art = "&artist=".$art;
	}
	
	$inxs = file_get_contents("conf/indexsites.db");
	$inxs = explode("\r\n",$inx);
	$resp ="";
	$results=array();
	$filter = array();
	foreach( $inxs as $inx){
		$indexsite = unserialize($inx);
		$ch = curl_init($indexsite->makeSearch($q));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$resp = curl_exec($ch);
		curl_close($ch);
		
		$xml = simplexml_load_string($resp);
		if(isset($resp) && count($xml->channel->item) !== 0){
		   foreach ($xml->channel->item as $item):
				//echo "<a href=\"".$item->link."\" ><h3>".$item->title . "</h3></a><br />";
				if(!in_array($item->title, $filter)){
					//create new SEARCHRESULT set title, grabs, and link
					//add to results
					//add title to filter
				}
		   endforeach;
		}
	}
	
	/*$resp = "";
	$endpt = "https://smackdownonyou.com/api";
	$data = "apikey=24473a123ea69a43cbdc55320d302847".
				//"&extended=1".
				"&t=music".
				"&cat=3010".$art.$alb;
	$endpt .="?".$data;*/
	
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Request</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/vader/jquery-ui.min.css" rel="stylesheet"></link>
<script type="text/javascript">
	function doSubmit(){
		if($("input[name=album]").val() != "" || $("input[name=artist]").val() != ""){
			$("form").submit();
		}
		else{
			$("#info").html("Please enter a value in one of the fields");
		}
	}
</script>
</head>

<body>
<form enctype="application/x-www-form-urlencoded" method="post">
<label>Find an Album?
<input type="text" name="album" /></label>
<label>Find an Artist?
<input type="text" name="artist" /></label>
<br/>
</form>
<button onClick="doSubmit();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Search</span></button>

<div id="info">&nbsp;</div>
<br />
<div>
	<?php
    $xml = simplexml_load_string($resp);
	if(isset($resp) && count($xml->channel->item) !== 0){
	   foreach ($xml->channel->item as $item):
			echo "<a href=\"".$item->link."\" ><h3>".$item->title . "</h3></a><br />";
			?>
            <form enctype="application/x-www-form-urlencoded" method="post">
            	<input type="hidden" name="nzbname" value="<?php echo $item->title; ?>" />
				<input type="hidden" name="name" value="<?php echo $item->link; ?>" />
            	<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Add</span></button>
            </form>
            <?php
	   endforeach;
	}
	elseif(isset($resp) && count($xml->channel->item) === 0) {
		echo "<h3>No Results</h3>";
	}
	else{
		echo "<h3>Enter values above and click search</h3>";
	}
	?>
</div>
</body>
</html>