
<table class="navigation">
<tr>
	<td class="navigation<?php echo ($_GET['view']=="paths")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=paths">Configuration</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="userconf")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=userconf">Users</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="databases")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=databases">Databases</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="scheduler")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=scheduler">Scheduler</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="nodestatus")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=nodestatus">Nodes Status</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="jobs")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=jobs">User Jobs</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="uploaddatabase")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=uploaddatabase">Upload Database</a>
	</td>
</tr>
<tr>
	<td class="navigation<?php echo ($_GET['view']=="clusterstatus")?"_selected":""; ?>">
		<a class="navigation" href="admin.php?view=clusterstatus">Cluster Status</a>
	</td>
</tr>
</table>
