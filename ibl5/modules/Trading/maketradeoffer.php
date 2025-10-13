<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

// Prepare trade data from POST
$tradeData = [
    'offeringTeam' => $_POST['offeringTeam'],
    'listeningTeam' => $_POST['listeningTeam'],
    'switchCounter' => $_POST['switchCounter'],
    'fieldsCounter' => $_POST['fieldsCounter'] + 1,
    'userSendsCash' => [],
    'partnerSendsCash' => [],
    'check' => [],
    'contract' => [],
    'index' => [],
    'type' => []
];

// Extract cash data
$i = 0;
while ($i < 7) {
    $tradeData['userSendsCash'][$i] = (int) ($_POST['userSendsCash' . $i] ?? 0);
    $tradeData['partnerSendsCash'][$i] = (int) ($_POST['partnerSendsCash' . $i] ?? 0);
    $i++;
}

// Extract form field data
for ($j = 0; $j < $tradeData['fieldsCounter']; $j++) {
    $tradeData['check'][$j] = $_POST['check' . $j] ?? null;
    $tradeData['contract'][$j] = $_POST['contract' . $j] ?? 0;
    $tradeData['index'][$j] = $_POST['index' . $j] ?? 0;
    $tradeData['type'][$j] = $_POST['type' . $j] ?? 0;
}

// Create trade offer using new class
$tradeOffer = new Trading_TradeOffer($db);
$result = $tradeOffer->createTradeOffer($tradeData);

// Display trade cap details
if (isset($result['capData'])) {
    echo "Your Payroll this season, if this trade is accepted: {$result['capData']['userPostTradeCapTotal']}<br>";
    echo "Their Payroll this season, if this trade is accepted: {$result['capData']['partnerPostTradeCapTotal']}<p>";
}

// Display any errors and exit if trade creation failed
if (!$result['success']) {
    if (isset($result['error'])) {
        echo $result['error'];
    } elseif (isset($result['errors'])) {
        foreach ($result['errors'] as $error) {
            echo $error . "<br>";
        }
    }
    echo "<p><a href='javascript:history.back()'>Please go back and adjust your trade proposal.</a>";
    exit;
}

echo $result['tradeText'] ?? '';

echo "<p>";
echo "Trade Offer Sent!<br>
    <a href='/ibl5/modules.php?name=Trading&op=reviewtrade'>Back to Trade Review</a>";
?>
