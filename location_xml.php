<?php
require("location_config.php");
require "domxml-php4-to-php5.php";

// Start XML file, create parent node
$doc = domxml_new_doc("1.0");
$node = $doc->create_element("markers");
$parnode = $doc->append_child($node);

// Opens a connection to a MySQL server
$connection=mysql_connect ('localhost', $username, $password);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

// Select all the rows in the markers table
$query = "SELECT * FROM hotaru_posts WHERE 1";
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  // Add to XML document node
  $node = $doc->create_element("marker");
  $newnode = $parnode->append_child($node);

  $newnode->set_attribute("name", $row['post_title']);
  $newnode->set_attribute("content", $row['post_content']);
  $newnode->set_attribute("lat", $row['post_lat']);
  $newnode->set_attribute("lng", $row['post_lng']);
  $newnode->set_attribute("type", $row['post_type']);
}

$xmlfile = $doc->dump_mem();
echo $xmlfile;

?>
