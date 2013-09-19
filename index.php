<?php
session_start();
$response = false;
$indexers = false;
$indexersprop = false;
$config = false;
$query = false;
require_once("bootstrap.php");
if(class_exists(CONFIG)){
	$config = true;
	if(is_file(CONFIG::$DBS.INDEXSITE::$dbfile)){
		$indexers = true; //check for indexers was good
		$inxs = file_get_contents(CONFIG::$DBS.INDEXSITE::$dbfile);
		$indexsites = unserialize($inxs);
		$indexersprop = is_array($indexsites); //check for indexsites class was good
	}
	if((isset($_REQUEST['artist']) && $_REQUEST['artist'] != "") || ( isset($_REQUEST['album']) && $_REQUEST['album'] != "")){
		$query = true;
		$q = array(
				'album'=> CONFIG::escape_query($_REQUEST['album']),
				'artist'=> CONFIG::escape_query($_REQUEST['artist'])
		);
		LOG::info(__FILE__." Line[".__LINE__."]"."searching for artist/album ".var_export($q));
	}
	elseif(isset($_REQUEST['q']) && $_REQUEST['q'] != ""){
		$query = true;
		$q = CONFIG::escape_query($_REQUEST['q']);
		LOG::info(__FILE__." Line[".__LINE__."]"."general search for ".$q);
	}
	
	if($query === true){
		require "lib/lastfm/lastfm.api.php";
					 
		// set api key				
		CallerFactory::getDefaultCaller()->setApiKey("24e80eb914d9be7c19392358d24a39dc");
					 
		// search for the Coldplay band
		$artistName = $q['artist'];
		$limit = 1;
		$results = Artist::search($artistName, $limit);		
		while ($artist = $results->current()) {
			echo "<div>";
			echo "<h3>" . $artist->getName() . "</h3>";
			echo "<a href=\"" . $artist->getUrl() . "\" >LastFm - " .$artist->getName()."</a><br>";
			/*echo '<img src="' . $artist->getImage(4) . '">';*/
			$albums = Artist::getTopAlbums($artist->getName());
			echo "<ul>";
			foreach ($albums as $album){
				echo "<li>".$album->getName()."</li>";
			}
			echo "</ul>";
			echo "</div>";
		 
			$artist = $results->next();
		}
		if($results && $indexers === true && $indexersprop === true){
			$results = array();
			$filter=array();
			$curls=array();
			for( $i=0; $i<count($indexsites); $i++){
				$indexsite = $indexsites[$i];
				if(!$indexsite->isEnabled()){
					continue;
				}
				array_push($curls, $indexsite->makeSearch($q));
				
				$ch = curl_init($indexsite->makeSearch($q));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$resp = curl_exec($ch);
				curl_close($ch);
				
				$xml = simplexml_load_string($resp);
				if(count($xml->channel->item) !== 0){
				   foreach ($xml->channel->item as $item):
						if(!in_array(strtolower(CONFIG::escape_query($item->title)), $filter)){
							$sr = new SEARCHRESULT();
							$sr->setLink($item->link);
							$sr->setTitle($item->title);
							$attr = $item->xpath('newznab:attr[@name="grabs"]');
							$sr->setGrabs((string)$attr[0]['value']);						
							array_push($results, $sr);
							array_push($filter, strtolower(CONFIG::escape_query($item->title)));
						}
				   endforeach;
				   
				   LOG::info(__FILE__." Line[".__LINE__."]"."found ".count($xml->channel->item)." results with site ".$indexsite->getName());
				}
				
				
			}
			if (count($results) >0){
				$response = true; //response recieved
			}
		}
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo CONFIG::$APPNAME; ?> | Request</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet"></link>
<link href="<?php echo $root.CONFIG::$STYLE; ?>" rel="stylesheet" type="text/css"></link>
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
    	<?php echo CONFIG::$APPNAME; ?>
    </div>
</div>
<div class="mainCont">	
    <div class="innerCont">
        <div class="subhead">
            <h3>Request</h3>
        </div>
        <div class="subhead">
            <a class="button" href="<?php echo $root.CONFIG::$MGMT; ?>">Manage</a>
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
					/*
					$i=0;
                   foreach ($results as $item): ?>
                        <form id="result<?php echo $i; ?>" enctype="application/x-www-form-urlencoded" method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$NTYSCRIPT; ?>">
                        	<input type="hidden" name="name" value="<?php echo $item->getTitle(); ?>" />
                            <input type="hidden" name="link" value="<?php echo $item->getLink(); ?>" /> 
                            <input type="hidden" name="method" value="sabnzbd" /> 
			    <?php echo "<a href=\"".$item->getLink()."\" ><h3>".$item->getTitle() . "</h3></a> <strong>Grabs: ".$item->getGrabs();?>
                			<input type="submit" value="Send" />
                            <br />
                        </form>
                        <?php
						$i++;
                   endforeach;*/
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
<div id="info" style="display:none;" title="Notifications">
<?php
	$notify=false;
	if(isset($_SESSION['response'])){
		echo "<p>".$_SESSION['response']."</p>";
		$notify=true;
	}
	if($config === false){
		LOG::error(__FILE__." Line[".__LINE__."]"."config.php missing");
		echo "<h3>Improper installation. Missing config.php</h3>";
		$notify=true;
	}
	elseif($indexers === false){
		LOG::warn(__FILE__." Line[".__LINE__."]"."couldn't find any indexers");
		echo "Please configure at least one index site<a class=\"button\" href=\"". $root.CONFIG::$MGMT."\" >Manage</a>";
		$notify=true;
	}
	elseif($indexersprop === false){
		LOG::warn(__FILE__." Line[".__LINE__."]"."couldn't find any proper indexers");
		echo "index site db curropted please repair<a class=\"button\" href=\"". $root.CONFIG::$MGMT."\" >Manage</a>";
		$notify=true;
	}
	
	if($notify){ ?>
   	<script type="text/javascript">
		$(function() {
			$("#info").show();
			$( "#info" ).dialog();
		});
  </script>
<?php }
	unset($_SESSION['q']);
	unset($_SESSION['response']);
	unset($_SESSION);
	session_destroy();
	session_unset();
	exit;
	?>
</div>
</body>
</html>