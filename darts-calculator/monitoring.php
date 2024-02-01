<?php
require_once('./include/php/session.php');
require_once('./include/php/dbconfig.php');
require_once('./include/php/sqldata.php');
require_once('./include/php/functions.php');

$version = "1.0.13";

//Gibt keinen Monitoring Code
if(!isset($_GET['code'])){

  if(!isset($_GET['code'])){
  $error = "Es wurde kein Monitoring-Code übermittelt.";
  displayErrorSite($error);
  return;
  }
}
else{
  $doesCodeexists = AccessorKeyAlreadyExist($db, $_GET['code']);

  if(!$doesCodeexists){
    $error = "Monitoring-Code '".$_GET['code']."' existiert nicht.";
    displayErrorSite($error);
    return;
  }

}


$gameData = SelectGameData($db, $_GET['code']);

if($gameData != ""){

    $gameData = json_decode($gameData,true);

    /*
    echo "<pre style='color: white;'>";
    print_r($gameData);
    echo "</pre>";
    */

    //Get Values from Data
    $remainingDarts = $gameData['RemainingDarts'];
    $playerData = $gameData['PlayerData'];

    $lastUpdate = SelectLastUpdate($db, $_SESSION['DBAccessorKey']);
    $lastUpdateTimeRelative = $lastUpdate['updateDateRelative'];

      

    //Sorts Array by PlayersTurn "0" or "1", if "1" is there it Stays TOP
    //https://stackoverflow.com/questions/2477496/php-sort-array-by-subarray-value
    usort($playerData, function ($a, $b) {
      return strcmp($b['PlayersTurn'], $a['PlayersTurn']);
    });

    //Display DartsIcon
    $showDart1 = "style='display: block;'";
    $showDart2 = "style='display: block;'";
    $showDart3 = "style='display: block;'";
  
    if ($remainingDarts == 1) {
      $showDart1 = "style='display: block;'";
      $showDart2 = "style='display: none;'";
      $showDart3 = "style='display: none;'";
    } else if ($remainingDarts == 2) {
      $showDart1 = "style='display: block;'";
      $showDart2 = "style='display: block;'";
      $showDart3 = "style='display: none;'";
    } else if ($remainingDarts >= 3) {
      $showDart1 = "style='display: block;'";
      $showDart2 = "style='display: block;'";
      $showDart3 = "style='display: block;'";
    }

    $insertLIString = "";

    for($x = 0; $x < count($playerData);$x++){

        //Define Playercolor
        $playerDIVBorderColor = 'style="border-color: white;"';
        $playerColor = 'style="color: white;"';

        if($playerData[$x]['PlayersTurn'] == "1"){
          $playerDIVBorderColor = 'style="border-color: red;"';
          $playerColor = 'style="color: red;"';
        }

        //Rebuild WonLegs
        $wonLegsString = "";
        for($y = 0; $y < intval($playerData[$x]['Player-WonLegs']); $y++){
          $wonLegsString .= '<i class="fa-solid fa-circle"></i>';
        }

        $insertLIString1 .= '<li id="listelement-' . $x . '" class="sidebar-table-players">
    <div class="col-md-12 col-xs-12 playername" id="playername-' . $x . '" '.$playerDIVBorderColor.'>
        <div class="row">
            <div class="col-md-7 col-xs-7 playernamefield" style="height: 50px;">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <p id="playernamefield-' . $x . '" '.$playerColor.'>' . $playerData[$x]['PlayerName'] . '</p>
                    </div>
                </div>';
                
    if ($x === 0)  {
          $insertLIString1 .=' <div class="row">
                    <div class="col-md-12 col-xs-12 checkouthint">
                        <p id="checkouthint-' . $x . '">'.$playerData[$x]['PlayerCheckouthint'].'</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12 checkouthint2">
                        <p id="checkouthint2-' . $x . '">'.$playerData[$x]['PlayerCheckouthint2'].'</p>
                    </div>
                </div>';
    }
                
        $insertLIString1 .=' 
          </div>
          <div class="col-md-5 col-xs-5 pointstocheck" style="height: 50px;">
              <p class="pointstochecktext" id="pointstocheck-' . $x . '">' . $playerData[$x]['PlayerScore'] . '</p>
          </div>
        </div>';
                
    if ($x === 0)  {
          $insertLIString1 .='
            <div class="row actual-throw">
                <div class="col-md-4 col-xs-4" id="throw-one-' . $x . '">'.$playerData[$x]['Player-Throw-One'].'</div>
                <div class="col-md-4 col-xs-4" id="throw-two-' . $x . '">'.$playerData[$x]['Player-Throw-Two'].'</div>
                <div class="col-md-4 col-xs-4" id="throw-three-' . $x . '">'.$playerData[$x]['Player-Throw-Three'].'</div>
            </div>';
    }
                
        $insertLIString1 .='
        <div class="row no-score">
            <div class="col-md-4 col-xs-4" id="no-score-one-' . $x . '">'.$playerData[$x]['Player-No-Score-One'].'</div>
            <div class="col-md-4 col-xs-4" id="no-score-two-' . $x . '">'.$playerData[$x]['Player-No-Score-Two'].'</div>
            <div class="col-md-4" id="no-score-three-' . $x . '">'.$playerData[$x]['Player-No-Score-Three'].'</div>
        </div>
        <div class="row statistics">
            <div class="col-md-4 col-xs-4 three-darts-avg" id="three-darts-avg-' . $x . '">'.$playerData[$x]['Player-Three-Darts-Avg'].'</div>
            <div class="col-md-4 col-xs-4 highest-score" id="highest-score-' . $x . '">'.$playerData[$x]['Player-Highest-Score'].'</div>
            <div class="col-md-4 col-xs-4 checkout-percentage" id="checkout-percentage-' . $x . '">'.$playerData[$x]['Player-Checkout-Percentage'].'</div>
        </div>
        <div class="row latest-darts-throw">
            <div class="col-md-12 col-xs-12">
                <p class="latest-darts-throw" style="display: inline;" id="latest-darts-throw-score-' . $x . '" >'.$playerData[$x]['Player-Latest-Darts-Throw-Score'].'</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12 won-legs-indicator" id="won-legs-indicator-' . $x . '">'.$wonLegsString.'</div>
        </div>
    </div>
</li>';
    }


}else{
    echo "<p style='color:white;'>Gibt keine GameData zu diesem DBAccessorKey!</p>";
}

?>



<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitoring of Darts Calculator">
    <meta name="author" content="Marcel Huss">
    <meta name="keywords" content="HTML,CSS,JavaScript, Darts Calculator, Monitoring, Darts">
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>
    <!--Favicons-->
    <link rel="apple-touch-icon" sizes="180x180" href="./include/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./include/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./include/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="./include/img/favicons/manifest.json">
    <link rel="mask-icon" href="./include/img/favicons/safari-pinned-tab.svg" color="#277cea">
    <link rel="shortcut icon" href="./include/img/favicons/favicon.ico">
    <meta name="msapplication-config" content="./include/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">


    <title>DartsCalculator - Monitoring</title>
    <!-- Bootstrap core CSS -->
    <link href="./include/css/bootstrap.min.css" rel="stylesheet">
    <link href="./include/css/fontawesome6.all.min.css" rel="stylesheet">
    <link href="./include/css/notification-service.1.0.1.css" rel="stylesheet">
    <link href="./include/css/game.css?v=<?php echo $version;?>" rel="stylesheet">
    <link href="./include/css/monitoring.css?v=<?php echo $version;?>" rel="stylesheet">

    <style>
      @media (min-width: 320px) {
          /*Namebox Styles*/
          .playernamefield p {
              color: white;
              font-family: 'Open Sans', sans-serif;
              font-weight: 700;
              font-size: 30px;
              margin-left: 10px;
          }
          /*Current Points left to check*/
          .pointstochecktext {
              color: white;
              font-family: 'Open Sans', sans-serif;
              font-weight: 400;
              font-size: 50px;
              text-align: right;
              margin-right: 10px;
              float: right;
              /*background-color: grey;*/
          }
          /*Fields throwed*/
          .actual-throw {
              color: white;
              font-family: 'Open Sans', sans-serif;
              font-weight: 700;
              font-size: 30px;
              text-align: center;
              /*border: 3px solid black;*/
          }
          /*Privious Take Style*/
          .latest-darts-throw {
              text-align: left;
          }
          /*General Textstyle for all Elements in Table*/
          .leftinformationtable {
              color: white;
              font-family: 'Open Sans', sans-serif;
              font-weight: 400;
              font-size: 12px;
              /*background-color:#272c31;*/
          }
          /*Checkouthint*/
          .checkouthint p {
              font-size: 20px;
          }
          .checkouthint2 p {
              font-size: 16px;
          }
          /*Darts Img*/
          .darts {
              padding: 0px;
              margin-top: 30px;
          }
          .darts img {
              height: 40px;
              width: 40px;
          }
          .numberblock {
              width: 100%;
              height: 100%;
              margin-top: 5vh;
          }
          .statistics {
              /*background-color:blueviolet; */
              padding-top: 5px;
          }
      }
        @media screen and (orientation: landscape) {
            .playerlist {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }
    </style>


  </head>
  <body class="darkbody">

<div class="container-full">


<!-- <div class="row">
  <div class="col-12 col-md-4 col-lg-4" style="margin-top: 10px; margin-left: 10px;">
    <button id="togglefullscreen" type="button" data-currentMode="compressed" class="btn btn-primary" tabindex="1"><i class="fa-duotone fa-expand fa-lg" aria-hidden="true"></i> Vollbildmodus</button>
  </div>
  <div class="col-12 col-md-3 col-lg-2" style="margin-top: 10px;">
    <button id="showstats" type="button" class="btn btn-primary"><i class="fa-duotone fa-chart-line fa-lg"></i> Statistiken</button>
  </div>
  <div class="col-12 col-md-3 col-lg-4" style="margin-top: 10px;">
    <div class="form-group">
      <span class="switch switch-lg">
        <input type="checkbox" class="switch" id="refreshtoggle" checked>
        <label for="refreshtoggle"> Aktualisierung</label>
      </span>
    </div>
  </div>
</div> -->

  


  <div class="row">

    <div class="col-12 col-md-1 col-xl-1 darts" style="margin-left:15px; width:40px;"> <!--style="width:3.3333%;-->
      <img id="dart1" src="include/img/dart-icon-white.png" <?php echo $showDart1;?>></img>
      <img id="dart2" src="include/img/dart-icon-white.png" <?php echo $showDart2;?>></img>
      <img id="dart3" src="include/img/dart-icon-white.png" <?php echo $showDart3;?>></img>
      <button id="togglefullscreen" type="button" class="btn btn-primary" width="40px" style="position:absolute; top:170px; left:5px" ><i class="fa-duotone fa-expand fa-lg" aria-hidden="true"></i></button>
      <button id="showstats"        type="button" class="btn btn-primary" width="40px" style="position:absolute; top:220px; left:5px" ><i class="fa-duotone fa-chart-line fa-lg"></i></button>
    </div>

    <div class="col-12 col-md-11 col-xl-11 leftinformationtable">
        <ul id="playerlist" class="playerlist">
            <?php echo $insertLIString1; ?>
        </ul>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
    <p id="lastupdated" style="color:white; position:absolute; bottom:1px; left:5px ">Updated: <?php echo $lastUpdateTimeRelative;?></p>
    </div>
  </div>

  <!---Modal -->
  <div id="modal" class="modal" tabindex="-1">
    <div class="modal-dialog"> <!--modal-xl-->
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="modal-title" class="modal-title">MODAL_TITLE</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div id="modal-body" class="modal-body"></div>
        <div id="modal-footer" class="modal-footer"></div>
      </div>
    </div>
  </div>

</div>

    <!-- JS Librarys at the end -->
    <script type="text/javascript" src="include/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="include/js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="include/js/fontawesome6.all.min.js"></script>
    <script type="text/javascript" src="include/js/notification.1.0.1.js"></script>
    <script type="text/javascript" src="include/js/monitoring.js?v=<?php echo $version;?>"></script>

<script>
function updateScreenWidth() {
    document.getElementById('widthValue').innerText = window.innerWidth;
}

// Update screen width on load and on resize
window.onload = updateScreenWidth;
window.onresize = updateScreenWidth;
</script>


  </body>
</html>    