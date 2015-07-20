<?php

include_once 'includes/main.inc.php';



if($authen->AuthSession())
{

		//include "includes/ncbiLastUpdate.php";
		if(isset($_GET['view']))
		{
			$file_name = $_GET['view'] . ".php";
			if (file_exists('includes/' . $file_name)) {
				include_once "includes/" . $file_name;
			}
			elseif($_GET['view']=='class') {
				include "includes/uploadfasta_simple.php";
                                include "includes/class_simple.php";
                        }
			else {
				include "includes/uploadfasta_simple.php";
				include "includes/mainform_simple.php";
			}
		}
		else {
			include "includes/uploadfasta_simple.php";
			include "includes/mainform_simple.php";
		}
}
else {
	include_once "includes/login.php";
}
include_once "includes/footer.php";
?>
