<?php
function get_string_between($string, $start, $end)
            {
                $string = " " . $string;
                $ini = strpos($string, $start);
                if ($ini == 0) {
                    return "";
                }
                $ini += strlen($start);
                $len = strpos($string, $end, $ini) - $ini;
                return substr($string, $ini, $len);
            }
function write2log($typelog,$logstring){
$ip=$_SERVER['REMOTE_ADDR'];
error_log($typelog."#".date("d/m/Y H:i:s")."#".$ip."#".$logstring."#
",3,"ig.log");
}
function getContents($str, $startDelimiter, $endDelimiter) {
  $contents = array();
  $startDelimiterLength = strlen($startDelimiter);
  $endDelimiterLength = strlen($endDelimiter);
  $startFrom = $contentStart = $contentEnd = 0;
  while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
    $contentStart += $startDelimiterLength;
    $contentEnd = strpos($str, $endDelimiter, $contentStart);
    if (false === $contentEnd) {
      break;
    }
    $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
    $startFrom = $contentEnd + $endDelimiterLength;
  }

  return $contents;
}
function checkstringada($str,$cari){
$mystring = $str;
$findme   = $cari;
$pos = strpos($mystring, $findme);
if ($pos === false) {
   return "0";
} else {
    return "1";
}
}
function check_404($url) {
   $headers=get_headers($url, 1);
   if ($headers[0]!='HTTP/1.1 200 OK') return true; else return false;
}
if (isset($_POST['urlx'])){
$urlx = $_POST['urlx'];
echo "<br><br><hr>";
if (checkstringada($urlx,"http")==0){
if (checkstringada($urlx,"https")==0){
die('<div class="alert alert-danger alert-dismissible">
 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error!</strong> Please provide complete URL to the photo page including http or https.
</div>');
}
}
if (checkstringada($urlx,"www.instagram.com")==0){
die('<div class="alert alert-danger alert-dismissible">
 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error!</strong> You sure you entering a valid Instagram URL?.
</div>');
}
if (check_404($urlx)==true){
die('<div class="alert alert-danger alert-dismissible">
 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error!</strong> Post Is Deleted or Private.
</div>');
}
$htmlok = file_get_contents($urlx);
write2log("1","Open:".$urlx);
if (checkstringada($urlx,"instagram.com/p/")==0){
$inipage="0";
}else{
$inipage="1";
}

if (checkstringada($htmlok,'"is_private":true,')==1){
die('<div class="alert alert-danger alert-dismissible">
 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error!</strong> This Instagram Account Is Private.
</div>');
}

//die($inipage);

$char = "'";
if ($inipage=="1"){
$hasilokGambar = get_string_between($htmlok ,'"display_url":"','","display_resources"');
$hasilokVideo = get_string_between($htmlok ,'"video_url":"','","video_view_count"');
if (empty(trim($hasilokVideo))){
	echo '
<div class="alert alert-success alert-dismissible">
 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success! </strong><a href="'.$hasilokGambar.'?dl=1" class="btn btn-success btn-xs" role="button">Download Picture</a> <a onclick="showimg('.$char.$hasilokGambar.$char.')" class="modal-img btn btn-info btn-xs" role="button">see Picture</a>.
</div>';
}else{
echo '<video width="320" height="240" controls>
  <source src="'.$hasilokVideo.'" type="video/mp4">
Your browser does not support the video tag.
</video>
		<a href="'.$hasilokVideo.'?dl=1" class="btn btn-success" role="button">Download Videos</a>';
}
}else{
$hasilokProfile = getContents($htmlok , '"display_url":"','","edge_liked_by"');
$x = 0;

echo '';
while($x <= count($hasilokProfile)-1) {
	echo '
<div class="alert alert-success alert-dismissible">
 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success! </strong><a href="'.$hasilokProfile[$x].'?dl=1" class="btn btn-success btn-xs" role="button">Download Picture</a> <a onclick="showimg('.$char.$hasilokProfile[$x].$char.')" class="modal-img btn btn-info btn-xs" role="button">see Picture</a>.
</div>';
  $x++;
}
echo ""; 
}
}else{
echo "<form action='' method='POST'><input type='url' name='urlx'><input type='submit'> ";
}
?>