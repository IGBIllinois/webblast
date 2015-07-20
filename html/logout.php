<?php

include_once 'includes/main.inc.php';

//initialize ldap authentication object
$authen=new Auth($sqlDataBase,$config_array);
$authen->AuthLogout();

header("Location: index.php");
?>
