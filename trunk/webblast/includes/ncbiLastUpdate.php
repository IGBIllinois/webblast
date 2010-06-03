<?php
$queryAvailableUpdates="SELECT id, updatestatus, updatedate FROM dbupdates  WHERE updatestatus=3 ORDER BY id DESC LIMIT 1";
$availableUpdates = $sqlDataBase->query($queryAvailableUpdates);

echo "<FONT size=2>Last NCBI Update: ".Date('n/j/Y g:i:sA',strtotime($availableUpdates[0]['updatedate']))."</font>";


?>
