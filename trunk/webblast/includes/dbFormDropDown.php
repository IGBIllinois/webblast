<?php
$nDbsOptions="";
$pDbsOptions="";

//$queryNDbs="SELECT id,dbname,description,type FROM dbs WHERE type=\"n\" AND active=1 OR (userid=".$_SESSION['userid']." AND active=0)";
$queryNDbs="SELECT d.id,d.dbname,d.description,d.type,u.netid, d.userid FROM users u RIGHT JOIN  dbs d ON d.userid=u.id WHERE d.type=\"n\" AND (d.active=1 OR (d.userid=".$_SESSION['userid']." AND d.active=0)) ORDER BY d.userid ASC";
$nDbsArray = $sqlDataBase->query($queryNDbs);

//$queryPDbs="SELECT id,dbname,description,type FROM dbs WHERE type=\"p\" AND active=1 OR (userid=".$_SESSION['userid']." AND active=0)";
$queryPDbs="SELECT d.id,d.dbname,d.description,d.type,u.netid, d.userid FROM users u RIGHT JOIN  dbs d ON d.userid=u.id WHERE d.type=\"p\" AND (d.active=1 OR (d.userid=".$_SESSION['userid']." AND d.active=0)) ORDER BY d.userid ASC";
$pDbsArray = $sqlDataBase->query($queryPDbs);

$i=0;
foreach($nDbsArray as $id=>$assoc)
{
	if($assoc['userid']==0)
	{
		$nDbsOptions=$nDbsOptions."MainBlastFormSimple.inputd.options[".$i."]=new Option('".$assoc['description']."','".$assoc['id']."');\n";
	}
	else
	{
		$nDbsOptions=$nDbsOptions."MainBlastFormSimple.inputd.options[".$i."]=new Option('(".$assoc['netid'].") ".$assoc['description']."','".$assoc['id']."');\n";
	}
	$i++;
}

$i=0;
foreach($pDbsArray as $id=>$assoc)
{
	if($assoc['userid']==0)
        {
        	$pDbsOptions=$pDbsOptions."MainBlastFormSimple.inputd.options[".$i."]=new Option('".$assoc['description']."','".$assoc['id']."');\n";
	}
	else
	{
		 $pDbsOptions=$pDbsOptions."MainBlastFormSimple.inputd.options[".$i."]=new Option('(".$assoc['netid'].") ".$assoc['description']."','".$assoc['id']."');\n";
	}
	$i++;
}
?>

<script>
var i;
function fill_capital(i){
var MainBlastFormSimple = document.getElementById("MainBlastFormSimple");
	MainBlastFormSimple.inputd.options.length=0;
switch(i)
{	
case 1:
<?php echo $nDbsOptions; ?>
break;
case 2:
<?php echo $pDbsOptions; ?>
break;
case 3:
<?php echo $pDbsOptions; ?>
break;
case 4:
<?php echo $nDbsOptions; ?>
break;
case 5:
<?php echo $nDbsOptions; ?>
break;
}

}
</script>

