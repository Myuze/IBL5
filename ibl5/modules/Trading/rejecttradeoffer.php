<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$offer_id = $_POST['offer'];
$teamRejecting = $_POST['teamRejecting'];
$teamReceiving = $_POST['teamReceiving'];

$queryClearInfo = "DELETE FROM ibl_trade_info WHERE `tradeOfferID` = '$offer_id'";
$resultClearInfo = $db->sql_query($queryClearInfo);
$queryClearCash = "DELETE FROM ibl_trade_cash WHERE `tradeOfferID` = '$offer_id'";
$resultClearCash = $db->sql_query($queryClearCash);

$rejectingUserDiscordID = Discord::getDiscordIDFromTeamname($db, $teamRejecting);
$receivingUserDiscordID = Discord::getDiscordIDFromTeamname($db, $teamReceiving);
$discordDMmessage = 'Sorry, trade proposal declined by <@!' . $rejectingUserDiscordID . '>.

Go here to make another offer: http://www.iblhoops.net/ibl5/modules.php?name=Trading&op=reviewtrade';
$arrayContent = array(
        'message' => $discordDMmessage,
        'receivingUserDiscordID' => $receivingUserDiscordID,);

echo "<p>";
// $response = Discord::sendCurlPOST('http://localhost:50000/discordDM', $arrayContent);

?>

<HTML><HEAD><TITLE>Trade Offer Processing</TITLE>
<meta http-equiv="refresh" content="0;url='/ibl5/modules.php?name=Trading&op=reviewtrade'">
</HEAD><BODY>
Trade Offer Rejected. Redirecting you to trade review page...
</BODY></HTML>
