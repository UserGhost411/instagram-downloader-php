<?php
  error_reporting(0);
  function get_string_between($string, $start, $end){
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {return "";}
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
  }
  function write2log($typelog,$logstring){
    $ip=$_SERVER['REMOTE_ADDR'];
    error_log($typelog."#".date("d/m/Y H:i:s")."#".$ip."#".$logstring."#\n",3,"ig.log");
  }
  function getContents($str, $startDelimiter, $endDelimiter) {
    $contents = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
      $contentStart += $startDelimiterLength;
      $contentEnd = strpos($str, $endDelimiter, $contentStart);
      if (false === $contentEnd) {break;}
      $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
      $startFrom = $contentEnd + $endDelimiterLength;
    }
  return $contents;
  }
  function checkstringada($str,$cari){
    if (strpos($str, $cari) === false) {
      return "0";
    } else {
      return "1";
    }
  }
  function check_200($url) {
    $headers=get_headers($url, 1);
    if ($headers[0]!='HTTP/1.1 200 OK') return false; else return true;
  }
  function addparam($url,$param){
    $url = str_replace("\u0026","&",$url);
    if ( strpos($url, "?")) {
      return $url . "&" . $param;
    }else{
      return $url . "?" . $param;
    }
  }
  function decodecaption($str){
    if($str){
      $str = json_decode('"'.$str.'"');
      return substr($str,0,150);
    }else{
      return "No Caption Avalaible";
    }
  }
  function IGPROFILE_API($uid){
    $apix = "https://i.instagram.com/api/v1/users/$uid/info/";
    $contentapi = file_get_contents($apix);
    //$hasilhdpic = json_decode($contentapi);//get_string_between($contentapi , '"hd_profile_pic_url_info": {"url": "','"');
  return $contentapi;
  }
  if (isset($_POST['urlx'])){
    $urlx = $_POST['urlx'];
    echo "<br><br><hr>";
    //Check Http(s) Protocols
      if (checkstringada($urlx,"http")==0 || checkstringada($urlx,"https")==0){
        die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Please provide complete URL to the photo page including http or https.</div>');
      }
    //valid URL instagram
      if (checkstringada($urlx,"www.instagram.com")==0){
        die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> You sure you entering a valid Instagram URL?.</div>');
      }
    //Check Post is Not Avalaible Or Private
      if (check_200($urlx)==false){
        die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Post Is Deleted or Private.</div>');
      }
    $htmlok = file_get_contents($urlx."");
    $iguid = get_string_between($htmlok,'"profilePage_','"');
    
    write2log("1","Open:".$urlx);
      if (checkstringada($urlx,"/p/")==0){
        $inipage="0";
        if(empty($iguid)){
          die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Cant Find This Instagram Account.</div>');
          }
        $igprofile = json_decode(IGPROFILE_API($iguid),TRUE);
      }else{
        $inipage="1";
       
      }
      if (checkstringada($htmlok,'"is_private":true,')==1){
        die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> This Instagram Account Is Private.</div>');
      }
    
    $char = "'";
if ($inipage=="1"){
$hasilokGambar = getContents($htmlok ,'"display_url":"','"');
$hasilokVideo = get_string_between($htmlok ,'"video_url":"','"');
$captionnya = decodecaption(get_string_between($htmlok ,'"text":"','"'));
if (empty(trim($hasilokVideo))){
  $x = 0;
  while($x <= count($hasilokGambar)-1) {
    echo '
    <div class="col-sm-4">
  <div class="panel panel-default ">
    <div class="panel-body"><img width="320" onclick="showimg('.$char.$hasilokGambar[$x].$char.')" src="'.str_replace("\u0026","&",$hasilokGambar[$x]).'"></img></div>
    <div class="panel-footer">
    
    <a href="'.addparam($hasilokGambar[$x],"dl=1").'" class="btn btn-success btn-xs" role="button">Download Picture</a> </div>
  </div>
  </div>
  ';
    $x++;
  }
}else{
if($hasilokVideo=="https://static.cdninstagram.com/rsrc.php/null.jpg"){
  $hasilokVideo= get_string_between($htmlok ,'property="og:video" content="','"');
}
echo '
<div class="col-sm-6 col-md-offset-3">
<div class="panel panel-default ">
  <div class="panel-body"><video width="500" height="300" controls>
  <source src="'.$hasilokVideo.'" type="video/mp4">
Your browser does not support the video tag.
</video></div>
  <div class="panel-footer">
  <strong>'.$captionnya.'</strong><br><BR>
  <a href="'.addparam($hasilokVideo,"dl=1").'" class="btn btn-success" role="button">Download Videos</a>
</div>
</div>
	';
}
}else{
$hasilokProfile = getContents($htmlok , '"display_url":"','","edge_liked_by"');
$hasilokcaption = getContents($htmlok , '"text":"','"');
//print_r($igprofile);
$igpic = $igprofile['user']['hd_profile_pic_url_info']['url'];
$x = 0;
echo '
<div class="panel panel-default ">
<div class="panel-heading"><b>'.$igprofile['user']['full_name'].'</b></div>
<div class="panel-body">
<div class="row">
<div class="col-sm-2">
<img class="img-responsive img-circle" onclick="showimg('.$char.$igpic.$char.')" width="150" src="'.str_replace("\u0026","&",$igpic).'">
</div>
<div class="col-sm-10">
<h1><b>'.$igprofile['user']['full_name'].'</b>('.$igprofile['user']['username'].')</h1>
<span class="label label-default">Followers:'.$igprofile['user']['follower_count'].'</span>
<span class="label label-primary">Following:'.$igprofile['user']['following_count'].'</span>
<span class="label label-success">Media:'.$igprofile['user']['media_count'].'</span>
<span class="label label-info">IG-UID:'.$iguid.'</span>
</div>
</div>
<div class="row">
<div class="col-sm-2">
<a href="'.addparam($igpic,"dl=1").'" class="btn btn-success btn-xs" role="button">Download Profile Picture</a>
</div>
</div>
</div>
</div>
</div><div class="row">';
while($x <= count($hasilokProfile)-1) {
  echo '
  <div class="col-sm-4">
<div class="panel panel-default ">
  <div class="panel-body"><img width="320" onclick="showimg('.$char.$hasilokProfile[$x].$char.')" src="'.str_replace("\u0026","&",$hasilokProfile[$x]).'"></img></div>
  <div class="panel-footer">
  <strong>'.decodecaption($hasilokcaption[$x]).'</strong><br><BR>
  <a href="'.addparam($hasilokProfile[$x],"dl=1").'" class="btn btn-success btn-xs" role="button">Download Picture</a> </div>
</div>
</div>
';
  $x++;
}
echo "</div>"; 
}
}else{
echo "<form action='' method='POST'><input type='url' name='urlx'><input type='submit'> ";
}
?>
