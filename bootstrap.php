<?php
	$root = "";
	$root = $_SERVER['DOCUMENT_ROOT'];
	$root = substr(dirname(__FILE__),strlen($root));
	$root =$root."/";
	$sroot = dirname(__FILE__)."/";
	
	//EDITME: Path to config.php
	$CONFIGFILE = "lib/config.php";
	try{
	require($sroot.$CONFIGFILE);
	
	if(class_exists(CONFIG)){
		if ($handle = opendir($sroot.CONFIG::$CLASSES)) {
			while (false !== ($file = readdir($handle))) {
				if(is_file($sroot.CONFIG::$CLASSES.$file)
					&& $sroot.CONFIG::$CLASSES.$file != $sroot.$CONFIGFILE){
					require($sroot.CONFIG::$CLASSES.$file);
				}
			}
		}
	}
	}
	catch(Exception $e) {
		echo $e;
	}
?>