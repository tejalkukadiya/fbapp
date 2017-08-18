<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>

  <script>

  $(document).ready(
  function () {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("form1").innerHTML = this.responseText;
      }
    };
    xhttp.open("POST", "controller.php?load=1", true);
    xhttp.send();
  });

  function down() {

    $data = $("#form1").serialize();
    // alert($data);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("msg").innerHTML =this.responseText;
      }
    };
    xhttp.open("POST", "controller.php?down=1&"+$data, true);
    xhttp.send();
  }
</script>
</head>
<body>
    <?php

if (!session_id()) {
    session_start(); 
}
require_once __DIR__.'/vendor/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '1622173214524006', 
  'app_secret' => '2a271598a2e13b4b966b9fd36f78630d',
  'default_graph_version' => 'v2.10',
  ]);

$helper = $fb->getRedirectLoginHelper();

if(isset($_GET['state'])){
  $helper->getPersistentDataHandler()->set('state',$_GET['state']);
}


try {
  
$token=$helper->getAccessToken();
// var_dump($token);
$_SESSION['fb_access_token']=(string) $token;
echo $_SESSION['fb_access_token'];

 $fb->setDefaultAccessToken($_SESSION['fb_access_token']);
// echo $token;
} catch (Exception $e) {
  
}

$permissions = ['email','user_photos']; // Optional permissions

// $loginUrl = $helper->getLoginUrl('http://localhost/fbapp/', $permissions);
$loginUrl = $helper->getLoginUrl('http://fbprofilefatch.azurewebsites.net/', $permissions);

?>
    <div class="container main">
      <div class="head row">

          <?php

              if (($_SESSION['fb_access_token'])== NULL) {
                  echo"hii welcome user";

                  echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';

              }else{
                $response=$fb->get('/me/?fields=name,picture,email',$_SESSION['fb_access_token']);
                $data=$response->getGraphNode();
            ?>

          <div class="col-md-3">
          <img src="<?php echo $data['picture']['url']; ?>" height="180px" width="180px">
          </div>
          <div class="profile col-md-9">
          <h2> User Profile</h2>
          <hr>
          <h4> <span>Name:</span> <?php
            
            
            // echo "<br/>";

           
           echo $data['name']; ?> </h4>
          <h4> <span>email id:</span> <?php  echo $data['email']?></h4>
          <h4> <span>place :</span>  Tejal Kukadiya </h4>
          </div>
      </div>

      <hr>
        <form id="form1" method="POST">
        
        
      </form>
      <button value="submit" onclick="down()">Download</button>
     <div id="msg">
       
     </div>
    
  <?php
  		$response = $fb->get('/me/?fields=albums');
        $data1=$response->getGraphNode();
  
      foreach ( $data1['albums'] as $key ) {
          
           echo '
            <div class="profile-albums">
               <h2> '.$key['name'].'</h2>
               <hr>
               <div class="row">
                

                  ';
                	$id=$key['id'];
                   $response = $fb->get('/'.$id.'/photos/?fields=source,name,image',$token);
                   $data2=$response->getGraphEdge();
                   foreach ($data2 as $key) {

                   	// $imageurl[$i]=$key['source'];
                   	// $i++;
                      echo '<div class="col-md-4"> <img src="'.$key['source'].'">
                    	 </div>';
                 }
                 echo ";
               </div>
             </div>";
	          }


}
  ?>
  				</div>
	          </body>
</html>