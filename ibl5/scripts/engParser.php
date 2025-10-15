<?php

require 'config.php';
mysql_connect($dbhost, $dbuname, $dbpass);
@mysql_select_db($dbname) or die("Unable to select database");

$leagueFileName = $db->sql_result($db->sql_query("SELECT value FROM ibl_settings WHERE name = 'League File Name'"), 0);
$engFile = fopen("$leagueFileName.eng", "rb");
$engArray = array();

while (!feof($engFile)) {
    $line = fgets($engFile);
    if (!mb_eregi('   ', $line)) {
        preg_match("/(.*), (.*)/", $line, $tempArray);
        $key = (string) $tempArray[1];
        $value = (int) $tempArray[2];
        $engArray[$key] = $value;
    }
}

if (mb_eregi('engParser.php', $_SERVER['REQUEST_URI'])) {
    var_dump($engArray);
}
