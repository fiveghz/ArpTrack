<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SuperTrack</title>

<link href="search-box.css" rel="stylesheet" type="text/css" />
</head>

<body bgcolor=#E0E2F1><center>
<div style="padding: 0 0 0 0;">
<?PHP

/*
--------------------------------------------------
|																								 |
|		ARP Track																		 |
|																								 |
|		Purpose: Poll network devices and maintain	 |
|		a historic database of MAC addresses and IP	 |
|		addresses that have been seen on the network |
|																								 |
|		Version History:														 |
|																								 |
--------------------------------------------------
*/
require_once('include.php');
$timer	= new timer(1);
if ($DEBUG) 
{
	$dbug		= new Debug();
}

echo "
<form action=index.php?a=search method=post>
<div id=search-box>
<H2>SuperTrack</H2>
Search by MAC, IPv4/IPv6, or User!
<br />
<br />
<div id=search-box-name style=margin-top:20px;>MAC:</div>
<div id=search-box-field style=margin-top:20px;>
	<input name=mac class=form-search title=MAC size=30 />
</div>
<div id=search-box-name>IPv4/IPv6:</div>
<div id=search-box-field>
	<input name=ip class=form-search title=IPv4/IPv6 size=30 />
</div>
<div id=search-box-name>User:</div>
<div id=search-box-field>
	<input name=user class=form-search title=User size=30 />
</div>
<br />
<span class=search-box-options>
<button type=submit>Search</button>
</span>

</div>

</div>

";

if (isset($_REQUEST['a']))
{
	if ($_REQUEST['a'] != 'search')
	{
		die("Unknown action!");
	}

	if (isset($_GET['ip']))
	{
		$ip=addslashes(trim($_GET['ip']));
	} elseif (isset($_REQUEST['ip'])) {
		$ip=addslashes(trim($_REQUEST['ip']));
	} else {
		$ip="";
	}
	
	if (isset($_GET['mac']))
	{
		$mac=addslashes(trim($_GET['mac']));
		$mac=NetUtil::formatMacAddress($mac,":");
	} elseif (isset($_REQUEST['mac'])) {
		$mac=addslashes(trim($_REQUEST['mac']));
		$mac=NetUtil::formatMacAddress($mac,":");
	} else {
		$mac="";
	}
	
	if (isset($_GET['user']))
	{
		$user=addslashes(trim($_GET['user']));
	} elseif (isset($_REQUEST['user'])) {
		$user=addslashes(trim($_REQUEST['user']));
	} else {
		$user="";
	}
	
	echo "<table width=1024><tr><td><div class=datagrid><table>
					<thead><tr><th width=20>MAC</th><th width=300>OUI</th><th width=20>IP</th><th width=120>First Seen</th><th width=120>Last Seen</th><th>User</th></tr></thead><tbody>";
					WouldYouLikeFriesWithThat($mac,$ip,$user);
	echo "</tbody></table></div></td></tr></table>";
}
echo "<p>
<pre>
<hr>
SuperTrack Version $version <br>";
if ($DEBUG) 
{
	echo "<font color=red>Debug is enabled!</font></center>";
	#$dbug->setMsg("Query", $query);
	$dbug->printAll();
}
#if ($query) echo "<p>SQL query: $query</p>\n";
#Debug::setMsg("Query", $query);
#Debug::printAll();
echo "<br>Page generated in " . $timer->get() . " seconds
</pre>

</body></html>";

?>
