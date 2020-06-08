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
  function contains($str,$cari){
    if (strpos($str, $cari) === false)return "0";else return "1";
  }
  function get_content($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BODY, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
  }
  function check_200($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BODY, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    if(explode("\n",$output)[0]=='HTTP/1.1 200 OK')return false; else return true;
  }
  function IGPROFILE_API($uid){
    $apix = "https://www.instagram.com/$uid/?__a=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apix);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BODY, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Instagram 64.0.0.14.96');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output,true);
  }
  if (isset($_POST['urlx'])){
    $urlx = $_POST['urlx'];
    echo "<br><br><hr>";
    if (contains($urlx,"http")==0 || contains($urlx,"https")==0){ //Check Http(s) Protocols
      die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Please provide complete URL to the photo page including http or https.</div>');
    }
    if (contains($urlx,"www.instagram.com")==0){//valid URL instagram
      die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> You sure you entering a valid Instagram URL?.</div>');
    }
    if (check_200($urlx)==false){//Check Post is Not Avalaible Or Private
      die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Post Is Deleted or Private.</div>');
    }

    if (!contains($urlx,"/p/")){
      $jsondata = json_decode("{".get_string_between(get_content($urlx),"window._sharedData = {","};")."}",true);
      $totalcontent = $jsondata['entry_data']['ProfilePage'][0]['graphql']['user'];
      $igusername = $totalcontent['username'];
      $iguid = $totalcontent['id'];
      $owner = $totalcontent;
      $pageall = $totalcontent['edge_owner_to_timeline_media']['edges'];
      if(empty($iguid)){
        die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Cant Find This Instagram Account.</div>');
      }
      $igprofile = IGPROFILE_API($igusername);
      ?>
      <div class="panel panel-default ">
      <div class="panel-heading"><b><?=$igusername?></b></div>
      <div class="panel-body">
      <div class="row">
      <div class="col-sm-2">
      <img class="img-responsive img-circle" onclick="showimg('<?=$totalcontent['profile_pic_url_hd']?>')" width="150" src="<?=$totalcontent['profile_pic_url_hd']?>">
      </div>
      <div class="col-sm-10">
      <h1><b><?= $totalcontent['full_name'] ?></b> (<?= $igusername ?>)</h1>
      <span class="label label-default">Followers:<?=$totalcontent['edge_followed_by']['count']?></span>
      <span class="label label-primary">Following:<?=$totalcontent['edge_follow']['count']?></span>
      <span class="label label-success">Media:<?=$totalcontent['edge_owner_to_timeline_media']['count']?></span>
      <span class="label label-info">IG-UID:<?=$iguid?></span>
      <br><br> <a href="<?=$totalcontent['profile_pic_url_hd']?>&dl=1" class="btn btn-success" role="button">Download Profile Picture</a>
      </div>
      </div>
      </div>
      </div>
      <?php
      foreach($pageall as $data){
        $url_pic = $data['node']['display_url'];
        $short = $data['node']['shortcode'];
      ?>
        <div class="col-sm-4">
          <div class="panel panel-default ">
            <div class="panel-body"><img width="320" onclick="showimg('<?=$url_pic?>')" src="<?=$url_pic?>"></img></div>
            <div class="panel-footer">
              <a href="?url=https://www.instagram.com/p/<?=$short?>/&source=post" class="btn btn-success btn-xs" role="button">See Post</a> 
            </div>
          </div>
        </div>
      <?php
      }
      die();
    }
    $jsondata = json_decode("{".get_string_between(get_content($urlx),"window._sharedData = {","};")."}",true);
    $totalcontent =  $jsondata['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
    $igusername = $totalcontent['username'];
    $iguid = $totalcontent['id'];
    $owner = $totalcontent;
    if (contains($htmlok,'"is_private":true,')){
      die('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> This Instagram Account Is Private.</div>');
    }
    if($totalcontent['is_video']==1){
      $url_video = $totalcontent['video_url'];
      ?>
      <div class="col-sm-12">
        <div class="panel panel-default ">
            <div class="panel-body">
              <center>
                <video width="500" height="300" controls><source src="<?=$url_video;?>" type="video/mp4">Your browser does not support the video tag.</video>
              </center>
            </div>
            <div class="panel-footer">
              <strong><?=$totalcontent['edge_media_to_caption']['edges'][0]['node']['text']?></strong><br><BR>
                <a href="<?=$url_video;?>&dl=1" class="btn btn-success" role="button">Download Videos</a>
            </div>
        </div>
      </div>
      <?php
    }else{
      $first = "<strong>".$totalcontent['edge_media_to_caption']['edges'][0]['node']['text']."</strong><br><BR>";
      if(array_key_exists('edge_sidecar_to_children',$totalcontent)){
      foreach($totalcontent['edge_sidecar_to_children']['edges'] as $data){
        $url_pic = $data['node']['display_url'];
        ?>
         <div class="col-sm-4">
          <div class="panel panel-default ">
            <div class="panel-body"><img width="320" onclick="showimg('<?=$url_pic?>')" src="<?=$url_pic?>"></img></div>
            <div class="panel-footer">
            <?=$first ?>
              <a href="<?=$url_pic?>&dl=1" class="btn btn-success btn-xs" role="button">Download Picture</a> 
            </div>
          </div>
        </div>
        <?php
        $first = "";
      }
      }else{
        $url_pic = $totalcontent['display_url'];
        ?>
         <div class="col-sm-4">
          <div class="panel panel-default ">
            <div class="panel-body"><img width="320" onclick="showimg('<?=$url_pic?>')" src="<?=$url_pic?>"></img></div>
            <div class="panel-footer">
              <?=$first ?>
              <a href="<?=$url_pic?>&dl=1" class="btn btn-success btn-xs" role="button">Download Picture</a> 
            </div>
          </div>
        </div>
        <?php
      }
    }
  }
?>
