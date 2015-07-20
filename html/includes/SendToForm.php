<?php

?>
<br><br><b>Secure File Copy Form</b><br>
<table>
<tr><th colspan=2><b>Secure File Copy Form (SCP)</b></th></tr>
<tr><td>Job #: </td><td> <b><?php echo $_GET['job']; ?></b>
<tr><td>Host: </td><td> <input type="text" name="scpHost"></td></tr>
<tr><td>Path: </td><td><input type="text" name="scpPath"></td></tr>
<tr><td>Username:</td><td> <input type="text" name="scpUser"></td></tr>
<tr><td>Password:</td><td> <input type="password" name="scpPass"></td></tr>
<tr><td></td><td><input type="submit" name="scpSend" value="Transfer"></td></tr>
</table>
