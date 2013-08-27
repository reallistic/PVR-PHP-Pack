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
	if(is_file("../conf/indexsites.db")){
		$indexers = true; //check for indexers was good
		$inxs = file_get_contents("../conf/indexsites.db");
		$indexsites = unserialize($inxs);
		$indexersprop = is_array($indexsites); //check for indexsites class was good
	}
	if((isset($_REQUEST['artist']) && $_REQUEST['artist'] != "") || ( isset($_REQUEST['album']) && $_REQUEST['album'] != "")){
		$query = true;
		$q = array(
				'album'=> escape_query($_REQUEST['album']),
				'artist'=> escape_query($_REQUEST['artist'])
		);
		//header("location: /request/");
	}
	elseif(isset($_REQUEST['q']) && $_REQUEST['q'] != ""){
		$query = true;
		$q = escape_query($_REQUEST['q']);
	}
	
	if($query === true){
		if($indexers === true && $indexersprop === true){
			$results = array();
			$filter=array();
			$curls=array();
			for( $i=0; $i<count($indexsites); $i++){
				$indexsite = $indexsites[$i];
				
				array_push($curls, $indexsite->makeSearch($q));
				
				$ch = curl_init($indexsite->makeSearch($q));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$resp = curl_exec($ch);
				curl_close($ch);
				
				$xml = simplexml_load_string($resp);
				if(count($xml->channel->item) !== 0){
				   foreach ($xml->channel->item as $item):
						if(!in_array(strtolower(escape_query($item->title)), $filter)){
							$sr = new SEARCHRESULT();
							$sr->setLink($item->link);
							$sr->setTitle($item->title);
							$attr = $item->xpath('newznab:attr[@name="grabs"]');
							$sr->setGrabs((string)$attr[0]['value']);						
							array_push($results, $sr);
							array_push($filter, strtolower(escape_query($item->title)));
						}
				   endforeach;
				}
				
			}
			if (count($results) >0){
				$response = true; //response recieved
			}
		}
	}
	
	unset($_SESSION['q']);
	unset($_SESSION);
	session_destroy();
	session_unset();
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
<link href="../conf/style.css" rel="stylesheet" type="text/css"></link>
<script type="text/javascript">
	$(function() {
		$( "input[type=submit], a.button, button" )
		  .button();
		$("button").click(function( event ) {
			event.preventDefault();
		});
	 });
	function doSubmit(){
		if($("input[name=q]").val() != "" || $("input[name=artist]").val() != "" || $("input[name=album]").val() != ""){
			$("form#srch").submit();
		}
		else{
			$("#info").html("Please enter a value in one of the fields");
		}
	}
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
            <h3>Request</h3>
        </div>
        <div class="subhead">
            <a class="button" href="../manage/">Manage</a>
        </div>
        <div style="clear:both"></div>
        <hr />
        <div>
            <form id="srch" enctype="application/x-www-form-urlencoded" method="post">
            <label>Search
            <input type="text" name="q" /></label><br>
            <strong>Or</strong><br>
            <label>Find an Artist?
            <input type="text" name="artist" /></label>
            <label>Find an Album?
            <input type="text" name="album" /></label>
            <br/>
            </form>
            <button onClick="doSubmit();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Search</span></button>
            <br />
            
            <br />
            <div id="results">
            <?php
                if($response === true){
                    /*echo implode("<br>",$results);*/
                   foreach ($results as $item): ?>
                        <form enctype="application/x-www-form-urlencoded" method="post" action="#">
                        	<input type="hidden" name="nzbname" value="<?php echo $item->getTitle(); ?>" />
                            <input type="hidden" name="name" value="<?php echo $item->getLink(); ?>" /> 
			    <?php echo "<a href=\"".$item->getLink()."\" ><h3>".$item->getTitle() . "</h3></a> <strong>Grabs: ".$item->getGrabs();?>
                			<input type="submit" value="Add" />
                            <br />
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
        </div>
    </div>
</div>
<div id="info">
<?php
	if($config === false){
		echo "<h3>Improper installation. Missing config.php</h3>";
	}
	elseif($indexers === false){
		echo "Please configure at least one index site<a class=\"button\" href=\"../manage/\" >Manage</a>";
	}
	elseif($indexersprop === false){
		echo "index site db curropted please repair<a class=\"button\" href=\"../manage/\" >Manage</a>";
	}
	
	if($query){
	}
	?>
</div>
</body>
</html>