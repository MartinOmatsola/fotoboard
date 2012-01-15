<?php
include_once '../utils.php';

$users = getFiles("./");

print '<html>
	<head><title>F O T O B O A R D  P H O T O S</title>
	</head>
	<body>
	<center>
		<div style="font-family:Tahoma;color:#444444;font-size:16px">
		<h2>USERS TABLE<h2>		
		<table width="400" style="border-style:solid;border-width:thin;border-color:#CFCFE7">
		<tr><td align="center" style="font-family:Tahoma;color:#FFFFFF;font-size:16px;background:#1A1A63" colspan="2"><b>User IDs</b></td></tr>';
foreach ($users as $num => $id) {
	$id = basename($id);
	if ($id != "index.php") {
		$num++;
		print '<tr><td align="right" style="font-family:Tahoma;color:#444444;font-size:14px">' . $num . '</td><td align="center" style="font-family:Tahoma;color:#1A1A63;font-size:14px;background:Lavender"><a href="' . $id . '">' . substr($id, 0, strrpos($id, ".")). '</a></td></tr>';
	}
}

print '</table></div></center></body></html>';
?>
