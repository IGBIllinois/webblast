<hr><a href="index.php?view=uploadform">
<?php
if(!isset($_GET['view']))
{
	$_GET['view']="uploadform";
}

if($_GET['view']=="uploadform")
{
	echo "<font color=\"black\">Upload Queries</font>";
}
else
{
	echo "Upload Queries";
}
?>
</a> &nbsp;&nbsp;&nbsp; 

<a href="index.php?view=jobs">
<?php
if($_GET['view']=="jobs")
{
	echo "<font color=\"black\"> View Jobs</font>";
}
else
{
	echo "View Jobs";
}
?>
</a> &nbsp;&nbsp;&nbsp; 

<a href="index.php?view=createdatabase">
<?php
if($_GET['view']=="createdatabase")
{
	echo "<font color=\"black\">Create Database</font>";
}
else
{
	echo "Create Database";
}
?>
</a> &nbsp;&nbsp;&nbsp; 

<a href="index.php?view=managedatabases">
<?php
if($_GET['view']=="managedatabases")
{
	echo "<font color=\"black\">Manage Databases</font>";
}
else
{
	echo "Manage Databases";
}
?>
</a>&nbsp;&nbsp;&nbsp; 

<a href="index.php?view=clusterstatus">
<?php
if($_GET['view']=="clusterstatus")
{
	echo "<font color=\"black\">Cluster Status</font>";
}
else
{
	echo "Cluster Status";
}
?>
</a>
<hr>
