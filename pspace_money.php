<?php
 
// Zend library include path
set_include_path(get_include_path() . PATH_SEPARATOR . "$_SERVER[DOCUMENT_ROOT]/ZendGdata-1.8.1/library");
 
include_once("Google_Spreadsheet.php");
 
$u = "pspace.money@gmail.com";
$p = "pspace22A";
 
$ss = new Google_Spreadsheet($u,$p);
$ss->useSpreadsheet("P-Space money");
 
// if not setting worksheet, "Sheet1" is assumed
//$ss->useWorksheet("Συνδρομές");
$email_address = 'takisgr@gmail.com';
//$email_row = "'";
$email_row = 'email="';
$email_row .= $email_address;
$email_row .= '"';
//$email_row .= "'";
// double quotes must be used for values with spaces
$rows = $ss->getRows($email_row);
 
if ($rows) {
$message = "Το μέλος ".$rows['0']['name'];
if ($rows['0']['months'] == 0) {
	$message .= " δεν οφείλει κανένα μήνα !\n";
	$message .= "Ευχαριστούμε !\n";
}
else if ($rows['0']['months'] == 1) {
	$message .= " οφείλει ένα μήνα !\n";
}
else $message .= " οφείλει ".$rows['0']['months']." μήνες !\n";
mail("monopatis@gmail.com","P-space money",$message);
}
else echo "Error, unable to get spreadsheet data";
 
?>
