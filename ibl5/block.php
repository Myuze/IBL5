<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

use Player\Player;

$sharedFunctions = new Shared($db);

$val = $_GET['day'];

$query = "SELECT ibl_fa_offers.*, ibl_plr.bird
FROM ibl_fa_offers
JOIN ibl_plr ON ibl_fa_offers.name = ibl_plr.name
ORDER BY ibl_fa_offers.name ASC, ibl_fa_offers.perceivedvalue DESC";
$result = $db->sql_query($query);
$num = $db->sql_numrows($result);

echo "<HTML>
	<HEAD><TITLE>Free Agent Processing</TITLE></HEAD>
	<BODY>
        <H1>You are viewing <font color=red>Day " . ($val) . "</font> results!</H1>
        <H2>Total number of offers: $num</H2>
		<TABLE BORDER=1>
			<TR style=\"font-weight:bold\">
				<TD COLSPAN=8>Free Agent Signings</TD>
                <TD>Bird</TD>
				<TD>MLE</TD>
				<TD>LLE</TD>
			</TR>";

$discordText = "";
$offerText = "";
$outcomeText = "";
$autoRejectedText = "These offers have been **auto-rejected** for being under half of the player's demands:";
$lastPlayerIteratedOn = "";
$i = 0;
while ($i < $num) {
    $name = $db->sql_result($result, $i, "name");
    $playerID = $sharedFunctions->getPlayerIDFromPlayerName($name);
    $player = Player::withPlayerID($db, $playerID);
    $teamOfPlayer = Team::initialize($db, $player->teamName);
    $offeringTeamName = $db->sql_result($result, $i, "team");
    $offeringTeam = Team::initialize($db, $offeringTeamName);
    $perceivedvalue = $db->sql_result($result, $i, "perceivedvalue");

    if ($lastPlayerIteratedOn != $player->name) {
        $discordText .= $offerText;
        $offerText = "";
        if ($outcomeText) {
            $discordText .= $outcomeText;
            if ($offerAccepted) {
                $discordText .= " <@!$acceptedTeamDiscordID>\n\n";
            }
        }
        $discordText .= "**" . strtoupper("$player->name, $teamOfPlayer->city $player->teamName") . "** <@!$teamOfPlayer->discordID>\n";
    }

    $offer1 = $db->sql_result($result, $i, "offer1");
    $offer2 = $db->sql_result($result, $i, "offer2");
    $offer3 = $db->sql_result($result, $i, "offer3");
    $offer4 = $db->sql_result($result, $i, "offer4");
    $offer5 = $db->sql_result($result, $i, "offer5");
    $offer6 = $db->sql_result($result, $i, "offer6");

    $birdYears = $db->sql_result($result, $i, "bird");
    $MLE = $db->sql_result($result, $i, "MLE");
    $LLE = $db->sql_result($result, $i, "LLE");
    $random = $db->sql_result($result, $i, "random");

    $query2 = "SELECT * FROM `ibl_demands` WHERE name = '$name'";
    $result2 = $db->sql_query($query2);
    $num2 = $db->sql_numrows($result2);

    $dem1 = $db->sql_result($result2, 0, "dem1");
    $dem2 = $db->sql_result($result2, 0, "dem2");
    $dem3 = $db->sql_result($result2, 0, "dem3");
    $dem4 = $db->sql_result($result2, 0, "dem4");
    $dem5 = $db->sql_result($result2, 0, "dem5");
    $dem6 = $db->sql_result($result2, 0, "dem6");

    $offeryears = 6;
    if ($offer6 == 0) {
        $offeryears = 5;
    }
    if ($offer5 == 0) {
        $offeryears = 4;
    }
    if ($offer4 == 0) {
        $offeryears = 3;
    }
    if ($offer3 == 0) {
        $offeryears = 2;
    }
    if ($offer2 == 0) {
        $offeryears = 1;
    }
    $offertotal = ($offer1 + $offer2 + $offer3 + $offer4 + $offer5 + $offer6) / 100;

    $demyrs = 6;
    if ($dem6 == 0) {
        $demyrs = 5;
    }
    if ($dem5 == 0) {
        $demyrs = 4;
    }
    if ($dem4 == 0) {
        $demyrs = 3;
    }
    if ($dem3 == 0) {
        $demyrs = 2;
    }
    if ($dem2 == 0) {
        $demyrs = 1;
    }
    $demands = ($dem1 + $dem2 + $dem3 + $dem4 + $dem5 + $dem6) / $demyrs * ((11 - $val) / 10);

    if ($perceivedvalue > $demands / 2) {
        $offerText .= "$offeringTeamName - $offer1";
        if ($offer2 != 0) {$offerText .= "/$offer2";}
        if ($offer3 != 0) {$offerText .= "/$offer3";}
        if ($offer4 != 0) {$offerText .= "/$offer4";}
        if ($offer5 != 0) {$offerText .= "/$offer5";}
        if ($offer6 != 0) {$offerText .= "/$offer6";}
        $offerText .= " <@!$offeringTeam->discordID>\n";
    } else {
        $autoRejectedText .= "\n<@!$offeringTeam->discordID>'s offer for $player->name: ";
        $autoRejectedText .= "$offeringTeamName - $offer1";
        if ($offer2 != 0) {$autoRejectedText .= "/$offer2";}
        if ($offer3 != 0) {$autoRejectedText .= "/$offer3";}
        if ($offer4 != 0) {$autoRejectedText .= "/$offer4";}
        if ($offer5 != 0) {$autoRejectedText .= "/$offer5";}
        if ($offer6 != 0) {$autoRejectedText .= "/$offer6";}
    }

    if ($lastPlayerIteratedOn != $name) {
        if ($perceivedvalue > $demands) {
            echo " <TR>
                <TD>$name</TD>
                <TD>$offeringTeamName</TD>
                <TD>$offer1</TD>
                <TD>$offer2</TD>
                <TD>$offer3</TD>
                <TD>$offer4</TD>
                <TD>$offer5</TD>
                <TD>$offer6</TD>
                <TD>$birdYears</TD>
                <TD>$MLE</TD>
                <TD>$LLE</TD>
            </TR>";
            $offerAccepted = TRUE;
            $acceptedTeamDiscordID = $offeringTeam->discordID;
            $outcomeText = $name . " accepts the " . $offeringTeamName . " offer of a " . $offeryears . "-year deal worth a total of " . $offertotal . " million dollars.";
            $text .= $outcomeText . "<br>\n";
            $code .= "UPDATE `ibl_plr`
				SET `cy` = '0',
					`cy1` = '" . $offer1 . "',
					`cy2` = '" . $offer2 . "',
					`cy3` = '" . $offer3 . "',
					`cy4` = '" . $offer4 . "',
					`cy5` = '" . $offer5 . "',
					`cy6` = '" . $offer6 . "',
					`teamname` = '" . $offeringTeamName . "',
					`cyt` = '" . $offeryears . "',
					`tid` = $offeringTeam->teamID
				WHERE `name` = '" . $name . "'
				LIMIT 1;";
            if ($MLE == 1) {
                $code .= "UPDATE `ibl_team_info` SET `HasMLE` = '0' WHERE `team_name` = '" . $offeringTeamName . "' LIMIT 1;";
            }
            if ($LLE == 1) {
                $code .= "UPDATE `ibl_team_info` SET `HasLLE` = '0' WHERE `team_name` = '" . $offeringTeamName . "' LIMIT 1;";
            }
        } else {
            $outcomeText = "**REJECTED**\n\n";
            $offerAccepted = FALSE;
        }
    }

    $nameholder = $name;
    $lastPlayerIteratedOn = $name;
    $i++;
}

$discordText .= $offerText;
$discordText .= $outcomeText;
if ($offerAccepted) {
    $discordText .= " <@!$acceptedTeamDiscordID>";
}

$i = 0;
echo "<TR style=\"font-weight:bold\">
    <TD COLSPAN=8>ALL OFFERS MADE</TD>
    <TD>Bird</TD>
    <TD>MLE</TD>
    <TD>LLE</TD>
    <TD>RANDOM</TD>
</TR> ";

while ($i < $num) {
    $name = $db->sql_result($result, $i, "name");
    $perceivedvalue = $db->sql_result($result, $i, "perceivedvalue");
    $offeringTeamName = $db->sql_result($result, $i, "team");
    $offeringTeam = Team::initialize($db, $offeringTeamName);

    $offer1 = $db->sql_result($result, $i, "offer1");
    $offer2 = $db->sql_result($result, $i, "offer2");
    $offer3 = $db->sql_result($result, $i, "offer3");
    $offer4 = $db->sql_result($result, $i, "offer4");
    $offer5 = $db->sql_result($result, $i, "offer5");
    $offer6 = $db->sql_result($result, $i, "offer6");

    $birdYears = $db->sql_result($result, $i, "bird");
    $MLE = $db->sql_result($result, $i, "MLE");
    $LLE = $db->sql_result($result, $i, "LLE");
    $random = $db->sql_result($result, $i, "random");

    echo "<TR>
        <TD>$name</TD>
        <TD>$offeringTeamName</TD>
        <TD>$offer1</TD>
        <TD>$offer2</TD>
        <TD>$offer3</TD>
        <TD>$offer4</TD>
        <TD>$offer5</TD>
        <TD>$offer6</TD>
        <TD>$birdYears</TD>
        <TD>$MLE</TD>
        <TD>$LLE</TD>
        <TD>$random</TD>
        <TD>$perceivedvalue</TD>
    </TR>";
    $offeryears = 6;
    if ($offer6 == 0) {
        $offeryears = 5;
    }
    if ($offer5 == 0) {
        $offeryears = 4;
    }
    if ($offer4 == 0) {
        $offeryears = 3;
    }
    if ($offer3 == 0) {
        $offeryears = 2;
    }
    if ($offer2 == 0) {
        $offeryears = 1;
    }
    $offertotal = ($offer1 + $offer2 + $offer3 + $offer4 + $offer5 + $offer6) / 100;

    $exttext .= "The " . $offeringTeamName . " offered " . $name . " a " . $offeryears . "-year deal worth a total of " . $offertotal . " million dollars.<br>\n";
    $i++;
}

echo "</TABLE>
    <hr>
    <FORM>";

if ($val < 12) {
    echo "<h2 style=\"color:red\">AUTO-REJECTED OFFERS</H2>
        <TEXTAREA COLS=85 ROWS=20>$autoRejectedText</TEXTAREA>
        <hr>";
}

echo "  <h2 style=\"color:#7289da\">ALL REMAINING OFFERS IN DISCORD FORMAT (FOR <a href=\"https://discord.com/channels/666986450889474053/682990441641279531\">#live-sims</a>)</h2>
        <TEXTAREA style=\"font-size: 24px\" COLS=85 ROWS=20>$discordText</TEXTAREA>
        <hr>
        <h2>SQL QUERY BOX</h2>
        <TEXTAREA COLS=125 ROWS=20>$code</TEXTAREA>
        <hr>
        <h2>ACCEPTED OFFERS IN HTML FORMAT (FOR NEWS ARTICLE)</h2>
        <TEXTAREA COLS=125 ROWS=20>$text</TEXTAREA>
        <hr>
        <h2>ALL OFFERS IN HTML FORMAT (FOR NEWS ARTICLE EXTENDED TEXT)</h2>
        <TEXTAREA COLS=125 ROWS=20>$exttext</TEXTAREA>
    </FORM>
</HTML>";
