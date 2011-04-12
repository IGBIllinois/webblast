<?php



$url= 'http://clcluster:6969/';
$conn = @fopen($url, "r");
$html = @file_get_contents($url);
@fclose($conn); 
echo $html;
?>
