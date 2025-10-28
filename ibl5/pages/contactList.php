<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$query = "SELECT * FROM ibl_team_info ORDER BY team_city ASC";
$result = $db->sql_query($query);
$num = $db->sql_numrows($result);

$i = 0;

echo "<html>
<head>
    <title>IBL GM Contact List</title>
</head>
<body>
    <center>
        <h2>IBL GM Contact List</h2>
        Click on a team name to access that team's page; click on a GM's name to e-mail the GM.<br>
        <table cellpadding=\"1\" border=\"1\">
            <tr>
                <th>Team</th>
                <th>GM's Name</th>
                <th>AIM</th>
                <th>Skype</th>
            </tr>
";

while ($i < $num) {
    $tid = $db->sql_result($result, $i, "teamid");
    $teamcity = $db->sql_result($result, $i, "team_city");
    $teamname = $db->sql_result($result, $i, "team_name");
    $color1 = $db->sql_result($result, $i, "color1");
    $color2 = $db->sql_result($result, $i, "color2");
    $owner = $db->sql_result($result, $i, "owner_name");
    $email = $db->sql_result($result, $i, "owner_email");
    $skype = $db->sql_result($result, $i, "skype");
    $aim = $db->sql_result($result, $i, "aim");
    $msn = $db->sql_result($result, $i, "msn");

    echo "<tr>
        <td style=\"background-color: #$color1; text-align: center;\">
            <a href=\"team.php?tid=$tid\" style=\"color: #$color2; text-decoration: none; font-weight: bold;\">
                $teamcity $teamname
            </a>
        </td>
        <td style=\"text-align: center;\">
            <a href=\"mailto:$email\" style=\"color: #333; text-decoration: underline;\">
                $owner
            </a>
        </td>
        <td style=\"text-align: center;\">$aim</td>
        <td style=\"text-align: center;\">$skype</td>
    </tr>";

    $i++;

}

echo "</TR></TABLE>
</BODY></HTML>";

$db->sql_close();
