<?
$dirbase = "";
$urlbase = "";

// Magnitudes
$mags = array(
"magnitude_j"=>"J",  
"magnitude_h"=>"H", 
"magnitude_k"=>"K");

// File Types
$file_types = array(
"Combined", 
"Finding_Chart", 
"Images", 
"Light_curves",
"Model_fits", 
"Notes",
"Photometry",
"Spectra", 
"RV");

$dbhost = '';
$dbuser = '';
$dbpass = '';
$dbname = '';

// mysql_connect is deprecated. mysqli_connect should be used instead
$link = mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());
?>
