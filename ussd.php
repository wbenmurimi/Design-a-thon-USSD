<?php
require "connection.php";
// Reads the variables sent via POST from our gateway
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];


if($conn->connect_error)
{
 $response ="CON could not connect to the database";
 die(print(json_encode($response)));
}

if ( $text == "" ) {

 // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";

}

else if ( $text == "1" ) {
  // English Users
  $response = "CON Weather forecast. Choose a week \n";
  $response .= "1. This week \n";
  $response .= "2. Next week \n";
  $response .= "3. Feedback \n";
  $response .= "10. Home ";
  
}

else if($text == "2") {

  // Kinyarwanda Users
  $response = "CON Weather forecast. Choose a week \n";
  $response .= "1. Iki cyumweru \n";
  $response .= "2. Igitaha \n";
  $response .= "3. Uko byagenze \n";
  $response .= "10. Ahabanza ";
  // This is a terminal request. Note how we start the response with END
  //$response = "END Your phone number is $phoneNumber";
}

else if ( $text == "1*2" ) {
  // English Users
  $response = "CON Sorry, weather forecast is not available\n";
  $response .= "10. Home ";
  
}  
else if ( $text == "2*2" ) {
  // Kinyarwanda Users
  $response = "CON Ntabwo iteganyagihe ribabshije kuboneka\n";
  $response .= "10. Ahabanza ";
  
}

else if ( $text == "10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if($text == "1*1") {
  // English Users This week
  $response = "CON Choose a day to see more details \n";

  $mysql_qry1="SELECT DAYOFWEEK(forecast_date) as w_day,morning,night FROM forecast_data WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW())";
  $result1 = mysqli_query($conn ,$mysql_qry1);

  if(mysqli_num_rows($result1) > 0) 
  {
    $i=0;
    while($row = $result1->fetch_assoc())
    {
      $i=$i+1;
      $response .= "".$row['w_day']." : ".$row['morning']."\n";  
    }

  }

  $response .= "10. Home ";

  // This is a second level response where the user selected 1 in the first instance 
}

else if ( $text == "2*1" ) {

  // Kinyarwanda Users This week
  $response = "CON Hitamo umunsi umenye ibiwerekeye\n";
  $mysql_qry1="SELECT DAYOFWEEK(forecast_date) as w_day,morning,night FROM forecast_data WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW())";
  $result1 = mysqli_query($conn ,$mysql_qry1);

  if(mysqli_num_rows($result1) > 0) 
  {
    $i=0;
    while($row = $result1->fetch_assoc())
    {
      $i=$i+1;
      $response .= "".$row['w_day']." : ".$row['morning']." \n";  
    }

  }
  $response .= "10. Ahabanza";

}

else if($text == "1*3") {

  // English Users feedback
  $response = "CON Was this information true yesterday? \n";
  $mysql_qry1="SELECT morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);

  if(mysqli_num_rows($result1) > 0) 
  {
    $i=0;
    while($row = $result1->fetch_assoc())
    {
      $i=$i+1;
      $response .="Morning: ".$row['morning']." \n";
      $response .="Afternoon: ".$row['afternoon']." \n";
      $response .="Night: ".$row['night']." \n";  
    }

  }

  $response .= "1. Yes \n";
  $response .= "2. No \n";
  $response .= "3. Maybe \n";
  $response .= "10. Home ";
}
else if($text == "2*3") {

  // Kinyarwanda Users feedback
  $response = "CON Ni ukweli? \n";
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);

  if(mysqli_num_rows($result1) > 0) 
  {
    $i=0;
    while($row = $result1->fetch_assoc())
    {
      $i=$i+1;
      $response .="Mugitondo: ".$row['morning']." \n";
      $response .="Ikigoroba: ".$row['afternoon']." \n";
      $response .="Nijoro: ".$row['night']." \n";  
    }

  }
  $response .= "1. Yego \n";
  $response .= "2. Oya \n";
  $response .= "3. Ntabyo nzi \n";
  $response .= "10. Ahabanza ";

}
else if($text == "1*3*1") { 
  // English Users feedback tank you
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);
 $f_id=1;
  if(mysqli_num_rows($result1) > 0) 
  {
    while($row = $result1->fetch_assoc())
    {

      $f_id = $row['id']; 
    }

  }

 $sql1 = "INSERT INTO feedback (farmer_id, forecast_id,response) VALUES(1,'$f_id',1)";
 mysqli_query($conn,$sql1);

 $response = "END Thank you for your feedback. \n";
}
else if($text == "1*3*3") { 
  // English Users feedback tank you
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);
 $f_id=1;
  if(mysqli_num_rows($result1) > 0) 
  {
    while($row = $result1->fetch_assoc())
    {

      $f_id = $row['id']; 
    }

  }
  $sql2 = "INSERT INTO feedback (farmer_id, forecast_id,response) VALUES(1,'$f_id',3)";
  mysqli_query($conn,$sql2);
  $response = "END Thank you for your feedback. \n";
}
else if($text == "1*3*2") { 
  // English Users feedback tank you
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);
 $f_id=1;
  if(mysqli_num_rows($result1) > 0) 
  {
    while($row = $result1->fetch_assoc())
    {

      $f_id = $row['id']; 
    }

  }
  $sql2 = "INSERT INTO feedback (farmer_id, forecast_id,response) VALUES(1,'$f_id',2)";
  mysqli_query($conn,$sql2);
  $response = "END Thank you for your feedback. \n";
}
else if($text == "2*3*1") { 
  // English Users feedback tank you
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);
 $f_id=1;
  if(mysqli_num_rows($result1) > 0) 
  {
    while($row = $result1->fetch_assoc())
    {

      $f_id = $row['id']; 
    }

  }
  $sql3 = "INSERT INTO feedback ((farmer_id, forecast_id,response) VALUES(1,'$f_id',1)";
  mysqli_query($conn,$sql3);
  $response = "END Urakoze kuduha amakuru. \n";
}
else if($text == "2*3*2") { 
  // Kinyarwanda Users feedback tank you
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);
  $f_id=1;
  if(mysqli_num_rows($result1) > 0) 
  {
    while($row = $result1->fetch_assoc())
    {

      $f_id = $row['id']; 
    }

  }
  $sql4 = "INSERT INTO feedback (farmer_id, forecast_id,response) VALUES(1,'$f_id',2)";
  mysqli_query($conn,$sql4);
  $response = "END Urakoze kuduha amakuru. \n";
}
else if($text == "2*3*3") { 
  // Kinyarwanda Users feedback tank you
  $mysql_qry1="SELECT id,morning,afternoon,night, min_temp, max_temp FROM forecast_data WHERE DATE(forecast_date)= DATE(NOW() - INTERVAL 1 DAY)";
  $result1 = mysqli_query($conn ,$mysql_qry1);
 $f_id=1;
  if(mysqli_num_rows($result1) > 0) 
  {
    while($row = $result1->fetch_assoc())
    {

      $f_id = $row['id']; 
    }

  }
  $sql4 = "INSERT INTO feedback (farmer_id, forecast_id,response) VALUES(1,'$f_id',3)";
  mysqli_query($conn,$sql4);
  $response = "END Urakoze cyhane for feedback. \n";
}
else if ( $text == "1*10" ) {
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}

else if ( $text == "1*10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if ( $text == "2*10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if ( $text == "1*3*10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if ( $text == "2*3*10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if ( $text == "1*1*10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if ( $text == "2*1*10" ) {

   // This is the first request. Note how we start the response with CON
 $response  = "CON Welcome, Choose your language \n";
 $response .= "1. English \n";
 $response .= "2. Kinyarwanda ";
}
else if($text == "1*1*1") {
  // English Users Show agronomist comments - Monday
 $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=1 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
 $result3 = mysqli_query($conn ,$mysql_qry3);

 if(mysqli_num_rows($result3) > 0) 
 {
  $response = "END Monday tatistics \n";
  while($row2 = $result3->fetch_assoc())
  { 
    $response .= "Min temp: ".$row2['min_temp']."  \n";
    $response .= "Max temp: ".$row2['max_temp']."  \n";     
    $response .= "Morning: ".$row2['morning']."  \n";
    $response .= "Afternoon: ".$row2['afternoon']."  \n";
    $response .= "Night: ".$row2['night']."  \n"; 
    $response .= "Suggestion: ".$row2['description'].""; 
  } 

}
}
else if($text == "1*1*2") {
  // English Users Show agronomist comments - Tuesday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=2 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Tuesday statistics \n";
    while($row2 = $result3->fetch_assoc())
    { 
      $response .= "Min temp: ".$row2['min_temp']."  \n";
      $response .= "Max temp: ".$row2['max_temp']."  \n";     
      $response .= "Morning: ".$row2['morning']."  \n";
      $response .= "Afternoon: ".$row2['afternoon']."  \n";
      $response .= "Night: ".$row2['night']."  \n"; 
      $response .= "Suggestion: ".$row2['description'].""; 
    } 

  }
}
else if($text == "1*1*3") {
  // English Users Show agronomist comments - Wednesday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=2 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Wednesday statistics \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Min temp: ".$row2['min_temp']."  \n";
    $response .= "Max temp: ".$row2['max_temp']."  \n";     
    $response .= "Morning: ".$row2['morning']."  \n";
    $response .= "Afternoon: ".$row2['afternoon']."  \n";
    $response .= "Night: ".$row2['night']."  \n"; 
    $response .= "Suggestion: ".$row2['description'].""; 
    } 

  }
}
else if($text == "1*1*4") {
  // English Users Show agronomist comments - Thursday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=4 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Thursday statistics \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Min temp: ".$row2['min_temp']."  \n";
    $response .= "Max temp: ".$row2['max_temp']."  \n";     
    $response .= "Morning: ".$row2['morning']."  \n";
    $response .= "Afternoon: ".$row2['afternoon']."  \n";
    $response .= "Night: ".$row2['night']."  \n"; 
    $response .= "Suggestion: ".$row2['description'].""; 
    } 

  }
}
else if($text == "1*1*5") {
  // English Users Show agronomist comments - Thursday
 $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=5 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
 $result3 = mysqli_query($conn ,$mysql_qry3);

 if(mysqli_num_rows($result3) > 0) 
 {
  $response = "END Friday statistics \n";
  while($row2 = $result3->fetch_assoc())
  { 
    $response .= "Min temp: ".$row2['min_temp']."  \n";
    $response .= "Max temp: ".$row2['max_temp']."  \n";     
    $response .= "Morning: ".$row2['morning']."  \n";
    $response .= "Afternoon: ".$row2['afternoon']."  \n";
    $response .= "Night: ".$row2['night']."  \n"; 
    $response .= "Suggestion: ".$row2['description'].""; 
  } 

}

}
else if($text == "1*1*6") {
  // English Users Show agronomist comments - Friday
 $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=6 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
 $result3 = mysqli_query($conn ,$mysql_qry3);

 if(mysqli_num_rows($result3) > 0) 
 {
  $response = "END Saturday statistics \n";
  while($row2 = $result3->fetch_assoc())
  { 
    $response .= "Min temp: ".$row2['min_temp']."  \n";
    $response .= "Max temp: ".$row2['max_temp']."  \n";     
    $response .= "Morning: ".$row2['morning']."  \n";
    $response .= "Afternoon: ".$row2['afternoon']."  \n";
    $response .= "Night: ".$row2['night']."  \n"; 
    $response .= "Suggestion: ".$row2['description'].""; 
  } 

}

}
else if($text == "1*1*7") {
  // English Users Show agronomist comments - Sunday
 $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=7 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
 $result3 = mysqli_query($conn ,$mysql_qry3);

 if(mysqli_num_rows($result3) > 0) 
 {
  $response = "END Sunday statistics \n";
  while($row2 = $result3->fetch_assoc())
  { 
    $response .= "Min temp: ".$row2['min_temp']."  \n";
    $response .= "Max temp: ".$row2['max_temp']."  \n";     
    $response .= "Morning: ".$row2['morning']."  \n";
    $response .= "Afternoon: ".$row2['afternoon']."  \n";
    $response .= "Night: ".$row2['night']."  \n"; 
    $response .= "Suggestion: ".$row2['description'].""; 
  } 

}
}

else if($text == "2*1*1") {
  // English Users Show agronomist comments
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=1 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Kuwambere inama uhabwa \n";
    while($row2 = $result3->fetch_assoc())
    { 
      $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
      $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
      $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
      $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
      $response .= "Nijoro: ".$row2['night']."  \n"; 
      $response .= "Inama uhabwa: ".$row2['description'].""; 
    } 

  }
}
else if($text == "2*1*2") {
 $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=2 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
 $result3 = mysqli_query($conn ,$mysql_qry3);

 if(mysqli_num_rows($result3) > 0) 
 {
  $response = "END Kuwakabiri inama uhabwa \n";
  while($row2 = $result3->fetch_assoc())
  { 
    $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
    $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
    $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
    $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
    $response .= "Nijoro: ".$row2['night']."  \n"; 
    $response .= "Inama uhabwa: ".$row2['description']."";
  } 

}
}
else if($text == "2*1*3") {
  // English Users Show agronomist comments - Wednesday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=3 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Kuwagatatu inama uhabwa \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
    $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
    $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
    $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
    $response .= "Nijoro: ".$row2['night']."  \n"; 
    $response .= "Inama uhabwa: ".$row2['description']."";
    } 

  }

}
else if($text == "2*1*4") {
  // English Users Show agronomist comments - Thursday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=4 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Kuwakane inama uhabwa \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
    $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
    $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
    $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
    $response .= "Nijoro: ".$row2['night']."  \n"; 
    $response .= "Inama uhabwa: ".$row2['description']."";
    } 

  }

}
else if($text == "2*1*5") {
  // English Users Show agronomist comments - Thursday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=5 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Kuwagatanu inama uhabwa \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
    $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
    $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
    $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
    $response .= "Nijoro: ".$row2['night']."  \n"; 
    $response .= "Inama uhabwa: ".$row2['description'].""; 
    } 

  }

}
else if($text == "2*1*6") {
  // English Users Show agronomist comments - Friday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=6 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Kuwagatandatu inama uhabwa \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
    $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
    $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
    $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
    $response .= "Nijoro: ".$row2['night']."  \n"; 
    $response .= "Inama uhabwa: ".$row2['description']."";
    } 

  }

}
else if($text == "2*1*7") {
  // English Users Show agronomist comments - Sunday
  $mysql_qry3="SELECT DAYOFWEEK(forecast_date) as w_day,morning,afternoon,night, min_temp, max_temp, description FROM forecast_data, suggestions WHERE YEARWEEK(forecast_date)=YEARWEEK(NOW()) AND DAYOFWEEK(forecast_date)=7 AND YEARWEEK(start_from)=YEARWEEK(NOW()) LIMIT 1";
  $result3 = mysqli_query($conn ,$mysql_qry3);

  if(mysqli_num_rows($result3) > 0) 
  {
    $response = "END Kucyumweru inama uhabwa \n";
    while($row2 = $result3->fetch_assoc())
    { 
    $response .= "Ubushyuhe bwo hejuru: ".$row2['min_temp']."  \n"; 
    $response .= "Ubushyuhe bwo hasi: ".$row2['max_temp']."  \n";     
    $response .= "Ikigoroba: ".$row2['morning']."  \n"; 
    $response .= "Ikigoroba: ".$row2['afternoon']."  \n"; 
    $response .= "Nijoro: ".$row2['night']."  \n"; 
    $response .= "Inama uhabwa: ".$row2['description'].""; 
    } 

  }

}

// Print the response onto the page so that our gateway can read it
header('Content-type: text/plain');
echo $response;

?>