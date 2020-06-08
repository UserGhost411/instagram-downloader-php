<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<style>

html {
  position: relative;
  min-height: 100%;
}
body {
  margin-bottom: 40px; /* Margin bottom by footer height */
}
.footer {
  position: fixed;
  bottom: 0;
  width: 100%;
  height: 40px; /* Set the fixed height of the footer here */
  line-height: 40px; /* Vertically center the text there */
  background-color: #f5f5f5;
}
/* Custom page CSS
-------------------------------------------------- */
/* Not required for template or sticky footer method. */



img.modal-img {
  cursor: pointer;
  transition: 0.3s;
}
img.modal-img:hover {
  opacity: 0.7;
}
.img-modal {
  display: none;
  position: fixed;
  z-index: 99999;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.9);
}
.img-modal img {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700%;
}
.img-modal div {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}
.img-modal img, .img-modal div {
  animation: zoom 0.6s;
}
.img-modal span {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
  cursor: pointer;
}
@media only screen and (max-width: 700px) {
  .img-modal img {
    width: 100%;
  }
}
@keyframes zoom {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}

</style>
<body>
<div class="container">
 <h2>Instagram Downloader</h2>
  <p>Download Image / Video / IGTV From Instagram , just input url Instagram Profile / Post Here:</p>
  <form class="form" method="POST" onsubmit="letsgo();return false;">
    <div class="col-sm-10">
      <input type="url" class="form-control" id="igurl" placeholder="Enter Url Instagram" name="igurl" value="<?php
      error_reporting(0);
      if(isset($_GET['url'])&& $_GET['source']=="post"){
        echo $_GET['url'];
        $panggil = "letsgo();";
      }else{
        $panggil ="";
      }
      ?>" required>
    </div>
      <div class="col-sm-2">
    <button type="submit" class="btn btn-default">Submit</button>
     </div>
  </form>
  <div id="hasil">
</div>
</div>
<footer class="footer">
      <div class="container">
        <span class="text-muted"><center>Developed By <a target="_BLANK" href="https://github.com/UserGhost411/instagram-downloader-php">UserGhost411</a></center></span>
      </div>
    </footer>
  <script>
  function showimg(urlnya){
  document.getElementById("imgnya").src=urlnya;
  $("#myModal").modal('show')
  }
  function letsgo(){
  var urlnya = document.getElementById("igurl").value;
   $("#loadmodal").modal('show')
 
   var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
		 $("#loadmodal").modal('hide')
    
		if(this.responseText.trim()=="<br><br><hr>"){
		 document.getElementById("hasil").innerHTML='<br><br><hr><div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong> Error Getting Instagram Content.</div>';
		}else{
		document.getElementById("hasil").innerHTML=this.responseText;
    }}
  };
  xhttp.open("POST", "engine.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("urlx="+urlnya);
  }
    </script>
  <?php echo "<script>$(document).ready(function() {".$panggil."});</script>";?>
 <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">View Image</h4>
        </div>
        <div class="modal-body">
          <center><img style="max-width:700px;" id="imgnya"></center>
        </div>
      </div>
    </div>
  </div>
   <div class="modal fade" id="loadmodal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body">
          <center>
         LOADING , Please Wait
         </center>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
