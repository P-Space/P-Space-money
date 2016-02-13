<?php
if(empty($_POST['email']))
{
    $errors .= "\n Error: email field is required";
}

$myemail = '';//<-----Put Your email address here.
$email_address = $_POST['email'];

if (!preg_match(
"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i",
$email_address))
{
    $errors .= "\n Error: Invalid email address";
}

if( empty($errors))
{
 $csv_url = ''; //Add the url that you got when you published the Google Sheet as csv to the web
 $csv = file_get_contents($csv_url);
 
 $Data = str_getcsv($csv, "\n"); //parse the rows
 foreach($Data as &$Row) $Row = str_getcsv($Row, ","); //parse the items in rows

 $found=0;
 foreach($Data as $i) {
  if(!strcmp($i[1],$email_address)) {
    $message = "Το μέλος ".$i[0];
    if($i[2]<=0) {
		$message .= " δεν οφείλει κανένα μήνα.\n";
			$message .= "Ευχαριστούμε !\n";
		}
		else if ($i[2] == 1) {
			$message .= " οφείλει ένα μήνα.\n";
		}
		else {
			$message .= " οφείλει ".$i[2]." μήνες.\n";
		}

	$to = $email_address;
	$email_subject = "[P-space money] Check my debt !";
	//" Here are the details:\n $rows";
	$headers = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
	$headers .= "From: $myemail\r\n";
	$headers .= "Reply-To: $myemail";

	//echo $message;

	mail($to,$email_subject,$message,$headers);
	//redirect to the 'thank you' page
	header('Location: money-ckeck-form-thanks.html');

  $found=1;
  }
 }

if(!$found)
  $errors .= "Δώσατε λάθος e-mail!";

}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>P-Space Money Check</title>
	<link rel="shortcut icon" href="http://www.p-space.gr/favicon.png" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
{
	font-family : Helvetica, sans-serif, Arial;
	font-size : 12px;
}

</style>
</head>

<body>

<?php
  echo $errors;
?>

</body>
</html>
