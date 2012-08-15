<?php 
function getRemoteIP ()
  {

    // check to see whether the user is behind a proxy - if so,
    // we need to use the HTTP_X_FORWARDED_FOR address (assuming it's available)

    if (strlen($_SERVER["HTTP_X_FORWARDED_FOR"]) > 0) { 

      // this address has been provided, so we should probably use it

      $f = $_SERVER["HTTP_X_FORWARDED_FOR"];

      // however, before we're sure, we should check whether it is within a range 
      // reserved for internal use (see http://tools.ietf.org/html/rfc1918)- if so 
      // it's useless to us and we might as well use the address from REMOTE_ADDR

      $reserved = false;

      // check reserved range 10.0.0.0 - 10.255.255.255
      if (substr($f, 0, 3) == "10.") {
        $reserved = true;
      }

      // check reserved range 172.16.0.0 - 172.31.255.255
      if (substr($f, 0, 4) == "172." && substr($f, 4, 2) > 15 && substr($f, 4, 2) < 32) {
        $reserved = true;
      }

      // check reserved range 192.168.0.0 - 192.168.255.255
      if (substr($f, 0, 8) == "192.168.") {
        $reserved = true;
      }

      // now we know whether this address is any use or not
      if (!$reserved) {
        $ip = $f;
      }

    } 

    // if we didn't successfully get an IP address from the above, we'll have to use
    // the one supplied in REMOTE_ADDR

    if (!isset($ip)) {
      $ip = $_SERVER["REMOTE_ADDR"];
    }

    // done!
    return $ip;

  }

$errors = '';
$myemail = 'test@example.com';//<-----Put Your email address here.
if(empty($_POST['email']))
{
    $errors .= "\n Error: email field is required";
}

$email_address = $_POST['email']; 

if (!preg_match(
"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", 
$email_address))
{
    $errors .= "\n Error: Invalid email address";
}

if( empty($errors))
{
	//#####################
	// Zend library include path
	set_include_path(get_include_path() . PATH_SEPARATOR . "$_SERVER[DOCUMENT_ROOT]/ZendGdata-1.8.1/library");
 
	include_once("Google_Spreadsheet.php");
 	//google username
	$u = "username@gmail.com";
	//password
	$p = "secret";
 
	$ss = new Google_Spreadsheet($u,$p);
	$ss->useSpreadsheet("P-Space money");
	//
 	// Save log to file
 	//
 	date_default_timezone_set('Europe/Sofia');
 	$somecontent = date('d\/m\/Y h:i:s a');
 	//echo $somecontent;
 	$somecontent = $somecontent."\t";
	$ip = getRemoteIP();
	$somecontent = $somecontent."IP: $ip\t";
	$host = gethostbyaddr($ip);
	$somecontent = $somecontent."HOST: $host\t";
	$somecontent = $somecontent."EMAIL: $email_address\t";
	$somecontent = $somecontent."\n";
	$filename = 'log.txt';

	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {

	    // In our example we're opening $filename in append mode.
	    // The file pointer is at the bottom of the file hence
	    // that's where $somecontent will go when we fwrite() it.
	    if (!$handle = fopen($filename, 'a')) {
	         //echo "Cannot open file ($filename)";
	         exit;
	    }

	    // Write $somecontent to our opened file.
	    if (fwrite($handle, $somecontent) === FALSE) {
	        //echo "Cannot write to file ($filename)";
	        exit;
	    }

	    //echo "Success, wrote ($somecontent) to file (<a href=$filename>$filename</a>)";

	    fclose($handle);

	} else {
	    //echo "The file $filename is not writable";
	}
 	//
	// if not setting worksheet, "Sheet1" is assumed
	//$ss->useWorksheet("f");

	$email_row = 'email="';
	$email_row .= $email_address;
	$email_row .= '"';

	// double quotes must be used for values with spaces
	$rows = $ss->getRows($email_row);
 
	if ($rows) {
		//print_r($rows);
	//	$message=print_r($rows,1);
		$message = "Το μέλος ".$rows['0']['name'];
		if ($rows['0']['months'] == 0) {
			$message .= " δεν οφείλει κανένα μήνα !\n";
			$message .= "Ευχαριστούμε !\n";
		}
		else if ($rows['0']['months'] == 1) {
			$message .= " οφείλει ένα μήνα !\n";
		}
		else $message .= " οφείλει ".$rows['0']['months']." μήνες !\n";
		$message .= "Σύνολο έχει δώσει ".$rows['0']['sum']." Ευρώ !\n";
		$to = $email_address; 
		$email_subject = "[P-space money] Check my debt !";
		//$email_body = "You have received a new message. ".
		//" Here are the details:\n $rows"; 
		$headers = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
		$headers .= "From: $myemail\r\n"; 
		$headers .= "Reply-To: $myemail";
	
		mail($to,$email_subject,$message,$headers);
		//redirect to the 'thank you' page
		header('Location: money-ckeck-form-thanks.html');
	}
	else echo "Error, unable to get spreadsheet data";
	//#####################
	
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
	<title>Contact form handler</title>
	<style type="text/css">
a 
{
	font-family : Arial, Helvetica, sans-serif;
	font-size : 12px; 
}

</style>
</head>

<body>
<!-- This page is displayed only if there is some error -->
<?php
echo nl2br($errors);
?>

</body>
</html>
