<?php
session_start();
$response = false;
$indexers = false;
$indexersprop = false;
$config = false;
$query = false;
if(is_file("../conf/config.php")){
	$config = true;
	require("../conf/config.php");
	
	if(isset($_REQUEST['q']) && $_REQUEST['q'] != ""){
		$_SESSION['q']=$_REQUEST['q'];
		//header("location: /request/");
	}
	elseif(isset($_SESSION['q'])){
		$query = true;
		$q = escape_query($_SESSION['q']);
		
		unset($_SESSION['q']);
		session_destroy();
		if(is_file("../conf/indexsites.db")){
			$indexers = true; //check for indexers was good
			$inxs = file_get_contents("../conf/indexsites.db");
			$indexsites = unserialize($inxs);
			$indexersprop = is_array($indexsites); //check for indexsites class was good
			if($indexersprop === true){
				for( $i=0; $i<count($indexsites); $i++){
					$indexsite = $indexsites[$i];
					$ch = curl_init($indexsite->makeSearch($q));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					$resp = curl_exec($ch);
					curl_close($ch);
					
					$xml = simplexml_load_string($resp);
					if(isset($resp) && count($xml->channel->item) !== 0){
					   foreach ($xml->channel->item as $item):
							if(!in_array($item->title, $filter)){
								$sr = new SEARCHRESULT($item->link,$item->title);
								$grabs = $item->xpath('//newznab::attr[@name="grabs"]');
								if($grabs){//<newznab:attr name="grabs" value="#" />
									$sr->setGrabs($grabs['value']);
								}
								array_push($results, $sr);
								array_push($filter, $item->title);
							}
					   endforeach;
					}
				}
				if (count($results) >0){
					$response = true; //response recieved
				}
			}
		}
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Plexcloud - Music | Request</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet"></link>
<script type="text/javascript">
	$(function() {
		$( "input[type=submit], a.button, button" )
		  .button();
	 });
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
<br />
<div id="info">
<?php
	if($config === false){
		echo "<h3>Improper installation. Missing config.php</h3>";
	}
	elseif($indexers === false){
		echo "<a class=\"button\" href=\"../manage/\" >Please configure at least one index site</a>";
	}
	elseif($indexersprop === false){
		echo "<a class=\"button\" href=\"../manage/\" >index site db curropted please repair</a>";
	}
	?>
</div>
<br />
<div id="results">
<?php
    if($response === true){
	   foreach ($results as $item):
			echo "<a href=\"".$item->getLink()."\" ><h3>".$item->getTitle() . "</h3></a><br />";
			?>
            <form enctype="application/x-www-form-urlencoded" method="post">
            	<input type="hidden" name="nzbname" value="<?php echo $item->getTitle(); ?>" />
				<input type="hidden" name="name" value="<?php echo $item->getLink(); ?>" />
            	<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Add</span></button>
            </form>
            <?php
	   endforeach;
	}
	elseif($query === false){
		echo "<h3>Enter values above and click search</h3>";
	}
	elseif($response === false) {
		echo "<h3>No Results</h3>";
	}
	?>
</div>
</body>
</html>