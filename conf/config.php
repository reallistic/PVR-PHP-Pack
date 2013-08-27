<?php
class INDEXSITE{
	private $name;
	private $apikey;
	private $category;
	private $url;
	public static $default_cat = "3010";
	public $enabled;
	
	public function __construct($n,$a,$u){
		$this->category = array();
		array_push($this->category, $default_cat);
		$this->name = $n;
		$this->apikey = $a;
		$this->url = $u;
		$this->enabled = true;
	}
	
	public function addCat($c){
		if(in_array($c, $this->category, true) === false){
			array_push($c);
		}
	}
	
	public function removeCat($c){
		if(in_array($c, $this->category, true) === false){
			return false;
		}
		else{
			array_splice($this->category,array_search($c,$this->category,true),1);
		}
	}
	
	public function makeSearch($q){
		$cats = implode(', ', $this->category);
		return $this->url."?apikey=".$this->apikey."cat=".$cats."&extended=1"."&t=search&q=".$q;
	}
	
	public function saveSite(){
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
	
	public function getName(){
	}
	
	public function getApiKey(){
		return $this->apikey;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getCat(){
		return $this->category;
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
					$p = md5($p);
					$this->username = $u;
					if($at->getUsername() === $u && $at->getPassword() === $p){
						$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$u);
						$this->info = array(true, "logged in successfully ".gettype($at->getUsername()));
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
			$np = md5($this->password);
			$this->password = $np;
			$this->authtoken = md5($_SERVER['REMOTE_ADDR'].$this->username);
			$fp = fopen("../conf/auth.db", 'w+');
			if(flock($fp, LOCK_EX)) {
				fwrite($fp, serialize($this));
				flock($fp, LOCK_UN);
				return array(true, "Credentials saved ".$this->username);
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
		return $this->username;
	}
	
	public function getPassword(){
		return $this->password;
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