<?php
class INDEXSITE{
	private $name;
	private $apikey;
	private $category;
	private $url;
	public static $default_cat = "3010";
	private $enabled;
	private $id;
	
	public function __construct($n,$a,$u, $i){
		$this->category = array();
		array_push($this->category, $default_cat);
		$this->name = $n;
		$this->apikey = $a;
		$this->url = $u;
		$this->enabled = true;
		$this->id = $i;
	}
	public static function withID($id){
		$response = array();
		if(is_file("../conf/indexsites.db")){
			$inxs = file_get_contents("../conf/indexsites.db");
			$inxs = unserialize($inxs);
			if(!is_array($inxs)){
				array_push($response, "indexsite db was curropt");
			}
			else{
				for( $i=0; $i<count($inxs); $i++){
					$indexsite = $inxs[$i];
					if($indexsite->getId() == $id){
						return $indexsite;
					}
					else{
						array_push($response, "Found non- matching indexsite:");
						array_push($response, "-id ".$id." ".$indexsite->getId());
						array_push($response, "-name ".$indexsite->getName());
						array_push($response, "-url ".$indexsite->getUrl());
						array_push($response, "-apikey ".$indexsite->getApiKey());
					}
				}
			}
		}
		else{
			array_push($response, "indexsite db not found");
		}
		
		return implode(",",$response);
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
		if(is_file("../conf/indexsites.db")){
			$inxs = file_get_contents("../conf/indexsites.db");
			$inxs = unserialize($inxs);
			if(!is_array($inxs)){
				$inxs=array();
			}
		}
		else{
			$inxs = array();
		}
		array_push($inxs, $this);
		$fp = fopen("../conf/indexsites.db", 'w+');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, serialize($inxs));
			flock($fp, LOCK_UN);
			return array(true, "site ".$this->name." saved");
		}
		else {
			return array(false, "file cannot be locked");
		}
		
		fclose($fp);
	}
	
	public function delSite(){
		$response = array();
		if(is_file("../conf/indexsites.db")){
			$inxs = file_get_contents("../conf/indexsites.db");
			$inxs = unserialize($inxs);
			if(!is_array($inxs)){
				$inxs=array();
			}
			$savedsites=array();
			
			for( $i=0; $i<count($inxs); $i++){
				$indexsite = $inxs[$i];
				if(!$this->isEqual($indexsite)){
					array_push($savedsites,$indexsite);
					array_push($response, "Found non- matching indexsite:");
					array_push($response, "-id ".$this->id." ".$indexsite->getId());
					array_push($response, "-name ".$this->name." ".$indexsite->getName());
					array_push($response, "-url ".$this->url." ".$indexsite->getUrl());
					array_push($response, "-apikey ".$this->apikey." ".$indexsite->getApiKey());
				}
				elseif($this->isEqual($indexsite)){
					//found site, skip it
					array_push($response, "Found indexsite skipping");
				}
				elseif(!$indexsite instanceof INDEXSITE){
					//improperly formatted skip it
					array_push($response, "Found improper indexsite skipping");
				}
				else{
					array_push($response, "error found object of type ". gettype($indexsite));
				}
			}
			
			if(count($inxs)>0 && count($savedsites) >0  && count($inxs) != count($savedsites)) {
				$fp = fopen("indexsites.db", 'w+');
				if(flock($fp, LOCK_EX)){
					fwrite($fp, serialize($savedsites));
					flock($fp, LOCK_UN);
					array_push($response, "saved ". count($savedsites) . " site(s)");
				}
				else{
					array_push($response, "failed saving ". count($savedsites) . " site(s). file cannot be locked");
				}
			}
			elseif(count($inxs)==0 || count($savedsites) == 0 ) {
				array_push($response, "either the index site db was curropt or no sites were saved");
				unlink("../conf/indexsites.db");
			}
			elseif(count($inxs) == count($savedsites)){
				array_push($response, "no changes neccassary");
			}
			else{
				array_push($response, "error ". count($inxs) . " sites in db ".count($savedsites) . " sites found");
			}
		}
		else{
			array_push($response, "indexsite db not found");
		}
		
		return implode(",",$response);
	}
	
	public function isEqual($obj){
		if($obj instanceof INDEXSITE){
			return ($this->apikey === $obj->getApiKey() && $this->id === $obj->getId() && $this->name === $obj->getName() && $this->url === $obj->getUrl());
		}
		
		return false;
	}
	
	public function sameClass($obj){
		return $obj instanceof $this;
	}
	
	public function getName(){
		return $this->name;
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
	
	public function getId(){
		return $this->id;
	}
	
	public function isEnabled(){
		return $this->enabled;
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