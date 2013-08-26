<?php
class INDEXSITE{
	private $name;
	private $apikey;
	private $category;
	private $url;
	public static $default_cat = "3010";
	
	function __construct($n,$a,$u){
		$this->category = array();
		$this->name = $n;
		$this->apikey = $a;
		$this->url = $u;
	}
	
	function addCat($c){
		if(in_array($c, $this->category, true) === false){
			array_push($c);
		}
	}
	
	function removeCat($c){
		if(in_array($c, $this->category, true) === false){
			return false;
		}
		else{
			array_splice($this->category,array_search($c,$this->category,true),1);
		}
	}
	
	function makeSearch($q){
		$cats = implode(', ', $this->category);
		return $this->url."?apikey=".$this->apikey."cat=".$cats."&extended=1"."&t=search&q=".$q;
	}
	
	function saveSite(){
		$fp = fopen("indexsites.db", 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this) . "\r\n");
			flock($fp, LOCK_UN);
			return array(true, "site ".$this->name." saved");
		}
		else {
			return array(false, "file cannot be locked");
		}
		
		fclose($fp);
	}
	
	/*$resp = "";
	$endpt = "https://smackdownonyou.com/api";
	$data = "apikey=24473a123ea69a43cbdc55320d302847".
				//"&extended=1".
				"&t=music".
				"&cat=3010".$art.$alb;
	$endpt .="?".$data;*/
}

class AUTH{
	//auth token to expire
	private $username;
	private $password;
	private $authtoken;
	private $level;
	private $ip;
	private $enttype = "AES-256-CBC";
	private $sh;
	private $iv;
	
	function __construct($u, $p){
		$this->authtoken = NULL;
		$this->checkAuth($u, $p);
	}
	
	function checkAuth($u, $p){
		if($this->authtoken === NULL){
			if(is_file("auth.db")){
				$at = unserialize(file_get_contents("auth.db"));
				if($at instanceof AUTH){
					$u = openssl_encrypt($u, $this->enttype, $this->sh, $this->iv["usr"]);
					$p = openssl_encrypt($p, $this->enttype, $this->sh, $this->iv["pwd"]);
					if($at->username === $u && $at->password === $p){
						$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$u);
					}
					else{
						$this->authtoken = NULL;
					}
				}
				else{
					$this->authtoken = NULL;
				}
			}
			else{
				$this->authtoken = "confirm";
			}
		}
		elseif($this->authtoken != "confirm"){
			
		}
	}
	
	function createAuth($u, $p){
		if(is_file("auth.db")){
			unlink("auth.db");
		}
		$this->sh = md5("plexcloud.tv");
		$iv_size1 = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		$iv1 = mcrypt_create_iv($iv_size1, MCRYPT_RAND);		
		$iv_size2 = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		$iv2 = mcrypt_create_iv($iv_size2, MCRYPT_RAND);		
		$u = openssl_encrypt($u, $this->enttype, $this->sh, $iv1);
		$p = openssl_encrypt($p, $this->enttype, $this->sh, $iv2);
		$this->iv = array("usr" => $iv1,"pwd" => $iv2);
		$this->username = $u;
		$this->password = $p;
		$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$u);
		$fp = fopen("auth.db", 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($this));
			flock($fp, LOCK_UN);
			return array(true, "Credentials saved");
		}
		else {
			return array(false, "file cannot be locked");
		}
	}
	
	function getUsername(){
		if($this->authtoken != NULL 
			&& $this->authtoken == md5($_SERVER['REMOTE_ADDR'].$this->username)){
			return openssl_decrypt($this->username, $this->enttype, $this->sh, $this->iv["usr"]);
		}
	}
}

class SEARCHRESULT{
	private $link;
	private $title;
	private $grabs;
	
	function __construct(){
		$this->grabs=0;
	}
	
	/*public staticfunction SEARCHRESULT($l, $t){
		$this->grabs=0;
		$this->link=$l;
		$this->title=$t;
	}
	
	function SEARCHRESULT($l, $t, $g){
		$this->grabs=intval($g);
		$this->link=$l;
		$this->title=$t;
	}*/
	
	function setLink($l){
		$this->link=$l;
	}
	function setTitle($t){
		$this->title=$t;
	}
	function setGrabs($g){
		$this->grabs=intval($g);
	}
	
	function getTitle(){
		return $this->title;
	}
	
	function getLink(){
		return $this->link;
	}
	function getGrabs(){
		return $this->grabs;
	}	
}

function addIndexSite($n, $a, $c, $u){
	
}

function escape_query($str) {
	$str=htmlentities($str, ENT_QUOTES);
	return strtr($str, array(
		"\0" => "",
		"\"" => "&#34;",
		"\\" => "&#92;"
	));
}

?>