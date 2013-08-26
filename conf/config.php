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
	public $info;
	private $auth_file = "../conf/auth.db";
	
	function __construct($u, $p){
		$this->authtoken = NULL;
		$this->info = array(false,"on init");
		$this->checkAuth($u, $p);
		
	}
	
	private function checkAuth($u, $p){
		if(!isset($this->authtoken)){
			if(is_file($this->auth_file)){
				$at = unserialize(file_get_contents($this->auth_file));
				if($at instanceof AUTH){
					$u = openssl_encrypt($u, $this->enttype, $this->sh, $at->iv["usr"]);
					$p = openssl_encrypt($p, $this->enttype, $this->sh, $at->iv["pwd"]);
					if($at->username === $u && $at->password === $p){
						$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$u);
						$this->info = array(true, "logged in successfully");
					}
					else{
						$this->authtoken = NULL;
						$this->info = array(true, "bad username or password");
					}
				}
				else{
					$this->authtoken = NULL;
					$this->info = array(true, "curropt auth.db file");
				}
			}
			else{
				$this->authtoken = "confirm";
				$this->info = array(true, "confirm");
				$this->username = $u;
				$this->password = $p;
			}
		}
		else{
			$this->info = array(false, "authtoken not null");
		}
	}
	public function checkToken(){
		return ($this->authtoken == md5($_SERVER['REMOTE_ADDR'].$this->username));
	}
	public function confirm($p){
		if($p !== $this->password){
			$this->info = array(false, "Passwords don't match");
		}
		elseif($this->authtoken == "confirm" && isset($this->password) && !is_file($this->auth_file)){
			$this->info = $this->createAuth();
		}
		else{
			$this->info = array(false, "an error occured2");
		}
	}
	private function createAuth(){		
		
		if($this->authtoken === "confirm" || $this->authtoken == md5($_SERVER['REMOTE_ADDR'].$u)){
			if(is_file($this->auth_file)){
				unlink($this->auth_file);
			}
			$this->sh = md5("plexcloud.tv");
			$iv_size1 = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
			$iv1 = mcrypt_create_iv($iv_size1, MCRYPT_RAND);		
			$iv_size2 = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
			$iv2 = mcrypt_create_iv($iv_size2, MCRYPT_RAND);		
			$u = openssl_encrypt($this->username, $this->enttype, $this->sh, $iv1);
			$p = openssl_encrypt($this->password, $this->enttype, $this->sh, $iv2);
			$this->iv = array("usr" => $iv1,"pwd" => $iv2);
			$this->username = $u;
			$this->password = $p;
			$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$u);
			$fp = fopen("../conf/auth.db", 'w+');
			if(flock($fp, LOCK_EX)) {
				fwrite($fp, serialize($this));
				flock($fp, LOCK_UN);
				return array(true, "Credentials saved");
			}
			else {
				return array(false, "file cannot be locked");
			}
		}
		else {
			return array(false, "An error occurred1");
		}
	}
	
	public function getUsername(){
		if($this->authtoken != NULL 
			&& $this->authtoken == md5($_SERVER['REMOTE_ADDR'].$this->username)){
			return openssl_decrypt($this->username, $this->enttype, $this->sh, $this->iv["usr"]);
		}
		elseif($this->authtoken == "confirm"){
			return $this->username;
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