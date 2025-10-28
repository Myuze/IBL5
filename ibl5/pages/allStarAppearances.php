<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$query = "SELECT name, COUNT(*) as appearances
FROM ibl_awards
WHERE Award LIKE '%Conference All-Star'
GROUP BY name;";
$result = $db->sql_query($query);

echo "<html><head><title>All-Star Appearances</title></head>";
echo "<body>";
echo "<H1>All-Star Appearances</H1>";

echo "<table cellpadding=5 border=1>";

while ($row = $db->sql_fetchrow($result)) {
    echo "<tr>
        <td>" . $row['name'] . "</td>
        <td>" . $row['appearances'] . "</td>
    </tr>";
}

echo "</table>";
echo "</body>";
echo "</html>";
