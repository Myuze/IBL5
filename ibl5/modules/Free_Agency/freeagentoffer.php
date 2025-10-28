<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

echo "<HTML><HEAD><TITLE>Free Agency Offer Entry</TITLE></HEAD><BODY>";

$Team_Name = $_POST['teamname'];
$Player_Name = $_POST['playername'];
$player_teamName = $_POST['player_teamname'];
$Demands_Years = $_POST['demyrs'];
$Demands_Total = $_POST['demtot'] * 100;
$amendedCapSpaceYear1 = $_POST['amendedCapSpaceYear1'];
$Cap_Space = $_POST['capnumber'];
$Cap_Space2 = $_POST['capnumber2'];
$Cap_Space3 = $_POST['capnumber3'];
$Cap_Space4 = $_POST['capnumber4'];
$Cap_Space5 = $_POST['capnumber5'];
$Cap_Space6 = $_POST['capnumber6'];
$Offer_1 = (int) $_POST['offeryear1'];
$Offer_2 = (int) $_POST['offeryear2'];
$Offer_3 = (int) $_POST['offeryear3'];
$Offer_4 = (int) $_POST['offeryear4'];
$Offer_5 = (int) $_POST['offeryear5'];
$Offer_6 = (int) $_POST['offeryear6'];
$MLE_Years = $_POST['MLEyrs'];
$Bird_Years = $_POST['bird'];
$Year1_Max = $_POST['max'];
$Minimum = $_POST['vetmin'];
$MLE = 0;
$LLE = 0;

$sharedFunctions = new Shared($db);

// Check if player being offered was previously signed to a team during this Free Agency period
$queryOfferedPlayer = "SELECT * FROM ibl_plr WHERE name = '$Player_Name';";
$resultOfferedPlayer = $db->sql_query($queryOfferedPlayer);
$currentContractYearOfOfferedPlayer = $db->sql_result($resultOfferedPlayer, 0, "cy");
$year1ContractOfOfferedPlayer = $db->sql_result($resultOfferedPlayer, 0, "cy1");
if ($currentContractYearOfOfferedPlayer == 0 AND ($year1ContractOfOfferedPlayer != "0")) {
    echo "Sorry, this player was previously signed to a team this Free Agency period.<p>
        Please <a href=\"/ibl5/modules.php?name=Free_Agency\">click here to return to the Free Agency main page</a>.";
    exit();
}

if ($MLE_Years == 8) {
    $Offer_1 = $Minimum;
    $Offer_2 = 0;
    $Offer_3 = 0;
    $Offer_4 = 0;
    $Offer_5 = 0;
    $Offer_6 = 0;
}

if ($MLE_Years == 7) {
    $Offer_1 = 145;
    $Offer_2 = 0;
    $Offer_3 = 0;
    $Offer_4 = 0;
    $Offer_5 = 0;
    $Offer_6 = 0;
    $LLE = 1;
}

if ($MLE_Years == 6) {
    $Offer_1 = 450;
    $Offer_2 = 495;
    $Offer_3 = 540;
    $Offer_4 = 585;
    $Offer_5 = 630;
    $Offer_6 = 675;
    $MLE = 1;
}

if ($MLE_Years == 5) {
    $Offer_1 = 450;
    $Offer_2 = 495;
    $Offer_3 = 540;
    $Offer_4 = 585;
    $Offer_5 = 630;
    $Offer_6 = 0;
    $MLE = 1;
}

if ($MLE_Years == 4) {
    $Offer_1 = 450;
    $Offer_2 = 495;
    $Offer_3 = 540;
    $Offer_4 = 585;
    $Offer_5 = 0;
    $Offer_6 = 0;
    $MLE = 1;
}

if ($MLE_Years == 3) {
    $Offer_1 = 450;
    $Offer_2 = 495;
    $Offer_3 = 540;
    $Offer_4 = 0;
    $Offer_5 = 0;
    $Offer_6 = 0;
    $MLE = 1;
}

if ($MLE_Years == 2) {
    $Offer_1 = 450;
    $Offer_2 = 495;
    $Offer_3 = 0;
    $Offer_4 = 0;
    $Offer_5 = 0;
    $Offer_6 = 0;
    $MLE = 1;
}

if ($MLE_Years == 1) {
    $Offer_1 = 450;
    $Offer_2 = 0;
    $Offer_3 = 0;
    $Offer_4 = 0;
    $Offer_5 = 0;
    $Offer_6 = 0;
    $MLE = 1;
}

$yrsinoffer = 6;
if ($Offer_6 == 0) {
    $yrsinoffer = 5;
}
if ($Offer_5 == 0) {
    $yrsinoffer = 4;
}
if ($Offer_4 == 0) {
    $yrsinoffer = 3;
}
if ($Offer_3 == 0) {
    $yrsinoffer = 2;
}
if ($Offer_2 == 0) {
    $yrsinoffer = 1;
}

$Offer_Avg = ($Offer_1 + $Offer_2 + $Offer_3 + $Offer_4 + $Offer_5 + $Offer_6) / $yrsinoffer;

// LOOP TO GET MILLIONS COMMITTED AT POSITION

$queryposition = "SELECT * FROM ibl_plr WHERE `name` ='$Player_Name'";
$resultposition = $db->sql_query($queryposition);
$player_pos = $db->sql_result($resultposition, 0, "pos");

$querymillions = "SELECT * FROM ibl_plr WHERE `teamname`='$Team_Name' AND `pos`='$player_pos' AND `name`!='$Player_Name'";
$resultmillions = $db->sql_query($querymillions);
$nummillions = $db->sql_numrows($resultmillions);

$tf_millions = 0;

$i = 0;
while ($i < $nummillions) {
    $millionscy = $db->sql_result($resultmillions, $i, "cy");
    $millionscy1 = $db->sql_result($resultmillions, $i, "cy1");
    $millionscy2 = $db->sql_result($resultmillions, $i, "cy2");
    $millionscy3 = $db->sql_result($resultmillions, $i, "cy3");
    $millionscy4 = $db->sql_result($resultmillions, $i, "cy4");
    $millionscy5 = $db->sql_result($resultmillions, $i, "cy5");
    $millionscy6 = $db->sql_result($resultmillions, $i, "cy6");

// LOOK AT SALARY COMMITTED NEXT YEAR, NOT THIS YEAR

    if ($millionscy == 0) {
        $tf_millions += $millionscy1;
    }
    if ($millionscy == 1) {
        $tf_millions += $millionscy2;
    }
    if ($millionscy == 2) {
        $tf_millions += $millionscy3;
    }
    if ($millionscy == 3) {
        $tf_millions += $millionscy4;
    }
    if ($millionscy == 4) {
        $tf_millions += $millionscy5;
    }
    if ($millionscy == 5) {
        $tf_millions += $millionscy6;
    }

    $i++;
}

// END LOOPS

// ==== GET MOD FACTORS

if ($tf_millions > 2000) {
    $tf_millions = 2000;
}

$query1 = "SELECT * FROM ibl_team_info WHERE team_name = '$Team_Name'";
$result1 = $db->sql_query($query1);

$tf_wins = $db->sql_result($result1, 0, "Contract_Wins");
$tf_loss = $db->sql_result($result1, 0, "Contract_Losses");
$tf_trdw = $db->sql_result($result1, 0, "Contract_AvgW");
$tf_trdl = $db->sql_result($result1, 0, "Contract_AvgL");

$queryteam = "SELECT * FROM ibl_plr WHERE name = '$Player_Name'";
$resultteam = $db->sql_query($queryteam);

$player_team = $db->sql_result($resultteam, 0, "teamname");
$player_winner = $db->sql_result($resultteam, 0, "winner");
$player_tradition = $db->sql_result($resultteam, 0, "tradition");
$player_security = $db->sql_result($resultteam, 0, "security");
$player_loyalty = $db->sql_result($resultteam, 0, "loyalty");
$player_playingtime = $db->sql_result($resultteam, 0, "playingTime");

$seasonWinLossDifferential = $tf_wins - $tf_loss;
$traditionWinLossDifferential = $tf_trdw - $tf_trdl;

$factorPlayForWinner = (0.000153 * ($seasonWinLossDifferential) * ($player_winner - 1));
$factorTradition = (0.000153 * ($traditionWinLossDifferential) * ($player_tradition - 1));

if ($Team_Name == $player_team) {
    $factorLoyalty = (.025 * ($player_loyalty - 1));
} else {
    $factorLoyalty = -(.025 * ($player_loyalty - 1));
}

$factorSecurity = (.01 * ($yrsinoffer - 1) - 0.025) * ($player_security - 1);
$factorPlayingTime = -(.0025 * $tf_millions / 100 - 0.025) * ($player_playingtime - 1);

$modifier = 1 + $factorPlayForWinner + $factorTradition + $factorLoyalty + $factorSecurity + $factorPlayingTime;
$factorPlayForWinner *= 100;
$factorTradition *= 100;
$factorLoyalty *= 100;
$factorSecurity *= 100;
$factorPlayingTime *= 100;

$random = (rand(5, -5));
$modrandom = (100 + $random) / 100;

$Demands_Average = $Demands_Total / $Demands_Years;

$perceivedvalue = $Offer_Avg * $modifier * $modrandom;

// echo "
//     Season Winner Bonus: $factorPlayForWinner %<br>
//     Season Wins: $tf_wins<br>
//     Season Losses: $tf_loss<br>
//     Season Win/Loss Differential: $seasonWinLossDifferential<br>
//     <br>
//     Tradition Bonus: $factorTradition %<br>
//     Tradition Wins: $tf_trdw<br>
//     Tradition Losses: $tf_trdl<br>
//     Tradition Win/Loss Differential: $traditionWinLossDifferential<br>
//     <br>
//     Loyalty Bonus: $factorLoyalty %<br>
//     <br>
//     Security Bonus: $factorSecurity %<br>
//     Years Offered: $yrsinoffer<br>
//     <br>
//     Play Time Bonus: $factorPlayingTime %<br>
//     Money Commited: $tf_millions<br>
//     <br>
//     Random: $modrandom%<br>
//     <br>
//     Demands Years: $Demands_Years<br>
//     Demands Total: $Demands_Total<br>
//     Demands Average: $Demands_Average<br>
//     <br>
//     Perceived Value: $perceivedvalue<br>";

$nooffer = 0;

// ==== CHECK FOR ILLEGAL OFFERS WITH ZERO CONTRACT AMOUNTS ====

// ====== (ADD HANDLING FOR MLE, LLE, VETMIN) ======

if ($Offer_1 == 0) {
    echo "Sorry, you must enter an amount greater than zero in the first year of a free agency offer. Your offer in Year 1 was zero, so this offer is not valid.<br>";
    $nooffer = 1;
}

if ($Offer_1 < $Minimum) {
    echo "Sorry, you must enter an amount greater than the Veteran's Minimum in the first year of a free agency offer.<br>
        Your offer in Year 1 was <b>$Offer_1</b>, but should be at least <b>$Minimum</b>.<br>";
    $nooffer = 1;
}

// ===== BIRD RIGHTS TREATMENT

if ($player_team != $Team_Name) {
    $Bird_Years = 0;
}

if ($Bird_Years > 2) {
    $Offer_max_increase = round($Offer_1 * 0.125, 0);
} else {
    $Offer_max_increase = round($Offer_1 * 0.1, 0);
}

// ==== CHECK FOR ILLEGAL OFFERS THAT ARE OVER THE SALARY CAP

$Hard_Cap_Space = $amendedCapSpaceYear1 + 2000;
$Hard_Cap_Space2 = $Cap_Space2 + 2000;
$Hard_Cap_Space3 = $Cap_Space3 + 2000;
$Hard_Cap_Space4 = $Cap_Space4 + 2000;
$Hard_Cap_Space5 = $Cap_Space5 + 2000;
$Hard_Cap_Space6 = $Cap_Space6 + 2000;

if ($Offer_1 > $Hard_Cap_Space) {
    echo "Sorry, you do not have sufficient cap space under the hard cap to make the offer.  You offered $Offer_1 in the first year of the contract, which is more than $Hard_Cap_Space, the amount of hard cap space you have available.<br>";
    $nooffer = 1;
}
if ($nooffer == 0) {
    if ($Bird_Years < 3 AND $Offer_1 > $amendedCapSpaceYear1 AND $MLE_Years == 0) {
        echo "Sorry, you do not have sufficient cap space under the soft cap to make the offer.  You offered $Offer_1 in the first year of the contract, which is more than $amendedCapSpaceYear1, the amount of soft cap space you have available.<br>";
        $nooffer = 1;
    }
}


// ==== CHECK FOR OFFERS OVER MAX

if ($nooffer == 0) {
    if ($Offer_1 > $Year1_Max) {
        echo "Sorry, you tried to offer a contract larger than the maximum allowed for this player based on their years of service.  The maximum you are allowed to offer this player is $Year1_Max in the first year of their contract.<br>";
        $nooffer = 1;
    }
}
// ==== CHECK FOR ILLEGAL RAISES

if ($nooffer == 0) {
    if ($Offer_2 > $Offer_1 + $Offer_max_increase) {
        $legaloffer = $Offer_1 + $Offer_max_increase;
        echo "Sorry, you tried to offer a larger raise than is permitted.  Your first year offer was $Offer_1 which means the maximum raise allowed each year is $Offer_max_increase.  Your offer in Year 2 was $Offer_2, which is more than your Year 1 offer, $Offer_1, plus the max increase of $Offer_max_increase.  Given your offer in Year 1, the most you can offer in Year 2 is $legaloffer.<br>";
        $nooffer = 1;
    }
}
if ($nooffer == 0) {
    if ($Offer_3 > $Offer_2 + $Offer_max_increase) {
        $legaloffer = $Offer_2 + $Offer_max_increase;
        echo "Sorry, you tried to offer a larger raise than is permitted.  Your first year offer was $Offer_1 which means the maximum raise allowed each year is $Offer_max_increase.  Your offer in Year 3 was $Offer_3, which is more than your Year 2 offer, $Offer_2, plus the max increase of $Offer_max_increase.  Given your offer in Year 2, the most you can offer in Year 3 is $legaloffer.<br>";
        $nooffer = 1;
    }
}
if ($nooffer == 0) {
    if ($Offer_4 > $Offer_3 + $Offer_max_increase) {
        $legaloffer = $Offer_3 + $Offer_max_increase;
        echo "Sorry, you tried to offer a larger raise than is permitted.  Your first year offer was $Offer_1 which means the maximum raise allowed each year is $Offer_max_increase.  Your offer in Year 4 was $Offer_4, which is more than your Year 3 offer, $Offer_3, plus the max increase of $Offer_max_increase.  Given your offer in Year 3, the most you can offer in Year 4 is $legaloffer.<br>";
        $nooffer = 1;
    }
}
if ($nooffer == 0) {
    if ($Offer_5 > $Offer_4 + $Offer_max_increase) {
        $legaloffer = $Offer_4 + $Offer_max_increase;
        echo "Sorry, you tried to offer a larger raise than is permitted.  Your first year offer was $Offer_1 which means the maximum raise allowed each year is $Offer_max_increase.  Your offer in Year 5 was $Offer_5, which is more than your Year 4 offer, $Offer_4, plus the max increase of $Offer_max_increase.  Given your offer in Year 4, the most you can offer in Year 5 is $legaloffer.<br>";
        $nooffer = 1;
    }
}
if ($nooffer == 0) {
    if ($Offer_6 > $Offer_5 + $Offer_max_increase) {
        $legaloffer = $Offer_5 + $Offer_max_increase;
        echo "Sorry, you tried to offer a larger raise than is permitted.  Your first year offer was $Offer_1 which means the maximum raise allowed each year is $Offer_max_increase.  Your offer in Year 5 was $Offer_5, which is more than your Year 4 offer, $Offer_4, plus the max increase of $Offer_max_increase.  Given your offer in Year 5, the most you can offer in Year 5 is $legaloffer.<br>";
        $nooffer = 1;
    }
}
// ==== CHECK FOR ILLEGAL LOWERING OF SALARY
if ($nooffer == 0) {
    if ($Offer_2 < $Offer_1) {
        if ($Offer_2 == 0) {
        } else {
            echo "Sorry, you cannot decrease salary in later years of a contract.  You offered $Offer_2 in the second year, which is less than you offered in the first year, $Offer_1.<br>";
            $nooffer = 1;
        }
    }

}
if ($nooffer == 0) {
    if (($Offer_3 < $Offer_2) and ($Offer_2 > 0)) {
        if ($Offer_3 == 0) {
        } else {
            echo "Sorry, you cannot decrease salary in later years of a contract.  You offered $Offer_3 in the third year, which is less than you offered in the second year, $Offer_2.<br>";
            $nooffer = 1;
        }
    }
}
if ($nooffer == 0) {
    if ($Offer_4 < $Offer_3) {
        if ($Offer_4 == 0) {
        } else {
            echo "Sorry, you cannot decrease salary in later years of a contract.  You offered $Offer_4 in the fourth year, which is less than you offered in the third year, $Offer_3.<br>";
            $nooffer = 1;
        }
    }
}
if ($nooffer == 0) {
    if ($Offer_5 < $Offer_4) {
        if ($Offer_5 == 0) {
        } else {
            echo "Sorry, you cannot decrease salary in later years of a contract.  You offered $Offer_5 in the fifth year, which is less than you offered in the fourth year, $Offer_4.<br>";
            $nooffer = 1;
        }
    }
}
if ($nooffer == 0) {
    if ($Offer_6 < $Offer_5) {
        if ($Offer_6 == 0) {
        } else {
            echo "Sorry, you cannot decrease salary in later years of a contract.  You offered $Offer_6 in the sixth year, which is less than you offered in the fifth year, $Offer_5.<br>";
            $nooffer = 1;
        }
    }
}

// ==== IF OFFER IS LEGIT, PROCESS OFFER ====

if ($nooffer == 0) {
    if ($Offer_2 == "" OR $Offer_2 == NULL) {
        $Offer_2 = 0;
    }
    if ($Offer_3 == "" OR $Offer_3 == NULL) {
        $Offer_3 = 0;
    }
    if ($Offer_4 == "" OR $Offer_4 == NULL) {
        $Offer_4 = 0;
    }
    if ($Offer_5 == "" OR $Offer_5 == NULL) {
        $Offer_5 = 0;
    }
    if ($Offer_6 == "" OR $Offer_6 == NULL) {
        $Offer_6 = 0;
    }

    $querydrop = "DELETE FROM `ibl_fa_offers` WHERE `name` = '$Player_Name' AND `team` = '$Team_Name' LIMIT 1";
    $resultdrop = $db->sql_query($querydrop);

    $querychunk = "INSERT INTO `ibl_fa_offers` 
    (`name`, 
     `team`, 
     `offer1`, 
     `offer2`, 
     `offer3`, 
     `offer4`, 
     `offer5`, 
     `offer6`, 
     `modifier`, 
     `random`, 
     `perceivedvalue`, 
     `mle`, 
     `lle`) 
        VALUES
    ( '$Player_Name', 
      '$Team_Name', 
      '$Offer_1', 
      '$Offer_2', 
      '$Offer_3', 
      '$Offer_4', 
      '$Offer_5', 
      '$Offer_6', 
      '$modifier', 
      '$random', 
      '$perceivedvalue', 
      '$MLE', 
      '$LLE' )";

    $resultchunk = $db->sql_query($querychunk);

    if ($resultchunk) {
        $playerTeamDiscordID = Discord::getDiscordIDFromTeamname($db, $player_teamName);
        if ($Team_Name == $player_teamName) {
            $discordMessage = "Free agent **$Player_Name** has been offered a contract to _stay_ with the **$player_teamName**.
_**$player_teamName** GM <@!$playerTeamDiscordID> could not be reached for comment._";
        } else {
            $discordMessage = "Free agent **$Player_Name** has been offered a contract to _leave_ the **$player_teamName**.
_**$player_teamName** GM <@!$playerTeamDiscordID> could not be reached for comment._";
        }

        if ($Offer_1 > 145) {
            $season = new Season($db);
            if ($season->freeAgencyNotificationsState == "On") {
                Discord::postToChannel('#free-agency', $discordMessage);
            }
        }

        echo "Your offer is legal. It should be immediately reflected in your Free Agency module.<br>
            Please <a href=\"/ibl5/modules.php?name=Free_Agency\">click here to return to the Free Agency module</a>.";
    }

} else {
    echo "<font color=#ff0000>Your offer was not legal and will not be recorded.<br>
        Please go \"Back\" in your browser to try again.</font>";
}
