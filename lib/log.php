<?php
class LOG{
	private $numlogs = 0;
	public static $LOGFILENAME = "pvr_php.log";

	public static function info($text){
		global $sroot;
		
		self::checkFileSize($text);
		if(false !== ($fp = fopen($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME, 'a'))){
			fwrite($fp, date("d/m/y i:i:s.u a", time())." [INFO]: $text");
			fclose($fp);
		}
	}
	
	public static function warn($text){
		global $sroot;
		
		self::checkFileSize($text);
		if(false !== ($fp = fopen($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME, 'a'))){
			fwrite($fp, date("d/m/y i:i:s.u a", time())." [WARN]: $text");
			fclose($fp);
		}
	}
	
	public static function error($text){
		global $sroot;
		
		self::checkFileSize($text);
		if(false !== ($fp = fopen($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME, 'a'))){
			fwrite($fp, date("d/m/y i:i:s.u a", time())." [ERROR]: $text");
			fclose($fp);
		}
	}
	
	private static function checkFileSize($text){
		global $sroot;
		$textsize = mb_strlen($text, "UTF-8");
		if(!is_dir($sroot.CONFIG::$LOGS)){
			mkdir($sroot.CONFIG::$LOGS,0774,true);
		}
		if(file_exists($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME)){
			$size = filesize($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME);
			if($size+$textsize > CONFIG::$MAXLOGSIZE){
				if(is_file($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.CONFIG::$LOGSTOKEEP)){
					unlink($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.CONFIG::$LOGSTOKEEP);
				}
				for($i =1; $i<CONFIG::$LOGSTOKEEP; $i++){
					if(is_file($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i)){
						rename($sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.$i,$sroot.CONFIG::$LOGS.LOG::$LOGFILENAME.($i+1));
					}
				}
				
			}
		}
	}
	private static function numLogs(){
		global $sroot;
		
		$cnt =0;
		if ($handle = opendir($sroot.CONFIG::$LOGS)) {
			while (false !== ($file = readdir($handle))) {
				if(is_file($sroot.CONFIG::$CLASSES.$file)){
					$cnt++;
				}
			}
		}
		return $cnt;
	}
}
?>