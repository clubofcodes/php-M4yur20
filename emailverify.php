<html lang="en">
<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Khand:wght@500&display=swap');
        *{
        margin:0;
        padding: 0;
        box-sizing: border-box;
        }
        body  { 
        height: 100vh;
        display: flex;
        font-size: 14px;
        text-align: center;
        justify-content: center;
        align-items: center;
        font-family: 'Khand', sans-serif;   
        }        

        .wrapperAlert {
        width: 500px;
        height: 400px;
        overflow: hidden;
        border-radius: 12px;
        border: thin solid #ddd;           
        }

        .topHalf {
        width: 100%;
        color: white;
        overflow: hidden;
        min-height: 250px;
        position: relative;
        padding: 40px 0;
        background: rgb(0,0,0);
        background: -webkit-linear-gradient(45deg, #019871, #a0ebcf);
        }

        .topHalff {
        width: 100%;
        color: white;
        overflow: hidden;
        min-height: 250px;
        position: relative;
        padding: 40px 0;
        background: rgb(0,0,0);
        background: -webkit-linear-gradient(45deg, 	#FF0000, #FFA07A);
        }

        .topHalf p {
        margin-bottom: 30px;
        }
        svg {
        fill: white;
        }
        .topHalf h1 {
        font-size: 2.25rem;
        display: block;
        font-weight: 500;
        letter-spacing: 0.15rem;
        text-shadow: 0 2px rgba(128, 128, 128, 0.6);
        }
                
        .bg-bubbles{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;            
        z-index: 1;
        }

        li{
        position: absolute;
        list-style: none;
        display: block;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.15);/* fade(green, 75%);*/
        bottom: -160px;

        -webkit-animation: square 20s infinite;
        animation:         square 20s infinite;

        -webkit-transition-timing-function: linear;
        transition-timing-function: linear;
        }
        li:nth-child(1){
        left: 10%;
        }		
        li:nth-child(2){
        left: 20%;

        width: 80px;
        height: 80px;

        animation-delay: 2s;
        animation-duration: 17s;
        }		
        li:nth-child(3){
        left: 25%;
        animation-delay: 4s;
        }		
        li:nth-child(4){
        left: 40%;
        width: 60px;
        height: 60px;

        animation-duration: 22s;

        background-color: rgba(white, 0.3); /* fade(white, 25%); */
        }		
        li:nth-child(5){
        left: 70%;
        }		
        li:nth-child(6){
        left: 80%;
        width: 120px;
        height: 120px;

        animation-delay: 3s;
        background-color: rgba(white, 0.2); /* fade(white, 20%); */
        }		
        li:nth-child(7){
        left: 32%;
        width: 160px;
        height: 160px;

        animation-delay: 7s;
        }		
        li:nth-child(8){
        left: 55%;
        width: 20px;
        height: 20px;

        animation-delay: 15s;
        animation-duration: 40s;
        }		
        li:nth-child(9){
        left: 25%;
        width: 10px;
        height: 10px;

        animation-delay: 2s;
        animation-duration: 40s;
        background-color: rgba(white, 0.3); /*fade(white, 30%);*/
        }		
        li:nth-child(10){
        left: 90%;
        width: 160px;
        height: 160px;

        animation-delay: 11s;
        }

        @-webkit-keyframes square {
        0%   { transform: translateY(0); }
        100% { transform: translateY(-500px) rotate(600deg); }
        }
        @keyframes square {
        0%   { transform: translateY(0); }
        100% { transform: translateY(-500px) rotate(600deg); }
        }

        .bottomHalf {
        align-items: center;
        padding: 35px;
        }
        .bottomHalf p {
        font-weight: 500;
        font-size: 1.05rem;
        margin-bottom: 20px;
        }

        button {
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 12px;            
        padding: 10px 18px;            
        background-color: #019871;
        text-shadow: 0 1px rgba(128, 128, 128, 0.75);
        }
        button:hover {
        background-color: #85ddbf;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email verification</title>
</head>
<body>
<?php 
require_once __DIR__.'/config.php';
require __DIR__.'/helperfuncs.php';
$email=$_GET['email'];
$hash=$_GET['hash'];
$link = getLink();

// Removing the illegal characters from email
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

//Validating
if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
  $sql = "SELECT * FROM users WHERE email='$email'";
  $result = $conn->query($sql);

  $row = $result->fetch_assoc();
  if($row['is_active']=='1'){
    $is_active=true;
  }
  else{
    $is_active=false;
  }
  if($row['hash']==$hash){
    $hash_error=false;
  }
  else{
    $hash_error=true;
  }
  if($row['is_active']=='0'){
      if($row['hash']==$hash){
          $activate = 1;
          $usql = 'UPDATE users SET is_active = ? WHERE email = ?';
          $stmt = $conn->prepare($usql);
          $stmt->bind_param('is', $activate, $email);
          $uresult=$stmt->execute();
      }
  }
  if($uresult and !$is_active and !$hash_error){ 
    $comic_id = getRandomComicId('https://c.xkcd.com/random/comic/');
    $api_url = 'https://xkcd.com/'.$comic_id.'/info.0.json';

    // GET Request
    $json_data = file_get_contents($api_url);

    // Decode JSON data into PHP array
    $comic_data = json_decode($json_data);

    $comic_body = str_replace(array( '(', ')', '[[', ']]', '{{', '}}', 'alt', '"..."', '...' ), '', $comic_data->transcript);							
    $mail->Subject = 'Your Comic ['.$comic_data->safe_title.'] is here!';
    $month = $comic_data->month;
    $day = $comic_data->day;
    $year = $comic_data->year;

    $release_date_ts=strtotime("$year-$month-$day");
    $release_date=date('Y-m-d',$release_date_ts);

    $date=date_create($release_date);
    $rel_date=date_format($date,"l, F jS, Y");

    try {	
      $mail->addAddress($email);
      
      $message = '

      <html>
      <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
      <style>
          .container {
          width: 500px;
          margin: 10px;
          border: 1px solid #fff;
          background-color: #ffffff;
          box-shadow: 0px 2px 7px #292929;
          -moz-box-shadow: 0px 2px 7px #292929;
          -webkit-box-shadow: 0px 2px 7px #292929;
          border-radius: 10px;
          -moz-border-radius: 10px;
          -webkit-border-radius: 10px;
      }
      .mainbody,
      .header,
      .footer {
          padding: 5px;
      }
      .mainbody {
          margin-top: 5px;
      }
      .header {
          text-align:center;
          min-height: 40px;
          height: auto;
          width: 100%;
          resize: both;
          overflow: auto;
          background-color: whiteSmoke;
          border-bottom: 1px solid #DDD;
          border-bottom-left-radius: 5px;
          border-bottom-right-radius: 5px;
      }
      .footer {
          height: 40px;
          background-color: whiteSmoke;
          border-top: 1px solid #DDD;
          -webkit-border-bottom-left-radius: 5px;
          -webkit-border-bottom-right-radius: 5px;
          -moz-border-radius-bottomleft: 5px;
          -moz-border-radius-bottomright: 5px;
          border-bottom-left-radius: 5px;
          border-bottom-right-radius: 5px;
      }
      </style>
      </head>
      <body>
      <h4>Hi '.$email.',</h4><br>
      <div class="container">
      <div class="header">
      <span style="position:relative;top:4px;font-size: 25px;"><strong>'.$comic_data->safe_title.'<strong></span>
      </div>
      <div class="mainbody" style="margin-top:5px;margin-left: 7px;">
          <img src='.$comic_data->img.' style="height:400px;width:96%;">
          <p>'.$comic_body.'</p>
      </div>
      <div class="footer">
          <h3>This Comic was released on '.$rel_date.'</h3>
      </div>
      </div>
      <div style="margin-left:13px;">If you would prefer not to receive comics in future from us
      <a href="'.$link.'/unsubscribe.php?email='.$email.'&token='.$hash.'" style="color:red">unsubscribe here.</a></div>
      </body>
      </html>
      
      ';

      $mail->Body = $message;
      $mail->send();
      $mail->clearAttachments();
      $mail->ClearAllRecipients();
  } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
    
    ?>
  <div class="wrapperAlert">
  <div class="contentAlert">
    <div class="topHalf">

      <p><svg viewBox="0 0 512 512" width="100" title="check-circle">
        <path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
        </svg></p>
      <h1>Congratulations</h1>

    <ul class="bg-bubbles">
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
    </ul>
    </div>

    <div class="bottomHalf">

      <p>Your email is successfully verified! You will start receiving comics in your email shortly.</p>

    </div>

  </div>        

  </div>
  <?php } elseif($is_active and !$hash_error){ ?>

    <div class="wrapperAlert">
  <div class="contentAlert">
    <div class="topHalf">

      <p><svg viewBox="0 0 512 512" width="100" title="check-circle">
        <path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" />
        </svg></p>
      <h1>Already verified</h1>

    <ul class="bg-bubbles">
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
    </ul>
    </div>

    <div class="bottomHalf">

      <p>You are already receiving comics from us, if you don???t find them please check your spam folder.</p>

    </div>

  </div>        

  </div>
  <?php } else{ ?>
  <div class="wrapperAlert">

  <div class="contentAlert">

    <div class="topHalff">

      <p>
          <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 24 24">
          <path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"/></svg>
      </p>
      <h1>Error Occurred</h1>

    <ul class="bg-bubbles">
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
    </ul>
    </div>

    <div class="bottomHalf">

      <p>Some error occurred! Please try again.</p>

    </div>

  </div>        

  </div>

<?php }} else{?>
  <div class="wrapperAlert">

  <div class="contentAlert">

    <div class="topHalff">

      <p>
          <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 24 24">
          <path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"/></svg>
      </p>
      <h1>Error Occurred</h1>

    <ul class="bg-bubbles">
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
      <li></li>
    </ul>
    </div>

    <div class="bottomHalf">

      <p>Some error occurred! Please try again.</p>

    </div>

  </div>        

  </div>
<?php }?>
</body>
</html>