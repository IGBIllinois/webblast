<?php
include "includes/header.php";
?>
<br>
<br>
<br>
<center>
<table>
<?php
echo "<b><br>Downloading Job #".$_GET['job']."</b><br>(File type: [.".$_GET['filetype']."])<br>";
echo "<tr><th colspan=\"2\">Log On to download the file</th></tr>";
?><br>
<FORM ACTION="download.php?job=<?echo $_GET['job']; ?>&filetype=<?echo $_GET['filetype'];?>" METHOD="POST">
<tr><td>IGB Username:</td><td> <INPUT TYPE="TEXT" NAME="loginname"></td></tr>
<tr><td>IGB Password:</td><td> <INPUT TYPE="PASSWORD" NAME="loginpass"></td></tr>
<tr><td colspan="2"><INPUT TYPE="submit" VALUE="Authenticate and Download" name="submitLogon"></td></tr>
</table>
</FORM>
</center>
<?php
include "includes/footer.php";
?>

