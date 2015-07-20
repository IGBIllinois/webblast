<?php
session_start();

include_once "includes/config.php";
include_once "includes/header.php";

function __autoload($class_name) {
        require_once '../libs/' . $class_name . '.php';
}


//Initialize database
$sqlDataBase= new SQLDataBase($config_array['sql_config']['sql_host'],$config_array['sql_config']['sql_database'],$config_array['sql_config']['sql_user'],$config_array['sql_config']['sql_pass']);

//initialize ldap authentication object
$authen=new Auth($sqlDataBase,$config_array);

//Authenticate submit login
include_once "includes/authenticate.php";

?>
