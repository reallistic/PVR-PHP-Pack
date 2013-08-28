<?php
class LOG{
	public static $LOGSTOKEEP = 5; //CHANGE ME: number of logs to keep
	public static $MAXLOGSIZE = 2097152; //CHANGE ME: max log file size (2MB)
	
	public static function info($text){
		$fp = fopen($sroot.CONFIG::$DBS.$this->dbfile, 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			return array(true, "Credentials saved ".$this->username);
		}
	}
}
?>