<?php
//include "includes/header.php";
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication canceled, refresh the browser to authenticate.';
    exit;
?>
