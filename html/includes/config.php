<?php

ini_set('upload_max_filesize', '2000MB');
ini_set('post_max_size', '2000MB');

//Path to ini config file, I recommend placing this file in the user folder
//So it is not accessible via the website.
$config_file_path="../etc/webblast.conf";

//Parse the ini file
$config_array=parse_ini_file($config_file_path,true);

//Add the config file path to the array so we can send it to perl scripts
$config_array['config_path']=array('config_file_path'=>$config_file_path);

?>
