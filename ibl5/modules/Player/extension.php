<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

echo "<HTML><HEAD><TITLE>Contract Extension Offer Result</TITLE></HEAD><BODY>";

// Collect input data
$teamName = $_POST['teamName'];
$playerID = $_POST['playerID'];
$playerName = $_POST['playerName'];
$demandsYears = $_POST['demandsYears'];
$demandsTotal = $_POST['demandsTotal'];

// Build offer array
$offer = [
    'year1' => (int) $_POST['offerYear1'],
    'year2' => (int) $_POST['offerYear2'],
    'year3' => (int) $_POST['offerYear3'],
    'year4' => (int) $_POST['offerYear4'],
    'year5' => (int) $_POST['offerYear5']
];

// Build demands array
$demands = [
    'total' => $demandsTotal,
    'years' => $demandsYears
];

// Build extension data for processor
$extensionData = [
    'teamName' => $teamName,
    'playerID' => $playerID,
    'playerName' => $playerName,
    'offer' => $offer,
    'demands' => $demands
];

// Process extension using new architecture
$processor = new \Extension\ExtensionProcessor($db);
$result = $processor->processExtension($extensionData);

// Display results
if (!$result['success']) {
    // Validation error - offer not legal
    echo "<font color=#ff0000>" . $result['error'] . "<br>";
    echo "Your extension attempt was not legal and will not be recorded as an attempt. If you have not yet successfully extended a player this season, and have not yet made a successful offer this sim, you may press the \"Back\" Button on your browser to try again.</font>";
} else {
    // Legal offer was made
    echo "Message from the commissioner's office: <font color=#0000cc>Your offer is legal, and is therefore an extension attempt. Please note that you may make no further extension attempts until after the next sim.</font><br>";
    
    if ($result['accepted']) {
        // Offer was accepted
        echo "<table bgcolor=#cccccc><tr><td><b>Response from $playerName:</b> " . $result['message'] . "</td></tr></table>";
        echo "Note from the commissioner's office: <font color=#cc0000>Please note that you have used up your successful extension for this season and may not make any more extension attempts.</font><br>";
        
        if ($_SERVER['SERVER_NAME'] != "localhost") {
            echo "<center>An e-mail regarding this extension has been successfully sent to the commissioner's office. Thank you.</center>";
        }
    } else {
        // Offer was rejected
        echo "<table bgcolor=#cccccc><tr><td><b>Response from $playerName:</b> " . $result['message'] . "</td></tr></table>";
        echo "Note from the commissioner's office: <font color=#cc0000>Please note that you will be able to make another attempt next sim as you have not yet used up your successful extension for this season.</font><br>";
    }
}

echo "</BODY></HTML>";

