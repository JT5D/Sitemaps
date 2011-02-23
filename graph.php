<?php
include('config.php');
include('includes/functions.php');
include('includes/mysql_functions.php');

$pages = mysql_array(mysql_query("SELECT * FROM `urls` WHERE `clicks` <= '3' AND `type` LIKE '%text/html%'"));
$sql = "SELECT max(clicks) FROM `urls`";
$max_clicks = mysql_result(mysql_query($sql),0,0);
if (mysql_error()) die();

if (!isset($_GET['pageID'])) {
	$page_keys = array_keys($pages);
	$seed_page = $page_keys[0];
} else {
	$seed_page = $_GET['pageID'];
}
$page = $pages[$seed_page];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if (strlen($page['title'])>0) echo $page['title'];
		else echo $page['url']; ?> Sitemap</title>
	<link rel="stylesheet" type="text/css" media="screen, print" href="slickmap.css" />
</head>

<?php 

function print_page($pageID) {
	global $pages;
	global $rels;
	global $seed_page;
	
	$page = $pages[$pageID];
	if (!isset($page['incomming'])) {
		$sql = "SELECT (SELECT Count(*) FROM links WHERE `to` = urls.ID) as popularity FROM `urls` WHERE `ID` = '".$pageID."'";
		$page['incomming'] = mysql_result(mysql_query($sql),0,0);		
	}
	echo "\t<li";
	if ($pageID == $seed_page) echo " id='home'";
	if ($page['title'] == "Page Not Found") echo " id=fourohfour";
	echo "><a href=?pageID=". urlencode($pageID) ." title='" . $page['url'] . "'>";
	if (strlen($page['title'])>0) echo $page['title'];
		else echo $page['url'];
	echo "</a>\r\n";
	if  (array_key_exists($pageID,$rels)) {
		if ($pageID != $seed_page)  echo "\t\t<ul>\r\n";
		foreach($rels[$pageID] as $childID) {
				print_page($childID);
			}
			if ($pageID != $seed_page)  echo "\t\t</ul>\r\n";
	}
	echo "\t</li>\r\n";
}

$max_clicks = 3;

if (!isset($_GET['refresh'])) {
	
	include('rels.php'); 

} else {

	for ($clicks = 1; $clicks <= $max_clicks; $clicks++) {
			$sql = "SELECT *, (SELECT Count(*) FROM links WHERE `to` = urls.ID) as popularity FROM `urls` INNER JOIN `links` ON urls.ID = links.from WHERE `clicks` = '$clicks' AND `type` LIKE '%text/html%' ORDER BY popularity DESC, `title` ASC";
		$this_level = mysql_array(mysql_query($sql));
		foreach ($this_level as $this_page) {
			$sql = "SELECT ID, (SELECT Count(*) FROM links WHERE `to` = urls.ID) as popularity FROM `urls` INNER JOIN `links` ON urls.ID = links.from WHERE `clicks` < '$clicks' AND `to` = '".$this_page['ID']."' ORDER BY popularity DESC, `title` ASC LIMIT 1";
			$parent = mysql_result(mysql_query($sql),0,0);		
			$rels[$parent][] = $this_page['ID'];
		}
	}

}

?>
<body style='width: <?php echo sizeof($rels[$seed_page]) * 200; ?>px;'>
<div class="sitemap">

	<h1><?php if (strlen($page['title'])>0) echo $page['title'];
		else echo $page['url']; ?> Sitemap</h1>
<h2>Preliminary Sitemap - Includes first three levels, HTML files only</h2>

	<ul id="primaryNav">

	<?php


	print_page($seed_page);

	?>
	</ul>
</div>
</body>

</html>